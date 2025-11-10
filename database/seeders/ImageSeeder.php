<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('config/datas/image.json');

        if (!file_exists($path)) {
            $this->command?->error("File not found: {$path}");
            return;
        }

        $json = file_get_contents($path);
        $items = json_decode($json, true);

        if (!is_array($items)) {
            $this->command?->error("Invalid JSON in {$path}");
            return;
        }

        $now = now();
        $rows = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $rows[] = [
                'id_image'   => $item['id_image'] ?? $item['id'] ?? null,
                'id_product' => $item['id_product'] ?? null,
                'position'   => $item['position'] ?? '1',
                'cover'      => $item['cover'] ?? '0',
                'status'     => $item['status'] ?? '1',
                'created_at' => $item['created_at'] ?? $now,
                'updated_at' => $item['updated_at'] ?? $now,
            ];
        }

        if (empty($rows)) {
            $this->command?->info('No image rows to seed.');
            return;
        }

        $chunks = array_chunk($rows, 100);
        foreach ($chunks as $chunk) {
            DB::table('images')->insert($chunk);
        }

        $this->command?->info('Seeded ' . count($rows) . ' images.');
    }
}
