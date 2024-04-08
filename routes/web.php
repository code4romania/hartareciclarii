<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapPointsController;
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

Route::get('/', HomeController::class)->name('home');

Route::inertia('/1', 'Home');
Route::inertia('/2', 'Home2');

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/profile', function () {
    return view('welcome');
});

Route::get('/reset/{token}', function () {
    return view('welcome');
});

Route::get('/point/{id}', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('validate-point/{point_id}', [MapPointsController::class, 'validatePoint'])->name('map-points.validate');
        Route::get('map-view/{point}', [MapPointsController::class, 'mapView'])->name('map_points.map-view');
        Route::get('download-xlsx-example', [MapPointsController::class, 'downloadXlsxExample'])->name('import.xlsx-example');
    });
});
