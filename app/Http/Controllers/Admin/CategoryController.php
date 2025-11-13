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
    public function index()
    {
        $categories = DB::table('categories')
            ->leftJoin('category_parents', 'categories.id_category', '=', 'category_parents.id_category')
            ->whereIn('categories.status', [1, 2])
            ->orderBy('categories.updated_at', 'desc')
            ->select(
                'categories.id',
                'categories.id_category',
                'categories.name',
                'categories.slug',
                'categories.status',
                'categories.updated_at'
            )
            ->get();

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

        // dd($category, $parentCategory);

        return view('admin.category-edit', compact('category', 'categories', 'parentCategory'));
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
            'status' => 'required|in:1,2',
        ];

        $validated = $request->validate($rules);

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
}