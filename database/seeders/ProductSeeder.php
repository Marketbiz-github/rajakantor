<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/product.json');

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
        $seenSlugs = [];

        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                continue;
            }

            // best-effort field mapping with fallbacks
            $name = $item['name'] ?? $item['title'] ?? $item['product_name'] ?? null;
            if (!$name) {
                // skip records without a name
                continue;
            }

            $id_product = $item['id_product'] ?? $item['id'] ?? $item['product_id'] ?? ($item['sku'] ?? null) ?? Str::slug($name);


            // Always use link_rewrite for slug if available
            $baseSlug = Str::slug($item['link_rewrite'] ?? $item['slug'] ?? ($item['url'] ?? $name));
            if (empty($baseSlug)) {
                $baseSlug = Str::slug($name);
            }

            $candidate = $baseSlug;
            $i = 1;
            while (isset($seenSlugs[$candidate]) || DB::table('products')->where('slug', $candidate)->exists()) {
                $candidate = $baseSlug . '-' . $i;
                $i++;
            }
            $seenSlugs[$candidate] = true;

            $rows[] = [
                'id_product' => (string) $id_product,
                'name' => $name,
                'slug' => $candidate,
                'description' => $item['description'] ?? $item['description_long'] ?? $item['deskripsi'] ?? null,
                'description_short' => $item['description_short'] ?? $item['short_description'] ?? $item['deskripsi_singkat'] ?? null,
                'status' => $item['status'] ?? 'active',
                'meta_title' => $item['meta_title'] ?? ($item['meta']['title'] ?? null),
                'meta_description' => $item['meta_description'] ?? ($item['meta']['description'] ?? null),
                'meta_keywords' => $item['meta_keywords'] ?? ($item['meta']['keywords'] ?? null),
                'created_at' => $item['created_at'] ?? $now,
                'updated_at' => $item['updated_at'] ?? $now,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No product rows to seed.');
            return;
        }

        // insert in chunks to avoid huge single query
        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            DB::table('products')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' products.');
    }
}
