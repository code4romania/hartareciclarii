<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\SubmitController;
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
    'middleware' => ['auth'],
    'controller' => AccountController::class,
], function () {
    Route::get('/', 'dashboard')->name('dashboard');
    Route::get('/settings', 'settings')->name('settings');
    Route::post('/profile', 'profile')->name('profile');
    Route::post('/password', 'password')->name('password');
});

Route::group([
    'prefix' => 'submit',
    'as' => 'submit.',
    'controller' => SubmitController::class,
], function () {
    Route::middleware(HandlePrecognitiveRequests::class)->group(function () {
        Route::post('/point', 'point')->name('point');
        Route::post('/report/{point}', 'report')->name('report');
    });

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

    Route::get('/point/{point}/{coordinates?}/report', 'report')->name('report')->middleware('auth');
    Route::get('/point/{point}/{coordinates?}', 'point')->name('point');
    Route::get('/material/{material}/{coordnates?}', 'material')->name('material');
    Route::get('/search/{coordinates}', 'search')->name('search');
    Route::get('/{coordinates?}', 'index')->name('index');
});
