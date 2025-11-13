<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /**
     * Show the settings edit form.
     */
    public function edit()
    {
        $settings = DB::table('site_settings')->first();
        return view('admin.setting', compact('settings'));
    }

    /**
     * Update or create site settings.
     */
    public function update(Request $request)
    {
        $settings = DB::table('site_settings')->first();

        $validated = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'about' => 'nullable|string',
            'information' => 'nullable|string',
            'wa' => 'nullable|string|max:50',
            'wa_order' => 'nullable|string|max:50',
            'terms' => 'nullable|string',
            'client' => 'nullable|string',
            'home_description' => 'nullable|string',
            // file validations
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg,ico|max:4096',
            'favicon' => 'nullable|image|mimes:jpg,jpeg,png,ico|dimensions:max_width=64,max_height=64|max:1024',
            'slider.*' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'banner_sidebar' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'banner_home_top' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
            'banner_home_bottom' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
        ]);

        $data = [
            'site_name' => $validated['site_name'] ?? null,
            'meta_title' => $validated['meta_title'] ?? null,
            'meta_keywords' => $validated['meta_keywords'] ?? null,
            'meta_description' => $validated['meta_description'] ?? null,
            'about' => $validated['about'] ?? null,
            'information' => $validated['information'] ?? null,
            'wa' => $validated['wa'] ?? null,
            'wa_order' => $validated['wa_order'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'client' => $validated['client'] ?? null,
            'home_description' => $validated['home_description'] ?? null,
        ];

        // helper to store uploaded image to storage/app/public/settings and return path like 'storage/settings/filename.ext'
        $storeToStoragePublic = function ($file, $prefix = '') {
            $ext = $file->getClientOriginalExtension();
            $name = time() . ($prefix ? "_{$prefix}" : '') . '_' . uniqid() . '.' . $ext;
            // store in storage/app/public/settings
            $stored = Storage::disk('public')->putFileAs('settings', $file, $name);
            if ($stored) {
                return 'storage/' . $stored; // accessible via asset('storage/...')
            }
            return null;
        };

        // logo
        if ($request->hasFile('logo')) {
            $path = $storeToStoragePublic($request->file('logo'), 'logo');
            if ($path) $data['logo'] = $path;
        }

        // favicon
        if ($request->hasFile('favicon')) {
            $path = $storeToStoragePublic($request->file('favicon'), 'favicon');
            if ($path) $data['favicon'] = $path;
        }

        // banners
        if ($request->hasFile('banner_sidebar')) {
            $p = $storeToStoragePublic($request->file('banner_sidebar'), 'banner_sidebar');
            if ($p) $data['banner_sidebar'] = $p;
        }
        if ($request->hasFile('banner_home_top')) {
            $p = $storeToStoragePublic($request->file('banner_home_top'), 'banner_home_top');
            if ($p) $data['banner_home_top'] = $p;
        }
        if ($request->hasFile('banner_home_bottom')) {
            $p = $storeToStoragePublic($request->file('banner_home_bottom'), 'banner_home_bottom');
            if ($p) $data['banner_home_bottom'] = $p;
        }

        // slider (multiple)
        if ($request->hasFile('slider')) {
            $sliderFiles = $request->file('slider');
            $sliderPaths = [];
            foreach ($sliderFiles as $f) {
                if ($f && $f->isValid()) {
                    $p = $storeToStoragePublic($f, 'slider');
                    if ($p) $sliderPaths[] = $p;
                }
            }
            // only set slider if we have uploaded files; otherwise keep existing
            if (count($sliderPaths) > 0) {
                $data['slider'] = json_encode($sliderPaths);
            }
        }

        // if there's an existing settings row, update it; otherwise insert
        if ($settings) {
            $data['updated_at'] = now();
            DB::table('site_settings')->where('id', $settings->id)->update($data);
        } else {
            $data['created_at'] = now();
            $data['updated_at'] = now();
            DB::table('site_settings')->insert($data);
        }

        return redirect()->route('settings.edit')->with('success', 'Site settings saved.');
    }
}