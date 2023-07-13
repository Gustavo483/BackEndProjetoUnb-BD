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
        Route::post('logout', 'logout')->name('logout');
        Route::post('updateUsuario/{id_usuario}', 'updateUsuario')->name('updateUsuario');
    });

    Route::controller(AvaliacoesController::class)->group(function () {
        Route::post('filtrarTurmas', 'filtrarTurmas')->name('filtrarTurmas');
        Route::post('criarAvaliacao/{id_usuario}', 'criarAvaliacao')->name('criarAvaliacao');
        Route::put('updateAvaliacao', 'updateAvaliacao')->name('updateAvaliacao');
        Route::get('editarAvaliacao/{id_avaliacao}', 'editarAvaliacao')->name('editarAvaliacao');
        Route::get('getAllDepartamento', 'getAllDepartamento')->name('getAllDepartamento');
        Route::get('getDisciplinas/{id_departamento}', 'getDisciplinas')->name('getDisciplinas');
        Route::get('getAvaliacoesUsuario/{id_usuario}', 'getAvaliacoesUsuario')->name('getAvaliacoesUsuario');
        Route::delete('DeleteAvaliacao/{id_avaliacao}', 'DeleteAvaliacao')->name('DeleteAvaliacao');
        Route::get('getProfessores/{id_departamento}', 'getProfessores')->name('getProfessores');
        Route::post('criarAvaliacaoProfessor/{id_usuario}', 'criarAvaliacaoProfessor')->name('criarAvaliacaoProfessor');
        Route::post('denunciarAvaliacao/{id_avaliacao}/{id_usuario}', 'denunciarAvaliacao')->name('denunciarAvaliacao');
        Route::get('getAvaliacoesProfessoresUsuario/{id_usuario}', 'getAvaliacoesProfessoresUsuario')->name('getAvaliacoesProfessoresUsuario');
        Route::get('getDenuncias', 'getDenuncias')->name('getDenuncias');
        Route::get('getDenunciasProfessor', 'getDenunciasProfessor')->name('getDenunciasProfessor');
        Route::delete('ignorarDenuncia/{id_denuncia}', 'ignorarDenuncia')->name('ignorarDenuncia');
        Route::delete('removerComentario/{id_denuncia}/{id_avaliacao}', 'removerComentario')->name('removerComentario');
        Route::delete('removerUsuario/{id_denuncia}/{id_avaliacao}/{id_usuario}', 'removerUsuario')->name('removerUsuario');
    });
});

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
});
