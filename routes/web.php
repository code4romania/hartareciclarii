<?php

declare(strict_types=1);

use App\Http\Controllers\MapController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
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
    'prefix' => 'account',
    'as' => 'account.',
    'middleware' => ['auth', 'verified'],
    'controller' => UserController::class,
], function () {
    Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [UserController::class, 'edit'])->name('settings');
});

Route::group([
    'prefix' => 'submit',
    'as' => 'submit.',
    'controller' => SubmitController::class,
], function () {
    Route::post('/point', 'point')->name('point')->middleware(HandlePrecognitiveRequests::class);
    Route::post('/image', 'image')->name('image');
    Route::delete('/image/{media:uuid}', 'deleteImage')->name('deleteImage');
});

Route::group([
    // 'prefix' => 'map',
    'as' => 'map.',
    'controller' => MapController::class,
], function () {
    Route::get('/suggest/{coordinates?}', 'suggest')->name('suggest');
    Route::get('/reverse/{coordinates?}', 'reverse')->name('reverse');

    Route::get('/point/{point}/{coordinates?}', 'point')->name('point');
    Route::get('/material/{material}/{coordnates?}', 'material')->name('material');
    Route::get('/search/{coordinates}', 'search')->name('search');
    Route::get('/{coordinates?}', 'index')->name('index');
});
