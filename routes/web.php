<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/category/{slug}', [App\Http\Controllers\CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{slug}', [App\Http\Controllers\ProductController::class, 'show'])->name('product.show');
Route::get('/search', [App\Http\Controllers\ProductController::class, 'search'])->name('product.search');
Route::get('/promo-product', [App\Http\Controllers\ProductController::class, 'promo'])->name('product.promo');

Route::get('/contact', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact');
Route::get('/about-us', [App\Http\Controllers\HomeController::class, 'about'])->name('about');
Route::get('/terms-and-conditions-of-use', [App\Http\Controllers\HomeController::class, 'terms'])->name('terms');
Route::get('/our-clients', [App\Http\Controllers\HomeController::class, 'client'])->name('client');
Route::get('/blog', [App\Http\Controllers\HomeController::class, 'blog'])->name('blog');

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/product', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('product.index');
    Route::get('/product/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{id}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('product.edit');
    Route::put('/product/{id}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('product.update');

    Route::get('/category', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('category.index');
    Route::get('/category/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('category.create');
    Route::post('/category', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('category.store');
    Route::get('/category/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/category/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('category.update');

    Route::put('/settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'update'])->name('settings.update');

    // Settings edit page
    Route::get('/settings', [App\Http\Controllers\Admin\SiteSettingController::class, 'edit'])->name('settings.edit');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
