<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/category.json');

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

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $name = $item['name'] ?? $item['title'] ?? $item['category_name'] ?? null;
            if (!$name) {
                continue;
            }

            $id_category = $item['id_category'] ?? $item['id'] ?? $item['category_id'] ?? Str::slug($name);

            // Always use link_rewrite for slug if available
            $baseSlug = Str::slug($item['link_rewrite'] ?? $item['slug'] ?? $name);
            if (empty($baseSlug)) {
                $baseSlug = Str::slug($name);
            }

            $candidate = $baseSlug;
            $i = 1;
            while (isset($seenSlugs[$candidate]) || DB::table('categories')->where('slug', $candidate)->exists()) {
                $candidate = $baseSlug . '-' . $i;
                $i++;
            }
            $seenSlugs[$candidate] = true;

            $rows[] = [
                'id_category' => (string) $id_category,
                'name' => $name,
                'slug' => $candidate,
                'description' => $item['description'] ?? $item['deskripsi'] ?? null,
                'status' => $item['status'] ?? 'active',
                'meta_title' => $item['meta_title'] ?? ($item['meta']['title'] ?? null),
                'meta_description' => $item['meta_description'] ?? ($item['meta']['description'] ?? null) ?? ($item['meta_description'] ?? null),
                'meta_keywords' => $item['meta_keywords'] ?? ($item['meta']['keywords'] ?? null),
                'created_at' => $item['created_at'] ?? $now,
                'updated_at' => $item['updated_at'] ?? $now,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No category rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            DB::table('categories')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' categories.');
    }
}
