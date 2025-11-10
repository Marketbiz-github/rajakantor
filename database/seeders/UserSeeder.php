<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Raja KANTOR',
                'email' => 'info@rajakantor.com',
                'password' => 'dcb312270cdbbc86246c9a1a9bb8e59a',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Husen MARKETBIZ',
                'email' => 'husen@marketbiz.net',
                'password' => '184bc33a5a14945f88912e0976e6a2c7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

