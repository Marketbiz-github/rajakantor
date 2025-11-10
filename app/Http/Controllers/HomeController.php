<?php
namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $siteSettings = SiteSetting::first();

        $productIds = DB::table('product_categories')
            ->where('status', 1)
            ->where('id_category', 1)
            ->orderByDesc('updated_at')
            ->pluck('id_product');

        $products = DB::table('products')
            ->where('status', 1)
            ->whereIn('id_product', $productIds)
            ->orderByRaw('FIELD(id_product, ' . $productIds->implode(',') . ')')
            ->get();

        // Ambil images untuk setiap produk
        $productIds = $products->pluck('id_product')->toArray();
        $images = DB::table('images')
            ->whereIn('id_product', $productIds)
            ->where('status', '1')
            ->where('position', '1')
            ->get();

        // Mapping images ke produk
        foreach ($products as $product) {
            $productImages = $images->where('id_product', $product->id_product);
            $product->images = $productImages->map(function($img) {
                return 'images/product/' . $img->id_product . '-' . $img->id_image . '.jpg';
            })->values()->all();
        }

        // dd($siteSettings, $products);
        return view('home', compact('siteSettings', 'products'));
    }

    public function contact()
    {
        $siteSettings = SiteSetting::first();
        return view('contact', compact('siteSettings'));
    }

    public function about()
    {
        $siteSettings = SiteSetting::first();
        return view('about', compact('siteSettings'));
    }

    public function terms()
    {
        $siteSettings = SiteSetting::first();
        return view('terms', compact('siteSettings'));
    }

    public function client()
    {
        $siteSettings = SiteSetting::first();
        return view('client', compact('siteSettings'));
    }

    public function blog()
    {
        return redirect()->away('https://blog.rajakantor.com');
    }
}