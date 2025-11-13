<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $products = DB::table('products')
            ->whereIn('status', [1, 2])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Add category name for each product
        $products = $products->map(function ($product) {
            $category = DB::table('product_categories')
                ->join('categories', 'product_categories.id_category', '=', 'categories.id_category')
                ->where('product_categories.id_product', $product->id_product)
                ->where('product_categories.status', 1)
                ->select('categories.name as category_name', 'categories.id_category')
                ->first();

            $product->category_name = $category?->category_name ?? '-';
            $product->id_category = $category?->id_category ?? null;

            return $product;
        });

        return view('admin.product', compact('products'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = DB::table('categories')->where('status', 1)->orderBy('name')->get();

        return view('admin.product-add', compact('categories'));
    }

    private function generateUniqueSlug($name, $table = 'products', $column = 'slug')
    {
        // 1️⃣ Buat slug dasar dari name
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // 2️⃣ Loop sampai dapat slug unik
        while (DB::table($table)->where($column, $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Store a new product with images
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_short' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:1,2',
            'image1' => 'nullable|image|mimes:jpg|max:2048',
            'image2' => 'nullable|image|mimes:jpg|max:2048',
            'image3' => 'nullable|image|mimes:jpg|max:2048',
            'category' => 'nullable|exists:categories,id',
        ];

        $validated = $request->validate($rules);

        // Generate unique id_product from max existing id_product + 1
        $maxIdProduct = DB::table('products')
            ->selectRaw('MAX(CAST(id_product AS UNSIGNED)) as max_id')
            ->value('max_id');

        // dd($maxIdProduct);
        
        $newIdProduct = ($maxIdProduct ? (int)$maxIdProduct : 0) + 1;
        
        $slug = $this->generateUniqueSlug($request->input('name'), 'products', 'slug');

        // Prepare product data
        $data = [
            'id_product' => $newIdProduct,
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'description_short' => $request->input('description_short'),
            'meta_title' => $request->input('meta_title'),
            'meta_keywords' => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'status' => $request->input('status'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // dd($data);

        // Insert product and get id
        $productId = DB::table('products')->insertGetId($data);

        // Handle image uploads: image1, image2, image3
        $imagePaths = ['image1' => 1, 'image2' => 2, 'image3' => 3];
        $firstImageId = null;

        foreach ($imagePaths as $fieldName => $position) {
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                
                // Generate image ID (sequential based on existing images for this product)
                $maxImageId = DB::table('images')
                    ->where('id_product', $newIdProduct)
                    ->selectRaw('MAX(CAST(id_image AS UNSIGNED)) as max_id')
                    ->value('max_id');
                $imageId = ($maxImageId ? (int)$maxImageId : 0) + 1;

                // Pastikan folder product ada
                $productPath = storage_path('app/public/product');
                if (!File::exists($productPath)) {
                    File::makeDirectory($productPath, 0755, true);
                }

                // Rename file to {id_product}-{id_image}.jpg
                $filename = $newIdProduct . '-' . $imageId . '.jpg';
                
                // Store in public/product
                // $path = $file->storeAs('public/product', $filename);
                $path = Storage::disk('public')->putFileAs('product', $file, $filename);
                // if ($path) {
                //     dd('Success', $path);
                // } else {
                //     dd('Failed');
                // }
                // dd($path);

                // Insert into images table
                DB::table('images')->insert([
                    'id_image' => $imageId,
                    'id_product' => $newIdProduct,
                    'position' => $position,
                    'cover' => ($firstImageId === null ? '1' : '0'), // First image is cover
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                if ($firstImageId === null) {
                    $firstImageId = $imageId;
                }
            }
        }

        // Link product to category if provided
        if ($request->input('category')) {
            DB::table('product_categories')->insert([
                'id_product' => $newIdProduct,
                'id_category' => $request->input('category'),
                'on_sale' => '0',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('product.index')->with('success', 'Produk berhasil dibuat. ID: ' . $newIdProduct);
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (! $product) {
            return redirect()->route('product.index')->with('error', 'Produk tidak ditemukan.');
        }

        // Load categories for dropdown
        $categories = DB::table('categories')->where('status', 1)->orderBy('name')->get();

        // Load current images for this product
        $images = DB::table('images')->where('id_product', $product->id_product)->where('status', 1)->orderBy('position')->get();

        // Load current category assignment
        $productCategory = DB::table('product_categories')
            ->where('id_product', $product->id_product)
            ->first();

        // dd($productCategory);

        return view('admin.product-edit', compact('product', 'categories', 'images', 'productCategory'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect()->route('product.index')->with('error', 'Produk tidak ditemukan.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'description_short' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'status' => 'required|in:1,2',
            'image1' => 'nullable|image|mimes:jpg|max:2048',
            'image2' => 'nullable|image|mimes:jpg|max:2048',
            'image3' => 'nullable|image|mimes:jpg|max:2048',
            'category' => 'nullable|exists:categories,id',
        ];

        $validated = $request->validate($rules);

        // Update product
        $data = [
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'description_short' => $request->input('description_short'),
            'meta_title' => $request->input('meta_title'),
            'meta_keywords' => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'status' => $request->input('status'),
            'updated_at' => now(),
        ];

        DB::table('products')->where('id', $id)->update($data);

        // Handle image uploads: image1, image2, image3
        $imagePaths = ['image1' => 1, 'image2' => 2, 'image3' => 3];

        foreach ($imagePaths as $fieldName => $position) {
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);

                // If there's an existing active image at this position, archive it (status = 2)
                $existing = DB::table('images')
                    ->where('id_product', $product->id_product)
                    ->where('position', $position)
                    ->where('status', '1')
                    ->first();

                if ($existing) {
                    DB::table('images')
                        ->where('id', $existing->id)
                        ->update([
                            'status' => '2',
                            'cover' => '0',
                            'updated_at' => now(),
                        ]);
                }

                // Generate image ID
                $maxImageId = DB::table('images')
                    ->where('id_product', $product->id_product)
                    ->selectRaw('MAX(CAST(id_image AS UNSIGNED)) as max_id')
                    ->value('max_id');
                $imageId = ($maxImageId ? (int)$maxImageId : 0) + 1;

                // Ensure storage folder exists
                $productPath = storage_path('app/public/product');
                if (!File::exists($productPath)) {
                    File::makeDirectory($productPath, 0755, true);
                }

                // Rename file to {id_product}-{id_image}.jpg and store
                $filename = $product->id_product . '-' . $imageId . '.jpg';
                $path = Storage::disk('public')->putFileAs('product', $file, $filename);

                // Decide cover: if no active cover exists now, or the existing archived image was cover, assign cover to new image
                $hasActiveCover = DB::table('images')
                    ->where('id_product', $product->id_product)
                    ->where('cover', '1')
                    ->where('status', '1')
                    ->exists();

                $assignCover = (!$hasActiveCover || ($existing && $existing->cover == '1')) ? '1' : '0';
                if ($assignCover === '1') {
                    // clear other cover flags just in case
                    DB::table('images')
                        ->where('id_product', $product->id_product)
                        ->update(['cover' => '0']);
                }

                // Insert new image record
                DB::table('images')->insert([
                    'id_image' => $imageId,
                    'id_product' => $product->id_product,
                    'position' => $position,
                    'cover' => $assignCover,
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Update category assignment
        $existingCategory = DB::table('product_categories')
            ->where('id_product', $product->id_product)
            ->first();

        if ($request->input('category')) {
            if ($existingCategory) {
                DB::table('product_categories')
                    ->where('id_product', $product->id_product)
                    ->update([
                        'id_category' => $request->input('category'),
                        'updated_at' => now(),
                    ]);
            } else {
                DB::table('product_categories')->insert([
                    'id_product' => $product->id_product,
                    'id_category' => $request->input('category'),
                    'on_sale' => '0',
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($existingCategory) {
            DB::table('product_categories')
                ->where('id_product', $product->id_product)
                ->delete();
        }

        return redirect()->route('product.edit', [$id])->with('success', 'Produk berhasil diperbarui.');
    }
}