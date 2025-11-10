<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Provide `siteSettings` and categories to the app landing layout
        View::composer('layouts.app-landingpage', function ($view) {
            // Get site settings
            $siteSettings = SiteSetting::first();

            // 1️⃣ Ambil semua kategori aktif
            $categories = DB::table('categories')
                ->select('id_category', 'name', 'status', 'description', 'slug', 'meta_title', 'meta_keywords', 'meta_description')
                ->where('status', 1)
                ->get()
                ->keyBy('id_category')
                ->toArray();

            // 2️⃣ Ambil semua relasi parent-child aktif
            $relations = DB::table('category_parents')
                ->select('id_category', 'id_parent', 'level_depth')
                ->where('status', 1)
                ->get()
                ->toArray();

            // 3️⃣ Tambahkan properti children di setiap kategori
            foreach ($categories as &$cat) {
                $cat->children = [];
            }
            unset($cat);

            // 4️⃣ Hubungkan kategori anak ke parent-nya
            foreach ($relations as $rel) {
                if (isset($categories[$rel->id_category]) && isset($categories[$rel->id_parent])) {
                    $categories[$rel->id_parent]->children[] = $categories[$rel->id_category];
                }
            }

            // 5️⃣ Ambil hanya kategori yang tidak punya parent → jadi root
            $tree = [];
            foreach ($categories as $category) {
                $hasParent = false;
                foreach ($relations as $rel) {
                    if ($rel->id_category == $category->id_category) {
                        $hasParent = true;
                        break;
                    }
                }
                if (!$hasParent) {
                    $tree[] = $category;
                }
            }

            // 6️⃣ Ubah dari object ke array biasa biar gampang dipakai di Blade
            $treeArray = json_decode(json_encode($tree), true);

             $sortTree = function (&$nodes) use (&$sortTree) {
                usort($nodes, function ($a, $b) {
                    return strcasecmp($a['name'], $b['name']);
                });

                foreach ($nodes as &$node) {
                    if (!empty($node['children'])) {
                        $sortTree($node['children']);
                    }
                }
            };

            $sortTree($treeArray);


            // dd($treeArray);

            // --- Latest 5 products (raw DB query, include cover image if available)
            $latestProducts = DB::table('products')
                ->select('id_product', 'name', 'slug', 'description', 'description_short', 'meta_title', 'meta_description', 'meta_keywords', 'created_at')
                ->where('status', 1)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->toArray();

            $latestArray = [];
            if (!empty($latestProducts)) {
                $productIds = array_map(function ($p) { return $p->id_product; }, $latestProducts);

                $images = DB::table('images')
                    ->whereIn('id_product', $productIds)
                    ->where('status', '1')
                    ->where('position', '1')
                    ->get()
                    ->toArray();

                foreach ($latestProducts as $p) {
                    $pArr = json_decode(json_encode($p), true);
                    $pArr['image'] = null;

                    foreach ($images as $img) {
                        if ($img->id_product == $p->id_product) {
                            $pArr['image'] = 'images/product/' . $img->id_product . '-' . $img->id_image . '.jpg';
                            break;
                        }
                    }

                    $latestArray[] = $pArr;
                }
            }

            $view->with([
                'siteSettings' => $siteSettings,
                'categories' => $treeArray,
                'latestProducts' => $latestArray,
            ]);
        });
    }
}
