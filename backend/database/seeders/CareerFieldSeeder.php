<?php

namespace Database\Seeders;

use App\Models\CareerField;
use Illuminate\Database\Seeder;

class CareerFieldSeeder extends Seeder
{
    public function run(): void
    {
        foreach (CareerField::DEFAULTS as $item) {
            $field = CareerField::query()->firstOrNew(['id' => $item['id']]);

            if (! $field->exists) {
                $field->fill([
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'icon' => $item['icon'],
                    'description' => $item['description'],
                    'display_order' => $item['display_order'],
                    'is_active' => true,
                ])->save();
                continue;
            }

            // Preserve admin edits; only backfill empty metadata fields.
            $changed = false;
            foreach (['icon', 'description', 'display_order'] as $attribute) {
                if (blank($field->{$attribute})) {
                    $field->{$attribute} = $item[$attribute];
                    $changed = true;
                }
            }
            if ($changed) {
                $field->save();
            }
        }
    }
}
