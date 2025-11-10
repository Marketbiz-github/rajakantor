<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/product_category.json');

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

        // helper to parse dates with multiple possible formats
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
                    // try next
                }
            }

            // last resort: let Carbon try to parse
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

            $id_product = $item['id_product'] ?? $item['product_id'] ?? $item['id_product_id'] ?? ($item['id'] ?? null);
            $id_category = $item['id_category'] ?? $item['category_id'] ?? $item['id_category_id'] ?? ($item['category'] ?? null);

            if (empty($id_product) || empty($id_category)) {
                // skip incomplete associations
                continue;
            }

            // avoid inserting duplicate product-category pairs
            $exists = DB::table('product_categories')
                ->where('id_product', (string) $id_product)
                ->where('id_category', (string) $id_category)
                ->exists();

            if ($exists) {
                continue;
            }

            $on_sale = (string) ($item['on_sale'] ?? $item['onsale'] ?? $item['on_sale_flag'] ?? '0');
            $status = $item['status'] ?? 'active';

            // parse created/updated timestamps if provided
            $created = $parseDate($item['created_at'] ?? $item['created'] ?? null);
            $updated = $parseDate($item['updated_at'] ?? $item['updated'] ?? null);

            $rows[] = [
                'id_product' => (string) $id_product,
                'id_category' => (string) $id_category,
                'on_sale' => $on_sale,
                'status' => $status,
                'created_at' => $created,
                'updated_at' => $updated,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No product-category rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            DB::table('product_categories')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' product-category.');
    }
}
