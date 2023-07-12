<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class AvaliacoesController extends Controller
{
    public function getAvaliacoesProfessoresUsuario()
    {
        $id_usuario = auth()->user()->id;
        $QueryProfessores = 'SELECT st_nomeProfessor FROM professores where st_nomeProfessor in (SELECT st_nomeProfessor FROM avaliacoes where id_usuario = '.$id_usuario.')';
        $QueryAvaliacoes = 'SELECT int_estrelas,st_avaliacao,id_avaliacao,st_nomeProfessor FROM avaliacoes where id_usuario = '.$id_usuario;
        $professores = DB::select($QueryProfessores);
        $avaliacoes = DB::select($QueryAvaliacoes);

        $response = [
            'professores' => $professores,
            'avaliacoes' => $avaliacoes,
        ];

        return response($response, 201);
    }

    public function criarAvaliacaoProfessor(Request $request)
    {
        $id_usuario = auth()->user()->id;
        $dados = '"'.$request->int_estrelas.'","'.$request->st_avaliacao.'","'.$request->id_professor.'","'.$id_usuario.'","'.date('Y-m-d').'"';
        $sql = 'INSERT INTO avaliacoes (int_estrelas,st_avaliacao, st_nomeProfessor,id_usuario,dt_cadastro) VALUES('.$dados.')';

        DB::insert($sql);
        $response = [
            'message'=> 'Avaliação cadastrada com sucesso'
        ];
        return response($response, 201);
    }
    public function getProfessores($id_departamento)
    {
        $QueryProfessores = 'SELECT st_nomeProfessor FROM professores where id_codDepartamento = '.$id_departamento;
        $QueryAvaliacoes = 'SELECT int_estrelas,st_avaliacao,id_avaliacao,st_nomeProfessor FROM avaliacoes where st_nomeProfessor in ( SELECT st_nomeProfessor FROM professores where id_codDepartamento = '.$id_departamento.') order by dt_cadastro';
        $professores = DB::select($QueryProfessores);
        $avaliacoes = DB::select($QueryAvaliacoes);

        $response = [
            'professores' => $professores,
            'avaliacoes' => $avaliacoes,
        ];

        return response($response, 201);

    }
    public function getDisciplinas($id_departamento)
    {
        $sql = 'SELECT DISTINCT id_codDisciplina, st_nomeDisciplina FROM disciplinas WHERE id_codDepartamento ="'.$id_departamento.'"';

        $disciplinas = DB::select($sql);

        $response = [
            'disciplinas' => $disciplinas,
        ];

        return response($response, 201);
    }

    public function DeleteAvaliacao($id_avaliacao)
    {
        $sql = 'DELETE FROM avaliacoes WHERE id_avaliacao = '.$id_avaliacao
        ;
        DB::delete($sql);

        $response = [
            'message' => 'Avaliação excluída com sucesso',
        ];

        return response($response, 201);
    }

    public function getAllDepartamento()
    {
        $sql = 'SELECT * FROM departamentos';

        $departamentos = DB::select($sql);

        $response = [
            'departamentos' => $departamentos,
        ];

        return response($response, 201);
    }

    public function updateAvaliacao(Request $request)
    {
        $sql = 'UPDATE avaliacoes set st_avaliacao = "'.$request->st_avaliacao.'", int_estrelas = '.$request->int_estrelas.' where id_avaliacao = '.$request->id_avaliacao;
        DB::update($sql);
        $response = [
            'message' => "Avaliação atualizada com sucesso",

        ];

        return response($response, 201);
    }

    public function filtrarTurmas(Request $request)
    {
        $sql = 'SELECT * FROM vw_turmasavaliacao where id_codDisciplina = "'
            .$request->id_codDisciplina.
            '" and st_periodoLetivo = "'
            .$request->st_periodoletivo.'"';

        $sql2 = 'SELECT id_turma,st_horario,st_nomeProfessor,st_nomeDisciplina,st_turma FROM turmas
            join disciplinas on turmas.id_codDisciplina = disciplinas.id_codDisciplina
            where id_turma in ( SELECT distinct id_turma FROM vw_turmasavaliacao where id_codDisciplina = "' .
            $request->id_codDisciplina . '" and st_periodoLetivo = "' . $request->st_periodoletivo . '")';

        $id_turmas = DB::select($sql2);
        $turmas = DB::select($sql);

        $response = [
            'bl_encontrouTurmas' => count($turmas) > 0 ? 1 : 0,
            'turmas' => $turmas,
            'id_turmas'=>$id_turmas,
        ];

        return response($response, 201);
    }

    public function getAvaliacoesUsuario()
    {
        $id_usuario = auth()->user()->id;
        $sql = 'SELECT * FROM vw_turmasavaliacao where id_usuario = '.$id_usuario;

        $sql2 = 'SELECT id_turma,st_horario,st_nomeProfessor,st_nomeDisciplina,st_turma FROM turmas
            join disciplinas on turmas.id_codDisciplina = disciplinas.id_codDisciplina
            where id_turma in ( SELECT distinct id_turma FROM vw_turmasavaliacao where id_usuario = '.$id_usuario.')';

        $id_turmas = DB::select($sql2);
        $turmas = DB::select($sql);

        $response = [
            'bl_encontrouTurmas' => count($turmas) > 0 ? 1 : 0,
            'turmas' => $turmas,
            'id_turmas'=>$id_turmas,
        ];

        return response($response, 201);

    }

    public function criarAvaliacao($id_turma, Request $request)
    {
        // Validando os dados do request
        $rules = [
            'int_estrelas' => 'required',
            'st_avaliacao' => 'required',
        ];

        $messages = [
            'required' =>'O campo deve ser preenchido',
        ];

        $request->validate($rules, $messages);

        $id_usuario = auth()->user()->id;

        $dados = '"'.$request->int_estrelas.'","'.$request->st_avaliacao.'","'.$id_turma.'","'.$id_usuario.'","'.date('Y-m-d').'"';
        $sql = 'INSERT INTO avaliacoes (int_estrelas,st_avaliacao, id_turma,id_usuario,dt_cadastro) VALUES('.$dados.')';
        DB::insert($sql);
        $response = [
            'message'=> 'Avaliação cadastrada com sucesso'
        ];
        return response($response, 201);
    }

    public function editarAvaliacao($id_avaliacao)
    {
        $sql = 'SELECT * FROM avaliacoes as a
                    JOIN turmas as t on a.id_turma = t.id_turma
                    JOIN departamentos as d on t.id_codDepartamento = d.id_codDepartamento
                    WHERE id_avaliacao = '.$id_avaliacao;

        $avaliacao = DB::select($sql);

        $response = [
            'bl_encontrouAvaliacao' => count($avaliacao) > 0 ? 1 : 0,
            'turmas' => $avaliacao,
        ];

        return response($response, 201);
    }
}
