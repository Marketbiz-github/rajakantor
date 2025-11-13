<?php
namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($slug)
    {
        $siteSettings = SiteSetting::first();

        $product = null;

        // Support URLs like: /product/{id_product}-{slug}
        // e.g. /product/6380-meja-kantor -> id_product = 6380, slug = meja-kantor
        $parts = explode('-', $slug, 2);
        if (count($parts) === 2 && $parts[0] !== '') {
            $idPart = $parts[0];
            $slugPart = $parts[1];

            $product = DB::table('products')
                ->where('status', 1)
                ->where('id_product', $idPart)
                ->where('slug', $slugPart)
                ->first();
        }

        // Fallback: try matching full slug (legacy behaviour)
        if (!$product) {
            $product = DB::table('products')
                ->where('status', 1)
                ->where('slug', $slug)
                ->first();
        }

        if (!$product) {
            abort(404);
        }

        // Ambil images untuk produk
        $images = DB::table('images')
            ->where('id_product', $product->id_product)
            ->where('status', '1')
            ->orderBy('position', 'asc')
            ->get();

        $productImages = $images->map(function($img) {
            $oldPath = public_path('images/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
            $newPath = storage_path('app/public/product/' . $img->id_product . '-' . $img->id_image . '.jpg');

            if (file_exists($newPath)) {
                return 'storage/product/' . $img->id_product . '-' . $img->id_image . '.jpg';
            } elseif (file_exists($oldPath)) {
                return 'images/product/' . $img->id_product . '-' . $img->id_image . '.jpg';
            } else {
                return 'images/product/en.jpg';
            }
        })->values()->all();


        $product->images = $productImages;

        // --- Build category breadcrumb trail for this product (use first assigned category)
        $categoryTrail = [];
        $catId = DB::table('product_categories')
            ->where('id_product', $product->id_product)
            ->where('status', 1)
            ->value('id_category');

        if ($catId) {
            // Walk up the parent chain (child -> parent -> ...)
            $current = DB::table('categories')
                ->where('id_category', $catId)
                ->where('status', 1)
                ->first();

            while ($current) {
                array_unshift($categoryTrail, [
                    'id_category' => $current->id_category,
                    'name' => $current->name,
                    'slug' => $current->slug,
                ]);

                $parentRel = DB::table('category_parents')
                    ->where('id_category', $current->id_category)
                    ->where('status', 1)
                    ->first();

                if ($parentRel && !empty($parentRel->id_parent)) {
                    $current = DB::table('categories')
                        ->where('id_category', $parentRel->id_parent)
                        ->where('status', 1)
                        ->first();
                } else {
                    break;
                }
            }
        }

        // Hilangkan kategori pertama (biasanya "Home")
        if (!empty($categoryTrail)) {
            array_shift($categoryTrail);
        }
        // dd($product);

        return view('product', compact('siteSettings', 'product', 'categoryTrail'));
    }

    /**
     * Search products by query string (GET /search?q=...)
     */
    public function search(Request $request)
    {
        $siteSettings = SiteSetting::first();

        $q = trim($request->query('q', ''));

        // Build base query
        $query = DB::table('products')
            ->where('status', 1);

        if ($q !== '') {
            $like = '%' . str_replace(' ', '%', $q) . '%';
            $query->where(function ($builder) use ($like) {
                $builder->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like);
            });
        } else {
            // If no query, return empty paginator
            $productsPaginated = DB::table('products')
                ->whereRaw('0 = 1')
                ->paginate(10);
            return view('search', compact('siteSettings', 'productsPaginated'));
        }

        // Paginate results (10 per page)
        $productsPaginated = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        // Attach first image for products on this page
        if ($productsPaginated->count() > 0) {
            $pageProductIds = $productsPaginated->pluck('id_product')->toArray();
            $images = DB::table('images')
                ->whereIn('id_product', $pageProductIds)
                ->where('status', '1')
                ->where('position', '1')
                ->get()
                ->keyBy('id_product');

            $productsPaginated->each(function ($product) use ($images) {
                $product->images = [];
                if (isset($images[$product->id_product])) {
                    $img = $images[$product->id_product];
                    
                    $oldPath = public_path('images/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    $newPath = storage_path('app/public/product/' . $img->id_product . '-' . $img->id_image . '.jpg');

                    if (file_exists($newPath)) {
                        $product->images[] = asset('storage/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    } elseif (file_exists($oldPath)) {
                        $product->images[] = asset('images/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    } else {
                        $product->images[] = asset('images/product/en.jpg');
                    }
                }
            });
        }

        // dd($request, $productsPaginated);

        return view('search', compact('siteSettings', 'productsPaginated'));
    }

    /**
     * Promo products - Get products with on_sale = 1 in product_categories
     */
    public function promo()
    {
        $siteSettings = SiteSetting::first();

        // Get product IDs where on_sale = 1
        $promoProductIds = DB::table('product_categories')
            ->where('status', 1)
            ->where('on_sale', 1)
            ->pluck('id_product')
            ->unique()
            ->toArray();

        // Get products and paginate
        $productsPaginated = DB::table('products')
            ->where('status', 1)
            ->whereIn('id_product', $promoProductIds ?: [0])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Attach first image for products on this page
        if ($productsPaginated->count() > 0) {
            $pageProductIds = $productsPaginated->pluck('id_product')->toArray();
            $images = DB::table('images')
                ->whereIn('id_product', $pageProductIds)
                ->where('status', '1')
                ->where('position', '1')
                ->get()
                ->keyBy('id_product');

            $productsPaginated->each(function ($product) use ($images) {
                $product->images = [];
                
                if (isset($images[$product->id_product])) {
                    $img = $images[$product->id_product];
                    
                    $oldPath = public_path('images/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    $newPath = storage_path('app/public/product/' . $img->id_product . '-' . $img->id_image . '.jpg');

                    if (file_exists($newPath)) {
                        $product->images[] = asset('storage/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    } elseif (file_exists($oldPath)) {
                        $product->images[] = asset('images/product/' . $img->id_product . '-' . $img->id_image . '.jpg');
                    } else {
                        $product->images[] = asset('images/product/en.jpg');
                    }
                }
            });
        }

        return view('promo', compact('siteSettings', 'productsPaginated'));
    }
}