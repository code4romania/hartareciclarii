<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\StaticController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
 * Public Routes
 */

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/recover', [UserController::class, 'recoverPassword']);
Route::post('/user/recover-confirm', [UserController::class, 'recoverPasswordConfirm']);

Route::get('/static/filters', [StaticController::class, 'filters']);
Route::post('/static/image', [StaticController::class, 'upload']);
Route::post('/static/recapcha', [StaticController::class, 'recapcha']);

Route::get('/map/nomenclatures', [MapController::class, 'nomenclatures']);
Route::get('/map/points', [MapController::class, 'points']);
Route::get('/map/point/{point}', [MapController::class, 'point']);

Route::post('/map/points', [MapController::class, 'create']);
Route::post('/report/problem/{point_id}', [MapController::class, 'report']);

/*
 * Protected Routes
 */
Route::get('/user/profile', [UserController::class, 'profile'])->middleware('auth:sanctum');
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/user/points', [UserController::class, 'points'])->middleware('auth:sanctum');
