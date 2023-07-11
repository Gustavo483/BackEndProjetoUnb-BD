<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvaliacoesController;
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
        Route::put('updateUsuario/{id_usuario}', 'updateUsuario')->name('updateUsuario');
    });

    Route::controller(AvaliacoesController::class)->group(function () {
        Route::post('filtrarTurmas', 'filtrarTurmas')->name('filtrarTurmas');
        Route::post('criarAvaliacao/{id_turma}', 'criarAvaliacao')->name('criarAvaliacao');
        Route::put('updateAvaliacao', 'updateAvaliacao')->name('updateAvaliacao');
        Route::get('editarAvaliacao/{id_avaliacao}', 'editarAvaliacao')->name('editarAvaliacao');
        Route::get('getAllDepartamento', 'getAllDepartamento')->name('getAllDepartamento');
        Route::get('getDisciplinas/{id_departamento}', 'getDisciplinas')->name('getDisciplinas');
        Route::get('getAvaliacoesUsuario/', 'getAvaliacoesUsuario')->name('getAvaliacoesUsuario');
        Route::delete('DeleteAvaliacao/{id_avaliacao}', 'DeleteAvaliacao')->name('DeleteAvaliacao');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});
