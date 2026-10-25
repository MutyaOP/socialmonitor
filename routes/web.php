<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\AnalyzeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

$admin = RoleMiddleware::class . ':admin';

Route::middleware('auth')->group(function () use ($admin) {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ---------- Тек Admin ----------
    // (/create маршруттары {id} маршруттарынан бұрын тіркелуі тиіс)
    Route::middleware($admin)->group(function () {
        Route::resource('sources', SourceController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['index', 'show']);
        Route::post('categories/reanalyze-all', [CategoryController::class, 'reanalyzeAll'])->name('categories.reanalyze-all');
        Route::post('categories/{category}/keywords', [CategoryController::class, 'storeKeyword'])->name('keywords.store');
        Route::delete('keywords/{keyword}', [CategoryController::class, 'destroyKeyword'])->name('keywords.destroy');

        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    // ---------- Admin + Operator ----------
    Route::resource('sources', SourceController::class)->only(['index']);
    Route::resource('categories', CategoryController::class)->only(['index', 'show']);

    Route::resource('posts', PostController::class)->except(['destroy']);
    Route::patch('posts/{post}/status', [PostController::class, 'status'])->name('posts.status');
    Route::post('posts/{post}/reanalyze', [PostController::class, 'reanalyze'])->name('posts.reanalyze');

    Route::get('analyze', [AnalyzeController::class, 'index'])->name('analyze.index');
    Route::post('analyze', [AnalyzeController::class, 'analyze'])->name('analyze.run');

    Route::get('alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('alerts/scan', [AlertController::class, 'scan'])->name('alerts.scan');
    Route::patch('alerts/{alert}/close', [AlertController::class, 'close'])->name('alerts.close');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
