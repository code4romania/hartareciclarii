<?php

use App\Http\Controllers\MapPointsController;
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
use App\Livewire\UsersReport;

Route::get('/', function ()
{
    return view('welcome');
});
Route::get('/profile', function ()
{
	return view('welcome');
});
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function ()
{
    Route::middleware('auth')->group(function ()
    {
        Route::get('validate-point/{point_id}', [MapPointsController::class, 'validatePoint'])->name('map-points.validate');
        Route::get('map-view/{point}', [MapPointsController::class, 'mapView'])->name('map_points.map-view');
        Route::get('download-xlsx-example', [MapPointsController::class, 'downloadXlsxExample'])->name('import.xlsx-example');
        // dd(Route::get('reports/users', UsersReport::class)->name('reports-users'));
        Route::get('users-reports', UsersReport::class)->name('reports-users');
    });
});

