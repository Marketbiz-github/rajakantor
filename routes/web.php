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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
