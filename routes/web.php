<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryPreviewController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\BlogPostController;
use Illuminate\Support\Facades\Route;

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
    Route::get('/{slug}', [BlogPostController::class, 'show'])->name('blog.show');
});

// Blog category route should be before catch-all
Route::get('/blog/category/{category:slug}', [BlogController::class, 'category'])
    ->name('blog.category');

// Admin preview routes - accessible by admin users only
Route::middleware(['auth:admin'])->group(function () {
    Route::get('preview/{page:slug}', [PageController::class, 'preview'])
        ->name('page.preview');
    Route::get('blog/preview/{post:slug}', [BlogPostController::class, 'preview'])
        ->name('blog.preview');
});

// This should be the very last route
Route::get('{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '.*');

require __DIR__.'/auth.php';
