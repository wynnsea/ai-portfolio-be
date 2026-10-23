<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PageViewController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public (website)
|--------------------------------------------------------------------------
*/

Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('certifications', [CertificationController::class, 'index'])->name('certifications.index');
Route::get('activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('settings', [SettingsController::class, 'show'])->name('settings.show');
Route::post('messages', [MessageController::class, 'store'])->middleware('throttle:messages')->name('messages.store');
Route::post('track', [PageViewController::class, 'store'])->middleware('throttle:track')->name('track');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:login')->name('login');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('me', [Admin\AuthController::class, 'me'])->name('me');
        Route::put('password', [Admin\AuthController::class, 'updatePassword'])->name('password');

        Route::get('dashboard', Admin\DashboardController::class)->name('dashboard');

        $contentResources = [
            'projects' => Admin\ProjectController::class,
            'certifications' => Admin\CertificationController::class,
            'activities' => Admin\ActivityController::class,
            'posts' => Admin\PostController::class,
        ];

        foreach ($contentResources as $name => $controller) {
            Route::patch("{$name}/reorder", [$controller, 'reorder'])->name("{$name}.reorder");
            Route::apiResource($name, $controller);
        }

        Route::apiResource('messages', Admin\MessageController::class)->except('store');

        Route::get('settings', [Admin\SettingsController::class, 'show'])->name('settings.show');
        Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');

        Route::post('uploads', [Admin\UploadController::class, 'store'])->name('uploads.store');
    });
});
