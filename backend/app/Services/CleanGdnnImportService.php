<?php

namespace App\Services;

use App\Models\SchoolSourceRecord;
use App\Models\SchoolTrainingMajor;
use App\Models\TrainingMajor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

class CleanGdnnImportService
{
    public function plan(array $clean, array $admissions): array
    {
        if (! is_array($clean['institutions'] ?? null) || ! is_array($clean['centers'] ?? null)) {
            throw new RuntimeException('Dữ liệu sạch phải có danh sách institutions và centers.');
        }
        $schools = DB::table('schools')->get();
        $byId = $schools->keyBy('id');
        // School IDs generated from imported rows end with a random three-digit
        // suffix. The same reviewed school can therefore have a different full
        // ID on local and VPS databases. Keep the stable slug portion as a
        // secondary identity, but only accept it when it resolves uniquely.
        $byStableId = $schools->groupBy(fn ($school) => $this->stableSchoolIdKey($school->id));
        $byName = $schools->groupBy(fn ($school) => $this->schoolKey($school->name));
        $byExactName = $schools->groupBy(fn ($school) => $this->exactSchoolKey($school->name));
        $duplicateCodes = collect($clean['institutions'])->filter(fn ($item) => filled($item['code'] ?? null))->groupBy('code')->filter(fn ($items) => $items->count() > 1)->keys()->all();
        $records = [];
        $skipped = [];
        foreach ([['training_report', $clean['institutions']], ['center_contact', $clean['centers']], ['admission_report', $admissions['institutions'] ?? []]] as [$type, $items]) {
            foreach ($items as $index => $item) {
                $sourceName = $item['name'] ?? $item['source_name'] ?? '';
                $school = null;
                $matchStatus = $item['match_status'] ?? 'unmatched';
                $trustedId = $type === 'admission_report' ? ($item['school_id'] ?? null)
                    : ($matchStatus === 'matched' ? ($item['project_school_id'] ?? null) : null);
                if ($trustedId && $byId->has($trustedId)) {
                    $candidate = $byId->get($trustedId);
                    if ($type !== 'admission_report' || empty($item['school_code']) || $candidate->code === $item['school_code']) {
                        $school = $candidate;
                    }
                }
                if (! $school && $trustedId) {
                    $stableMatches = $byStableId->get($this->stableSchoolIdKey($trustedId), collect());
                    if ($stableMatches->count() === 1) {
                        // The reviewed source ID is authoritative here. Do not
                        // require the legacy source code: existing VPS rows can
                        // legitimately retain a different internal code.
                        $school = $stableMatches->first();
                    }
                }
                // Never use candidate_project_school_id or fuzzy/substring matches.
                if (! $school && $matchStatus !== 'needs_review') {
                    $matches = $byExactName->get($this->exactSchoolKey($sourceName), collect());
                    if ($matches->isEmpty()) {
                        $matches = $byName->get($this->schoolKey($sourceName), collect());
                    }
                    if ($type === 'center_contact') {
                        $matches = $matches->filter(fn ($s) => ! $s->parent_school_id && in_array($s->campus_type, [null, 'MAIN'], true));
                    }
                    if ($matches->count() === 1) {
                        $school = $matches->first();
                    }
                }
                if ($school && $type === 'center_contact' && ($school->parent_school_id || ! in_array($school->campus_type, [null, 'MAIN'], true))) {
                    $school = null;
                }
                if (! $school) {
                    $skipped[] = ['record_type' => $type, 'source_name' => $sourceName, 'reason' => $matchStatus === 'needs_review' ? 'Cần xác minh danh tính cơ sở' : 'Không ghép được duy nhất với CSDL'];

                    continue;
                }
                // Director numbers must never enter imported records or public APIs.
                unset($item['director_phone'], $item['source_director_phone'], $item['phone_field_note']);
                if ($type === 'training_report') {
                    if (in_array($item['code'] ?? null, $duplicateCodes, true)) {
                        unset($item['code'], $item['source_code']);
                    }
                    foreach (['current_scale', 'planned_2026'] as $period) {
                        if (! empty($item['summary'][$period]['source_total_row']['issues'])) {
                            unset($item['summary'][$period]['source_total_row']);
                        }
                    }
                }
                $key = $type.':'.hash('sha256', (string) ($item['source_ref'] ?? $item['school_id'] ?? $index));
                $payload = $item + ['source_metadata' => $type === 'admission_report'
                    ? ['source_file' => $admissions['source_file'] ?? null, 'display_period' => $admissions['display_period'] ?? null]
                    : ['generated_date' => $clean['generated_date'] ?? null, 'sources' => $clean['sources'] ?? []]];
                $hash = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                $existing = SchoolSourceRecord::where('source_key', $key)->first();
                $records[] = [
                    'source_key' => $key, 'record_type' => $type, 'source_name' => $sourceName,
                    'school_id' => $school?->id, 'match_status' => $school ? 'matched' : ($matchStatus === 'needs_review' ? 'needs_review' : 'unmatched'),
                    'content_hash' => $hash, 'payload' => $payload,
                    'changed' => ! $existing || $existing->content_hash !== $hash || $existing->school_id !== $school?->id,
                    'address_changed' => $school && $type === 'center_contact' && filled($item['address'] ?? null) && $school->address !== $item['address'],
                ];
            }
        }
        $catalog = [];
        $assignments = [];
        $majorIndex = TrainingMajor::all()->groupBy(fn ($major) => $this->majorKey($major->name));
        foreach ($records as $record) {
            if ($record['record_type'] !== 'training_report' || ! $record['school_id']) {
                continue;
            }
            foreach ($record['payload']['programs'] ?? [] as $program) {
                $name = trim((string) ($program['name'] ?? ''));
                if ($name === '') {
                    throw new RuntimeException('Thiếu tên ngành tại '.$record['source_name']);
                }
                $key = $this->majorKey($name);
                $matches = $majorIndex->get($key, collect());
                if ($matches->count() > 1) {
                    throw new RuntimeException('Danh mục có nhiều ngành cùng tên chuẩn hóa: '.$name);
                }
                $catalog[$key] ??= ['name' => $name, 'id' => $matches->first()?->id, 'codes' => []];
                $catalog[$key]['codes'] = array_values(array_unique([...$catalog[$key]['codes'], ...($program['source_codes'] ?? [])]));
                $levels = [];
                foreach (['cao_dang', 'trung_cap'] as $level) {
                    if (! empty($program['codes_by_level'][$level]) || ($program['current_scale'][$level] ?? 0) > 0 || ($program['planned_2026'][$level] ?? 0) > 0) {
                        $levels[] = $level;
                    }
                }
                // "Sơ cấp và khác" cannot be silently assigned to one specific level.
                foreach (array_unique($levels ?: ['chua_xac_dinh']) as $level) {
                    $assignments[$record['school_id'].'|'.$key.'|'.$level] = ['school_id' => $record['school_id'], 'major_key' => $key, 'degree_level' => $level];
                }
            }
        }
        $codeNames = [];
        foreach ($catalog as $key => $item) {
            foreach ($item['codes'] as $code) {
                $codeNames[$code][$key] = true;
            }
        }
        foreach ($catalog as $key => &$item) {
            $code = count($item['codes']) === 1 ? (string) $item['codes'][0] : null;
            $taken = $code ? TrainingMajor::where('code', $code)->where('id', '!=', $item['id'] ?? 0)->exists() : false;
            $item['code'] = $code && preg_match('/^\d{7,8}$/', $code) && count($codeNames[$code]) === 1 && ! $taken ? $code : null;
        }
        unset($item);
        $newAssignments = collect($assignments)->filter(function ($link) use ($catalog): bool {
            $id = $catalog[$link['major_key']]['id'];

            return ! $id || ! SchoolTrainingMajor::where('school_id', $link['school_id'])->where('training_major_id', $id)->where('degree_level', $link['degree_level'])->exists();
        })->count();

        return [
            'records' => $records, 'catalog' => $catalog, 'assignments' => array_values($assignments), 'skipped' => $skipped,
            'counts' => [
                'records' => count($records), 'matched' => collect($records)->where('match_status', 'matched')->count(),
                'skipped_uncertain' => count($skipped),
                'reports_to_write' => collect($records)->where('changed', true)->count(),
                'addresses_to_update' => collect($records)->where('address_changed', true)->count(),
                'new_majors' => collect($catalog)->whereNull('id')->count(), 'new_assignments' => $newAssignments,
            ],
        ];
    }

    public function apply(array $plan): string
    {
        $backupPath = storage_path('app/import-backups/clean-gdnn-'.now()->format('Ymd-His-u').'.json');
        File::ensureDirectoryExists(dirname($backupPath));
        $backup = [];
        foreach (['schools', 'training_majors', 'school_training_major', 'school_source_records'] as $table) {
            $backup[$table] = DB::table($table)->get();
        }
        // MySQL returns geometry as binary bytes; hex preserves them exactly in JSON.
        foreach ($backup['schools'] as $school) {
            if (isset($school->geom) && is_string($school->geom)) {
                $school->geom = bin2hex($school->geom);
            }
        }
        $backup['binary_encoding'] = ['schools.geom' => 'hex'];
        $encoded = json_encode($backup, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        if (File::put($backupPath, $encoded) !== strlen($encoded)) {
            throw new RuntimeException('Không tạo được bản sao đầy đủ trước khi ghi.');
        }
        DB::transaction(function () use ($plan): void {
            foreach ($plan['records'] as $record) {
                if ($record['changed']) {
                    SchoolSourceRecord::updateOrCreate(['source_key' => $record['source_key']], collect($record)->except(['changed', 'address_changed'])->all());
                }
                if ($record['address_changed']) {
                    DB::table('schools')->where('id', $record['school_id'])->update(['address' => $record['payload']['address'], 'updated_at' => now()]);
                }
            }
            $ids = [];
            foreach ($plan['catalog'] as $key => $item) {
                $major = $item['id'] ? TrainingMajor::findOrFail($item['id']) : TrainingMajor::create(['name' => $item['name'], 'code' => $item['code'], 'is_active' => true]);
                if (! $major->code && $item['code']) {
                    $major->update(['code' => $item['code']]);
                }
                $ids[$key] = $major->id;
            }
            foreach ($plan['assignments'] as $link) {
                SchoolTrainingMajor::firstOrCreate([
                    'school_id' => $link['school_id'], 'training_major_id' => $ids[$link['major_key']], 'degree_level' => $link['degree_level'],
                ], ['annual_quota' => 0]);
            }
        });

        return $backupPath;
    }

    public function schoolKey(string $value): string
    {
        $value = Str::of($value)->ascii()->lower()->value();
        $value = str_replace(['giao duc nghe nghiep', 'giao duc thuong xuyen', 'huong nghiep', '&', 'gtvt'], ['gdnn', 'gdtx', 'hn', ' va ', 'giao thong van tai'], $value);
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
        // Source reports use common abbreviated school/province names. Expand
        // only complete tokens so unrelated words and school names are not
        // changed. The resulting key must still resolve to exactly one row.
        $value = preg_replace('/\bcd\b/', 'cao dang', $value);
        $value = preg_replace('/\bnd\b/', 'nam dinh', $value);
        $value = preg_replace('/\b(huyen|thi xa|thanh pho)\b/', ' ', $value);

        return Str::squish($value);
    }

    private function exactSchoolKey(string $value): string
    {
        return Str::squish(preg_replace('/[^a-z0-9]+/', ' ', Str::ascii(mb_strtolower($value))));
    }

    private function stableSchoolIdKey(string $value): string
    {
        return preg_replace('/-\d{3}$/', '', $value) ?: $value;
    }

    private function majorKey(string $value): string
    {
        return Str::slug(str_replace('&', ' và ', $value));
    }
}
