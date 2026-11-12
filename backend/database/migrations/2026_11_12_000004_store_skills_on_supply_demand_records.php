<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('workforce_demands', 'skills')) {
            Schema::table('workforce_demands', function (Blueprint $table): void {
                $table->json('skills')->nullable()->after('salary');
            });
        }

        if (! Schema::hasColumn('training_capacities', 'skills')) {
            Schema::table('training_capacities', function (Blueprint $table): void {
                $table->json('skills')->nullable()->after('equipment');
            });
        }

        $this->copyPivotToJson('workforce_demands', 'demand_skill', 'workforce_demand_id');
        $this->copyPivotToJson('training_capacities', 'capacity_skill', 'training_capacity_id');
    }

    public function down(): void
    {
        $this->copyJsonToPivot('workforce_demands', 'demand_skill', 'workforce_demand_id');
        $this->copyJsonToPivot('training_capacities', 'capacity_skill', 'training_capacity_id');

        if (Schema::hasColumn('workforce_demands', 'skills')) {
            Schema::table('workforce_demands', fn (Blueprint $table) => $table->dropColumn('skills'));
        }

        if (Schema::hasColumn('training_capacities', 'skills')) {
            Schema::table('training_capacities', fn (Blueprint $table) => $table->dropColumn('skills'));
        }
    }

    private function copyPivotToJson(string $table, string $pivot, string $foreignKey): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable($pivot) || ! Schema::hasTable('skills')) {
            return;
        }

        DB::table($table)->orderBy('id')->each(function (object $record) use ($table, $pivot, $foreignKey): void {
            if (filled($record->skills ?? null)) {
                return;
            }

            $skills = DB::table($pivot)
                ->join('skills', 'skills.id', '=', $pivot.'.skill_id')
                ->where($pivot.'.'.$foreignKey, $record->id)
                ->orderBy('skills.name')
                ->pluck('skills.name')
                ->filter()
                ->values()
                ->all();

            if ($skills !== []) {
                DB::table($table)->where('id', $record->id)->update([
                    'skills' => json_encode($skills, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ]);
            }
        });
    }

    private function copyJsonToPivot(string $table, string $pivot, string $foreignKey): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasTable($pivot) || ! Schema::hasTable('skills') || ! Schema::hasColumn($table, 'skills')) {
            return;
        }

        DB::table($table)->whereNotNull('skills')->orderBy('id')->each(function (object $record) use ($pivot, $foreignKey): void {
            $names = json_decode((string) $record->skills, true);
            if (! is_array($names)) {
                return;
            }
            DB::table($pivot)->where($foreignKey, $record->id)->delete();
            foreach (is_array($names) ? $names : [] as $name) {
                $name = Str::of((string) $name)->squish()->trim()->toString();
                if ($name === '') {
                    continue;
                }

                $normalized = Str::of($name)->ascii()->lower()->squish()->trim()->toString();
                $skillId = DB::table('skills')->where('normalized_name', $normalized)->value('id');
                if (! $skillId) {
                    $skillId = DB::table('skills')->insertGetId([
                        'name' => $name,
                        'normalized_name' => $normalized,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table($pivot)->insertOrIgnore([
                    $foreignKey => $record->id,
                    'skill_id' => $skillId,
                ]);
            }
        });
    }
};
