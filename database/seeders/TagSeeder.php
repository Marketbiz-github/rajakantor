<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/tag.json');

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

            $name = $item['name'] ?? $item['title'] ?? null;
            if (!$name) {
                continue;
            }

            $id_tag = $item['id_tag'] ?? $item['id'] ?? Str::slug($name);

            $baseSlug = Str::slug($item['slug'] ?? $name);
            if (empty($baseSlug)) {
                $baseSlug = Str::slug($name);
            }

            $candidate = $baseSlug;
            $i = 1;
            while (isset($seenSlugs[$candidate]) || DB::table('tags')->where('slug', $candidate)->exists()) {
                $candidate = $baseSlug . '-' . $i;
                $i++;
            }
            $seenSlugs[$candidate] = true;

            $rows[] = [
                'id_tag' => (string) $id_tag,
                'name' => $name,
                'slug' => $candidate,
                'status' => $item['status'] ?? 'active',
                'created_at' => $item['created_at'] ?? $now,
                'updated_at' => $item['updated_at'] ?? $now,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No tag rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 200);
        foreach ($chunks as $chunk) {
            DB::table('tags')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' tags.');
    }
}
