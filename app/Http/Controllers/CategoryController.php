<?php
namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $siteSettings = SiteSetting::first();

        $category = null;

        // Support URLs like: /category/{id_category}-{slug}
        // e.g. /category/1-home -> id_category = 1, slug = home
        $parts = explode('-', $slug, 2);
        if (count($parts) === 2 && is_numeric($parts[0])) {
            $idPart = $parts[0];
            $slugPart = $parts[1];

            $category = DB::table('categories')
                ->where('status', 1)
                ->where('id_category', $idPart)
                ->where('slug', $slugPart)
                ->first();
        }

        // Fallback: try matching full slug (legacy behaviour)
        if (!$category) {
            $category = DB::table('categories')
                ->where('status', 1)
                ->where('slug', $slug)
                ->first();
        }

        if (!$category) {
            abort(404);
        }

        // --- Get child categories (cached for 1 hour)
        $cacheKey = 'category_children_' . $category->id_category;
        $children = Cache::remember($cacheKey, 3600, function () use ($category) {
            $childCategoryIds = DB::table('category_parents')
                ->where('id_parent', $category->id_category)
                ->where('status', 1)
                ->pluck('id_category')
                ->toArray();

            if (empty($childCategoryIds)) {
                return [];
            }

            return DB::table('categories')
                ->select('id_category', 'name', 'slug', 'description')
                ->where('status', 1)
                ->whereIn('id_category', $childCategoryIds)
                ->orderBy('name')
                ->get()
                ->toArray();
        });

        // --- Get paginated products WITHOUT loading all data at once
        // Use JOIN to get only the products in this category, then paginate
        $productsPaginated = DB::table('products')
            ->select('products.*')
            ->join('product_categories', 'products.id_product', '=', 'product_categories.id_product')
            ->where('products.status', 1)
            ->where('product_categories.status', 1)
            ->where('product_categories.id_category', $category->id_category)
            ->distinct()
            ->paginate(10);

        // --- Get images ONLY for products on current page (lazy loading)
        if ($productsPaginated->count() > 0) {
            $pageProductIds = $productsPaginated->pluck('id_product')->toArray();
            
            $images = DB::table('images')
                ->whereIn('id_product', $pageProductIds)
                ->where('status', '1')
                ->where('position', '1')
                ->get()
                ->keyBy('id_product');

            // Attach images to products
            foreach ($productsPaginated as $product) {
                $product->images = [];
                if (isset($images[$product->id_product])) {
                    $img = $images[$product->id_product];
                    $product->images[] = 'images/product/' . $img->id_product . '-' . $img->id_image . '.jpg';
                }
            }
        }

        return view('category', compact('siteSettings', 'category', 'children', 'productsPaginated'));
    }
}