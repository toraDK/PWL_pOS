<?php

use App\Http\Controllers\kategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\BarangController;
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

Route::get('/', [WelcomeController::class, 'index']);

Route::group(['prefix' => 'user'], function(){
    Route::get('/', [UserController::class, 'index']);
    Route::post('/list', [UserController::class, 'list']);
    Route::get('/create', [UserController::class, 'create']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::get('/{id}/edit', [UserController::class, 'edit']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
});

Route::group(['prefix' => 'level'], function(){
    Route::get('/', [LevelController::class, 'index']);
    Route::post('/list', [LevelController::class, 'list']);
    Route::get('/create', [LevelController::class, 'create']);
    Route::post('/', [LevelController::class, 'store']);
    Route::get('/{id}/edit', [LevelController::class, 'edit']);
    Route::put('/{id}', [LevelController::class, 'update']);
    Route::delete('/{id}', [LevelController::class, 'destroy']);
});

Route::group(['prefix' => 'kategori'], function(){
    Route::get('/', [kategoriController::class, 'index']);
    Route::post('/list', [kategoriController::class, 'list']);
    Route::get('/create', [kategoriController::class, 'create']);
    Route::post('/', [kategoriController::class, 'store']);
    Route::get('/{id}/edit', [kategoriController::class, 'edit']);
    Route::put('/{id}', [kategoriController::class, 'update']);
    Route::delete('/{id}', [kategoriController::class, 'destroy']);
});

Route::group(['prefix' => 'barang'], function(){
    Route::get('/', [barangController::class, 'index']);
    Route::post('/list', [barangController::class, 'list']);
    Route::get('/create', [barangController::class, 'create']);
    Route::post('/', [barangController::class, 'store']);
    Route::get('/{id}', [barangController::class, 'show']);
    Route::get('/{id}/edit', [barangController::class, 'edit']);
    Route::put('/{id}', [barangController::class, 'update']);
    Route::delete('/{id}', [barangController::class, 'destroy']);
});