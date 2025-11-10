<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/product_tag.json');

        if (!File::exists($path)) {
            $this->command?->error("File not found: {$path}");
            return;
        }

        $json = File::get($path);
        $items = json_decode($json, true);

        if (!is_array($items) || empty($items)) {
            $this->command?->info("No product-tag data found in {$path}");
            return;
        }

        $now = Carbon::now();
        $rows = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            // JSON may use different keys; be tolerant
            $idProduct = $item['id_product'] ?? null;
            $idTag = $item['id_tag'] ?? $item['tag_id'] ?? $item['id'] ?? null;

            if (empty($idProduct) || empty($idTag)) {
                continue;
            }

            // avoid duplicate product-tag pairs
            $exists = DB::table('product_tags')
                ->where('id_product', (string) $idProduct)
                ->where('id_tag', (string) $idTag)
                ->exists();

            if ($exists) {
                continue;
            }

            $created = $item['created_at'] ?? $item['created'] ?? $now;
            $updated = $item['updated_at'] ?? $item['updated'] ?? $now;

            // normalize to Carbon where possible
            try {
                $created = Carbon::parse($created);
            } catch (\Exception $e) {
                $created = $now;
            }

            try {
                $updated = Carbon::parse($updated);
            } catch (\Exception $e) {
                $updated = $now;
            }

            $rows[] = [
                'id_product' => (string) $idProduct,
                'id_tag' => (string) $idTag,
                'created_at' => $created,
                'updated_at' => $updated,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No product-tag rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            DB::table('product_tags')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' product-tag.');
    }
}
