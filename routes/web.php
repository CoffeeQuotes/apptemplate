<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryPreviewController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes (Filament handles its own routes under /admin)
Route::middleware(['auth:admin'])->group(function () {
    // Any additional admin routes that aren't handled by Filament
});

// User routes
Route::middleware(['auth:web'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Gallery routes - accessible by both admin and authenticated users
Route::middleware(['auth:admin,web'])->group(function () {
    Route::get('/galleries/{gallery}/preview', [GalleryPreviewController::class, 'show'])
        ->name('gallery.preview');
});

// Page preview route - accessible by admin users only
Route::middleware(['auth:admin'])->group(function () {
    Route::get('preview/{page:slug}', [PageController::class, 'preview'])
        ->name('page.preview');
});

// Public routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Blog routes - publicly accessible
Route::prefix('blog')->group(function () {
    Route::get('/', [BlogPostController::class, 'index'])->name('blog.index');
    Route::get('/category/{category:slug}', [BlogController::class, 'category'])
        ->name('blog.category');
    Route::get('/{slug}', [BlogPostController::class, 'show'])->name('blog.show');
});

// Admin preview routes - accessible by admin users only
Route::middleware(['auth:admin'])->group(function () {
    Route::get('preview/{page:slug}', [PageController::class, 'preview'])
        ->name('page.preview');
    Route::get('blog/preview/{post:slug}', [BlogPostController::class, 'preview'])
        ->name('blog.preview');
});

// Debug routes - add before the catch-all route
Route::get('/debug-product/{id}', function ($id) {
    $product = \App\Models\Product::with('images')->findOrFail($id);
    dd([
        'product' => $product->toArray(),
        'images' => $product->images->toArray(),
        'storage_path' => storage_path('app/public'),
        'files' => \Illuminate\Support\Facades\Storage::disk('public')->files('products'),
        'image_urls' => $product->images->map(fn($img) => [
            'path' => $img->path,
            'url' => Storage::disk('public')->url($img->path),
            'exists' => Storage::disk('public')->exists($img->path),
        ])->toArray(),
    ]);
});

Route::get('/debug-storage', function () {
    dd([
        'storage_path' => storage_path('app/public'),
        'files' => \Illuminate\Support\Facades\Storage::disk('public')->files('products'),
        'all_files' => \Illuminate\Support\Facades\Storage::disk('public')->allFiles(),
    ]);
});

// This should be the very last route
Route::get('{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '.*');

require __DIR__.'/auth.php';
