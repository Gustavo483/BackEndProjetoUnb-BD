<?php

use App\Http\Controllers\AuthController;
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
Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::controller(AuthController::class)->group(function () {
        Route::get('teste', 'teste')->name('teste');
        Route::post('logout', 'logout')->name('logout');
        Route::get('editUsuario', 'editUsuario')->name('editUsuario');
        Route::put('uptadeUsuario/{id_usuario}', 'uptadeUsuario')->name('uptadeUsuario');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});
