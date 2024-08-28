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

Route::group([
    // 'prefix' => 'map',
    'as' => 'map.',
    'controller' => MapController::class,
], function () {
    Route::get('/point/{point}/{coordinates?}', 'point')->name('point');
    Route::get('/material/{material}/{coordnates?}', 'material')->name('material');
    Route::get('/search/{coordinates}', 'search')->name('search');
    Route::get('/suggest/{coordinates}', 'suggest')->name('suggest');
    Route::get('/{coordinates?}', 'index')->name('index');
});

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::prefix('cont')->middleware('auth:web')->group(function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/update', [UserController::class, 'update'])->name('profile.update');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('profile.change-password');
});
