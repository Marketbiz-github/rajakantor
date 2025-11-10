<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class CategoryParentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/category_parent.json');

        if (!File::exists($path)) {
            $this->command?->error("File not found: {$path}");
            return;
        }

        $json = File::get($path);
        $items = json_decode($json, true);

        if (!is_array($items)) {
            $this->command?->error("Invalid JSON in {$path}");
            return;
        }

        $now = Carbon::now();
        $rows = [];

        $parseDate = function ($value) use ($now) {
            if (empty($value)) {
                return $now;
            }
            $formats = [
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'Y-m-d H:i:s',
                'Y-m-d',
                'd/m/Y',
            ];

            foreach ($formats as $fmt) {
                try {
                    $dt = Carbon::createFromFormat($fmt, $value);
                    if ($dt) {
                        return $dt;
                    }
                } catch (\Exception $e) {
                    // continue
                }
            }

            try {
                return Carbon::parse($value);
            } catch (\Exception $e) {
                return $now;
            }
        };

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $id_category = $item['id_category'] ?? $item['category_id'] ?? $item['id'] ?? null;
            $id_parent = $item['id_parent'] ?? $item['parent_id'] ?? $item['parent'] ?? null;

            if (empty($id_category) || empty($id_parent)) {
                continue;
            }

            // skip if pair already exists
            $exists = DB::table('category_parents')
                ->where('id_category', (string) $id_category)
                ->where('id_parent', (string) $id_parent)
                ->exists();

            if ($exists) {
                continue;
            }

            $status = $item['status'] ?? 'active';
            $levelDepth = $item['level_depth'] ?? '1';
            $created = $parseDate($item['created_at'] ?? $item['created'] ?? null);
            $updated = $parseDate($item['updated_at'] ?? $item['updated'] ?? null);

            $rows[] = [
                'id_category' => (string) $id_category,
                'id_parent' => (string) $id_parent,
                'level_depth' => $levelDepth,
                'status' => $status,
                'created_at' => $created,
                'updated_at' => $updated,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No category-parent rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            DB::table('category_parents')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' category-parent.');
    }
}
