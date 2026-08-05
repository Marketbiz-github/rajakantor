<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = DB::table('categories')
            ->leftJoin('category_parents', 'categories.id_category', '=', 'category_parents.id_category')
            ->leftJoin('categories as parents', 'category_parents.id_parent', '=', 'parents.id_category');

        if ($request->has('status') && in_array($request->status, [1, 2, '1', '2'])) {
            $query->where('categories.status', $request->status);
        } else {
            $query->whereIn('categories.status', [1, 2]);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('categories.id_category', 'like', "%{$search}%")
                  ->orWhere('categories.name', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('categories.updated_at', 'desc')
            ->select(
                'categories.id',
                'categories.id_category',
                'categories.name',
                'parents.name as parent_name',
                'categories.slug',
                'categories.status',
                'categories.updated_at'
            )
            ->paginate(15);

        $categories->getCollection()->transform(function ($category) {
            $children = DB::table('category_parents')
                ->join('categories', 'category_parents.id_category', '=', 'categories.id_category')
                ->where('id_parent', $category->id_category)
                ->pluck('categories.name');
                
            $category->child_count = $children->count();
            $category->children_names = $children->implode(', ');
                
            $category->product_count = DB::table('product_categories')
                ->where('id_category', $category->id_category)
                ->count();
                
            return $category;
        });

        return view('admin.category', compact('categories'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $categories = DB::table('categories')->where('status', 1)->orderBy('name')->get();

        return view('admin.category-add', compact('categories'));
    }

    private function generateUniqueSlug($name, $table = 'categories', $column = 'slug')
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (DB::table($table)->where($column, $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Store a new category
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'category_parent' => 'nullable|exists:categories,id_category',
            'image' => 'nullable|image|mimes:jpg|max:2048',
            'status' => 'required|in:1,2',
        ];

        $validated = $request->validate($rules);

        // Generate unique id_category from max existing id_category + 1
        $maxIdCategory = DB::table('categories')
            ->selectRaw('MAX(CAST(id_category AS UNSIGNED)) as max_id')
            ->value('max_id');

        $newIdCategory = ($maxIdCategory ? (int)$maxIdCategory : 0) + 1;

        $slug = $this->generateUniqueSlug($request->input('name'), 'categories', 'slug');

        $categoryData = [
            'id_category' => $newIdCategory,
            'name' => $request->input('name'),
            'slug' => $slug,
            'description' => $request->input('description'),
            'meta_title' => $request->input('meta_title'),
            'meta_keywords' => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'status' => $request->input('status'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('categories')->insert($categoryData);

        // Save to category_parents if parent is selected
        if ($request->input('category_parent')) {
            DB::table('category_parents')->insert([
                'id_category' => $newIdCategory,
                'id_parent' => $request->input('category_parent'),
                'level_depth' => '1',
                'status' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Ensure storage folder exists
            $categoryPath = storage_path('app/public/category');
            if (!File::exists($categoryPath)) {
                File::makeDirectory($categoryPath, 0755, true);
            }

            // Rename file to {id_category}.jpg
            $filename = $newIdCategory . '.jpg';

            // Store in public/category
            $path = Storage::disk('public')->putFileAs('category', $file, $filename);
        }

        return redirect()->route('category.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $category = DB::table('categories')
            ->where('id', $id)
            ->first();

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $categories = DB::table('categories')->where('status', 1)->get();

        // Get parent category data if exists
        $parentCategory = null;
        if ($category->id_category) {
            $parentCategory = DB::table('category_parents')
                ->where('id_category', $category->id_category)
                ->first();
        }

        // Get counts for delete validation
        $children = DB::table('category_parents')
            ->join('categories', 'category_parents.id_category', '=', 'categories.id_category')
            ->where('id_parent', $category->id_category)
            ->pluck('categories.name');
            
        $childCount = $children->count();
        $childrenNames = $children->implode(', ');
            
        $productCount = DB::table('product_categories')->where('id_category', $category->id_category)->count();

        return view('admin.category-edit', compact('category', 'categories', 'parentCategory', 'childCount', 'productCount', 'childrenNames'));
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id)
    {
        $category = DB::table('categories')
            ->where('id', $id)
            ->first();

        if (!$category) {
            abort(404, 'Kategori tidak ditemukan');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'category_parent' => 'nullable|exists:categories,id_category',
            'image' => 'nullable|image|mimes:jpg|max:2048',
            'status' => 'required|in:1,2,hapus',
        ];

        $validated = $request->validate($rules);

        if ($request->input('status') === 'hapus') {
            $childCount = DB::table('category_parents')->where('id_parent', $category->id_category)->count();
            if ($childCount > 0) {
                return redirect()->back()->with('error', 'Gagal: Kategori ini memiliki subkategori. Hapus subkategori terlebih dahulu.');
            }
            
            $deleteProducts = $request->input('delete_products') == '1';
            $this->deleteCategoryData($category, $deleteProducts);

            return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus.');
        }

        $categoryData = [
            'name' => $request->input('name'),
            'slug' => $request->input('slug'),
            'description' => $request->input('description'),
            'meta_title' => $request->input('meta_title'),
            'meta_keywords' => $request->input('meta_keywords'),
            'meta_description' => $request->input('meta_description'),
            'status' => $request->input('status'),
            'updated_at' => now(),
        ];

        DB::table('categories')
            ->where('id', $id)
            ->update($categoryData);

        // Update or insert category_parents entry
        $existingParent = DB::table('category_parents')
            ->where('id_category', $category->id_category)
            ->first();

        if ($request->input('category_parent')) {
            if ($existingParent) {
                // Update existing parent relationship
                DB::table('category_parents')
                    ->where('id_category', $category->id_category)
                    ->update([
                        'id_parent' => $request->input('category_parent'),
                        'status' => '1',
                        'updated_at' => now(),
                    ]);
            } else {
                // Insert new parent relationship
                DB::table('category_parents')->insert([
                    'id_category' => $category->id_category,
                    'id_parent' => $request->input('category_parent'),
                    'level_depth' => '1',
                    'status' => '1',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } elseif ($existingParent) {
            // Delete parent relationship if no parent selected
            DB::table('category_parents')
                ->where('id_category', $category->id_category)
                ->delete();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Ensure storage folder exists
            $categoryPath = storage_path('app/public/category');
            if (!File::exists($categoryPath)) {
                File::makeDirectory($categoryPath, 0755, true);
            }

            // Rename file to {id_category}.jpg
            $filename = $category->id_category . '.jpg';

            // Store in public/category
            $path = Storage::disk('public')->putFileAs('category', $file, $filename);
        }

        return redirect()->route('category.edit', [$id])->with('success', 'Kategori berhasil diperbarui');
    }

    /**
     * Delete category completely
     */
    public function destroy(Request $request, $id)
    {
        $category = DB::table('categories')->where('id', $id)->first();
        if (!$category) {
            return redirect()->route('category.index')->with('error', 'Kategori tidak ditemukan.');
        }

        // Validate children first
        $childCount = DB::table('category_parents')->where('id_parent', $category->id_category)->count();
        if ($childCount > 0) {
            return redirect()->route('category.index')->with('error', 'Gagal: Kategori ini memiliki subkategori. Hapus subkategori terlebih dahulu.');
        }

        $deleteProducts = $request->input('delete_products') == '1';
        $this->deleteCategoryData($category, $deleteProducts);

        return redirect()->route('category.index')->with('success', 'Kategori berhasil dihapus.');
    }

    /**
     * Bulk delete categories
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->route('category.index')->with('error', 'Tidak ada kategori yang dipilih.');
        }

        $deleteProducts = $request->input('delete_products') == '1';
        $categories = DB::table('categories')->whereIn('id', $ids)->get();
        $deletedCount = 0;

        foreach ($categories as $category) {
            $childCount = DB::table('category_parents')->where('id_parent', $category->id_category)->count();
            if ($childCount == 0) {
                $this->deleteCategoryData($category, $deleteProducts);
                $deletedCount++;
            }
        }

        if ($deletedCount < count($categories)) {
            return redirect()->route('category.index')->with('success', $deletedCount . ' kategori berhasil dihapus. Beberapa dilewati karena memiliki subkategori.');
        }

        return redirect()->route('category.index')->with('success', $deletedCount . ' kategori berhasil dihapus.');
    }

    /**
     * Helper to delete category and its relations/images, and optionally products
     */
    private function deleteCategoryData($category, $deleteProducts = false)
    {
        if ($deleteProducts) {
            // Get all products that belong to this category
            $productIds = DB::table('product_categories')
                ->where('id_category', $category->id_category)
                ->pluck('id_product');

            if ($productIds->isNotEmpty()) {
                // Delete product images physically
                $productImages = DB::table('images')->whereIn('id_product', $productIds)->get();
                foreach ($productImages as $img) {
                    $filename = $img->id_product . '-' . $img->id_image . '.jpg';
                    if (Storage::disk('public')->exists('product/' . $filename)) {
                        Storage::disk('public')->delete('product/' . $filename);
                    }
                    $publicPath = public_path('images/product/' . $filename);
                    if (File::exists($publicPath)) {
                        File::delete($publicPath);
                    }
                }

                // Delete from product-related tables
                DB::table('images')->whereIn('id_product', $productIds)->delete();
                DB::table('product_categories')->whereIn('id_product', $productIds)->delete();
                
                // Get internal IDs for deleting from 'products' table
                $products = DB::table('products')->whereIn('id_product', $productIds)->pluck('id');
                DB::table('products')->whereIn('id', $products)->delete();
            }
        } else {
            // Just delete the relation to this category
            DB::table('product_categories')->where('id_category', $category->id_category)->delete();
        }

        // Delete category image physically
        $filename = $category->id_category . '.jpg';
        if (Storage::disk('public')->exists('category/' . $filename)) {
            Storage::disk('public')->delete('category/' . $filename);
        }
        $publicPath = public_path('images/category/' . $filename);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
        }

        // Delete category relationships
        DB::table('category_parents')->where('id_category', $category->id_category)->delete();
        DB::table('category_parents')->where('id_parent', $category->id_category)->delete();

        // Delete category itself
        DB::table('categories')->where('id', $category->id)->delete();
    }
}