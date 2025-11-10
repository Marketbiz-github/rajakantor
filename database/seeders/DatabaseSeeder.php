<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(CategorySeeder::class);
        // $this->call(TagSeeder::class);
        $this->call(CategoryParentSeeder::class);
        $this->call(ProductCategorySeeder::class);
        // $this->call(ProductTagSeeder::class);
        $this->call(SiteSettingSeeder::class);
        $this->call(ImageSeeder::class);
    }
}
