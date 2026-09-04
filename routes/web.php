<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicNewsController;
use Illuminate\Support\Facades\Route;

// Public Portal Routes
Route::get('/', [PublicNewsController::class, 'home'])->name('home');
Route::get('/berita', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [PublicNewsController::class, 'show'])->name('news.show');
Route::get('/berita/kategori/{slug}', [PublicNewsController::class, 'category'])->name('news.category');
Route::get('/search', [PublicNewsController::class, 'search'])->name('news.search');

// Authentication routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Admin routes (auth only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('articles', ArticleController::class)->except(['show']);
    });
});
