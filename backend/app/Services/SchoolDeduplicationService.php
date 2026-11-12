<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class SchoolDeduplicationService
{
    public function plan(): array
    {
        $pairs = [];
        $identity = app(SchoolIdentityService::class);
        foreach (config('school_identity.merges', []) as $oldId => $alias) {
            $old = DB::table('schools')->find($oldId);
            if (! $old) {
                continue;
            }
            $target = DB::table('schools')->find($alias['target']);
            if (! $target || $old->code !== $alias['code'] || $identity->key($old->name) !== $identity->key($target->name)) {
                throw new RuntimeException('Dữ liệu không còn khớp ánh xạ đã đối chiếu: '.$oldId);
            }
            if ($old->parent_school_id || $target->parent_school_id || $old->campus_type !== 'MAIN' || $target->campus_type !== 'MAIN') {
                throw new RuntimeException('Không tự gộp cơ sở trực thuộc: '.$oldId);
            }
            $pairs[] = ['old_id' => $oldId, 'target_id' => $target->id, 'name' => $target->name];
        }

        return $pairs;
    }

    /** Include legacy references without foreign keys, as well as declared FKs. */
    private function references(): array
    {
        $references = [];
        foreach (Schema::getTables() as $table) {
            $name = $table['name'];
            foreach (Schema::getColumnListing($name) as $column) {
                if (in_array($column, ['school_id', 'parent_school_id'], true)) {
                    $references[$name.'|'.$column] = [$name, $column];
                }
            }
            foreach (Schema::getForeignKeys($name) as $foreign) {
                if ($foreign['foreign_table'] === 'schools') {
                    if (count($foreign['columns']) !== 1 || $foreign['foreign_columns'] !== ['id']) {
                        throw new RuntimeException('Cần xử lý khóa ngoại riêng tại '.$name);
                    }
                    $column = $foreign['columns'][0];
                    $references[$name.'|'.$column] = [$name, $column];
                }
            }
        }

        return array_values($references);
    }

    public function apply(): array
    {
        $references = $this->references();

        return DB::transaction(function () use ($references): array {
            // Keep the snapshot and the actual merge consistent under concurrent edits.
            $locked = DB::table('schools')->orderBy('id')->lockForUpdate()->get();
            $pairs = $this->plan();
            if (! $pairs) {
                return ['merged' => 0, 'backup' => null, 'changes' => []];
            }
            $tables = array_unique(['schools', ...array_column($references, 0)]);
            $backup = ['mapping' => $pairs, 'tables' => [], 'binary_encoding' => ['schools.geom' => 'hex']];
            foreach ($tables as $table) {
                $backup['tables'][$table] = DB::table($table)->lockForUpdate()->get()->map(function ($row) use ($table) {
                    if ($table === 'schools' && isset($row->geom)) {
                        $row->geom = bin2hex($row->geom);
                    }

                    return $row;
                })->all();
            }
            $path = storage_path('app/import-backups/school-dedup-'.now()->format('Ymd-His-u').'.json');
            File::ensureDirectoryExists(dirname($path));
            $json = json_encode($backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            if (File::put($path, $json) !== strlen($json)) {
                throw new RuntimeException('Không tạo được bản sao trước khi gộp.');
            }
            $changes = [];
            foreach ($pairs as $pair) {
                foreach ($references as [$table, $column]) {
                    $query = DB::table($table)->where($column, $pair['old_id']);
                    if (! $query->exists()) {
                        continue;
                    }
                    // Prefer the complete newer catalog. Do not reintroduce seed
                    // majors, education levels, or add their quotas to source totals.
                    if (in_array($table, ['school_training_major', 'school_education_levels'], true)
                        && DB::table($table)->where($column, $pair['target_id'])->exists()) {
                        $changes[] = ['table' => $table, 'old_id' => $pair['old_id'], 'archived' => $query->delete()];
                    } else {
                        // Unique conflicts (e.g. two accounts/capacity declarations)
                        // abort the entire transaction; never silently delete them.
                        $changes[] = ['table' => $table, 'old_id' => $pair['old_id'], 'moved' => $query->update([$column => $pair['target_id']])];
                    }
                }
                foreach ($references as [$table, $column]) {
                    if (DB::table($table)->where($column, $pair['old_id'])->exists()) {
                        throw new RuntimeException('Còn liên kết chưa chuyển tại '.$table);
                    }
                }
                DB::table('schools')->where('id', $pair['old_id'])->delete();
            }
            $expected = $locked->count() - count($pairs);
            if (DB::table('schools')->count() !== $expected) {
                throw new RuntimeException('Số lượng trường sau gộp không khớp.');
            }

            return ['merged' => count($pairs), 'backup' => $path, 'changes' => $changes];
        });
    }
}
