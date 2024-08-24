<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/point/{point}/{coordinates?}', [MapController::class, 'point'])->name('point');
Route::get('/material/{material}/{coordinates?}', [MapController::class, 'material'])->name('material');
Route::get('/search', [MapController::class, 'search'])->name('search');
Route::get('/suggest', [MapController::class, 'suggest'])->name('suggest');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::prefix('cont')->middleware('auth:web')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});

// Route::prefix('admin')->group(function () {
//     Route::middleware('auth')->group(function () {
//         Route::get('validate-point/{point_id}', [MapPointsController::class, 'validatePoint'])->name('map-points.validate');
//         Route::get('map-view/{point}', [MapPointsController::class, 'mapView'])->name('map_points.map-view');
//         Route::get('download-xlsx-example', [MapPointsController::class, 'downloadXlsxExample'])->name('import.xlsx-example');
//     });
// });

Route::get('/{coordinates?}', [MapController::class, 'index'])->name('home');
