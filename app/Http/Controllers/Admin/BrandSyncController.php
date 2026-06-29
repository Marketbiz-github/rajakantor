<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BrandSyncController extends Controller
{
    /**
     * Brand names to sync. Each brand will get its own category
     * and all products from sub-categories (containing the brand name)
     * will also be assigned to the brand category.
     */
    private $brands = [
        'Alba',
        'Lion',
        'Brother',
        'Chairman',
        'Donati',
        'Elite',
        'Indachi',
        'Daiko',
        'Chitose',
        'Uno',
        'Modera',
        'Savello',
        'Ergotec',
        'Erka',
        'Daichiban',
        'Carrera',
    ];

    /**
     * GET /dashboard/sync-brands
     * 
     * Trigger route to:
     * 1. Create brand categories if they don't exist
     * 2. Find all sub-categories containing the brand name
     * 3. Assign products from those sub-categories to the brand category
     */
    public function sync()
    {
        $results = [];
        $totalCreated = 0;
        $totalAssigned = 0;

        foreach ($this->brands as $brandName) {
            $result = $this->syncBrand($brandName);
            $results[] = $result;
            $totalCreated += $result['category_created'] ? 1 : 0;
            $totalAssigned += $result['products_assigned'];
        }

        return response()->json([
            'success' => true,
            'message' => "Sync complete. {$totalCreated} brand categories created, {$totalAssigned} product assignments added.",
            'brands' => $results,
        ]);
    }

    /**
     * GET /dashboard/sync-brands/preview
     * 
     * Preview what will happen without making any changes (dry run).
     */
    public function preview()
    {
        $results = [];

        foreach ($this->brands as $brandName) {
            $result = $this->previewBrand($brandName);
            $results[] = $result;
        }

        return response()->json([
            'success' => true,
            'message' => 'Preview only — no changes were made.',
            'brands' => $results,
        ]);
    }

    /**
     * Sync a single brand: create category if needed, assign products.
     */
    private function syncBrand(string $brandName): array
    {
        $result = [
            'brand' => $brandName,
            'category_created' => false,
            'category_id' => null,
            'sub_categories_found' => 0,
            'products_assigned' => 0,
            'products_already_assigned' => 0,
            'sub_categories' => [],
        ];

        // 1. Find or create the brand category
        $brandCategory = DB::table('categories')
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($brandName))])
            ->first();

        if (!$brandCategory) {
            // Create the brand category
            $maxIdCategory = DB::table('categories')
                ->selectRaw('MAX(CAST(id_category AS UNSIGNED)) as max_id')
                ->value('max_id');
            $newIdCategory = ($maxIdCategory ? (int) $maxIdCategory : 0) + 1;

            $slug = $this->generateUniqueSlug($brandName);

            DB::table('categories')->insert([
                'id_category' => $newIdCategory,
                'name' => $brandName,
                'slug' => $slug,
                'description' => "Produk-produk {$brandName}",
                'meta_title' => "Produk {$brandName} - Raja Kantor",
                'meta_keywords' => strtolower($brandName) . ', furniture kantor, office equipment',
                'meta_description' => "Jual produk {$brandName} murah di Raja Kantor. Tersedia berbagai macam furniture dan equipment kantor merek {$brandName}.",
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Also add to category_parents with Home (id_parent = 1) as parent, like the production site
            DB::table('category_parents')->insert([
                'id_category' => $newIdCategory,
                'id_parent' => 1, // Home
                'level_depth' => 1,
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $result['category_created'] = true;
            $result['category_id'] = $newIdCategory;
            $brandCategoryId = $newIdCategory;
        } else {
            $result['category_id'] = $brandCategory->id_category;
            $brandCategoryId = $brandCategory->id_category;
        }

        // 2. Find all sub-categories containing this brand name
        $subCategories = DB::table('categories')
            ->where('status', 1)
            ->where('name', 'LIKE', "%{$brandName}%")
            ->whereRaw('LOWER(TRIM(name)) != ?', [strtolower(trim($brandName))])
            ->get();

        $result['sub_categories_found'] = $subCategories->count();

        // 3. For each sub-category, get its products and assign to brand category
        foreach ($subCategories as $subCat) {
            $productIds = DB::table('product_categories')
                ->where('id_category', $subCat->id_category)
                ->pluck('id_product')
                ->toArray();

            if (empty($productIds))
                continue;

            // Link sub-category to brand category in category_parents so it shows up as a subcategory on the brand category page
            $parentRelationExists = DB::table('category_parents')
                ->where('id_category', $subCat->id_category)
                ->where('id_parent', $brandCategoryId)
                ->exists();

            if (!$parentRelationExists) {
                DB::table('category_parents')->insert([
                    'id_category' => $subCat->id_category,
                    'id_parent' => $brandCategoryId,
                    'level_depth' => 2,
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Check which products are NOT yet assigned to the brand category
            $alreadyAssigned = DB::table('product_categories')
                ->where('id_category', $brandCategoryId)
                ->whereIn('id_product', $productIds)
                ->pluck('id_product')
                ->toArray();

            $toAssign = array_diff($productIds, $alreadyAssigned);

            $result['products_already_assigned'] += count($alreadyAssigned);

            if (!empty($toAssign)) {
                $insertData = [];
                foreach ($toAssign as $pid) {
                    $insertData[] = [
                        'id_product' => $pid,
                        'id_category' => $brandCategoryId,
                        'on_sale' => '0',
                        'status' => '1',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('product_categories')->insert($insertData);
                $result['products_assigned'] += count($toAssign);
            }

            $result['sub_categories'][] = [
                'name' => $subCat->name,
                'id' => $subCat->id_category,
                'total_products' => count($productIds),
                'newly_assigned' => count($toAssign),
                'already_assigned' => count($alreadyAssigned),
            ];
        }

        return $result;
    }

    /**
     * Preview a single brand (dry run, no DB changes).
     */
    private function previewBrand(string $brandName): array
    {
        $result = [
            'brand' => $brandName,
            'category_exists' => false,
            'category_id' => null,
            'would_create_category' => false,
            'sub_categories_found' => 0,
            'products_would_assign' => 0,
            'products_already_assigned' => 0,
            'sub_categories' => [],
        ];

        // Check if brand category exists
        $brandCategory = DB::table('categories')
            ->where('status', 1)
            ->whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($brandName))])
            ->first();

        if ($brandCategory) {
            $result['category_exists'] = true;
            $result['category_id'] = $brandCategory->id_category;
            $brandCategoryId = $brandCategory->id_category;
        } else {
            $result['would_create_category'] = true;
            $brandCategoryId = null; // Will be created
        }

        // Find sub-categories
        $subCategories = DB::table('categories')
            ->where('status', 1)
            ->where('name', 'LIKE', "%{$brandName}%")
            ->whereRaw('LOWER(TRIM(name)) != ?', [strtolower(trim($brandName))])
            ->get();

        $result['sub_categories_found'] = $subCategories->count();

        foreach ($subCategories as $subCat) {
            $productIds = DB::table('product_categories')
                ->where('id_category', $subCat->id_category)
                ->pluck('id_product')
                ->toArray();

            if (empty($productIds))
                continue;

            $alreadyAssigned = 0;
            if ($brandCategoryId) {
                $alreadyAssigned = DB::table('product_categories')
                    ->where('id_category', $brandCategoryId)
                    ->whereIn('id_product', $productIds)
                    ->count();
            }

            $wouldAssign = count($productIds) - $alreadyAssigned;

            $result['products_would_assign'] += $wouldAssign;
            $result['products_already_assigned'] += $alreadyAssigned;

            $result['sub_categories'][] = [
                'name' => $subCat->name,
                'id' => $subCat->id_category,
                'total_products' => count($productIds),
                'would_assign' => $wouldAssign,
                'already_assigned' => $alreadyAssigned,
            ];
        }

        return $result;
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table('categories')->where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
