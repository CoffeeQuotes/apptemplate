<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\GalleryPreviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes (Filament handles its own routes under /admin)
Route::middleware(['auth:admin'])->group(function () {
    // Any additional admin routes that aren't handled by Filament
});

// User routes
Route::middleware(['auth'])->group(function () {
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

require __DIR__.'/auth.php';
