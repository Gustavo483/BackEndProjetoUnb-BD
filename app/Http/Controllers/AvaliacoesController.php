<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

class AvaliacoesController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getDenunciasProfessor()
    {
        $sql = 'select * from vw_denunciasprofessores';
        $denunciasProfessores = DB::select($sql);

        $response = [
            'denunciasProfessores' => $denunciasProfessores,
        ];

        return response($response, 201);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getDenuncias()
    {
        $sql = 'select * from vw_denunciasturmas';
        $denuncias = DB::select($sql);

        $response = [
            'denuncias' => $denuncias,
        ];

        return response($response, 201);
    }

    /**
     * @param $id_denuncia
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function ignorarDenuncia($id_denuncia)
    {
        $sql = 'DELETE FROM denuncias WHERE id_denuncia = '.$id_denuncia;
        DB::delete($sql);

        $response = [
            'message' => 'Denuncia excluída com sucesso',
        ];

        return response($response, 201);
    }

    /**
     * @param $id_denuncia
     * @param $id_avaliacao
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function removerComentario($id_denuncia, $id_avaliacao)
    {
        $sql = 'DELETE FROM denuncias WHERE id_denuncia = '.$id_denuncia;
        $sql2 = 'DELETE FROM avaliacoes WHERE id_avaliacao = '.$id_avaliacao;
        DB::delete($sql);
        DB::delete($sql2);

        $response = [
            'message' => 'Denuncia e comentário excluídos com sucesso',
        ];

        return response($response, 201);
    }

    /**
     * @param $id_denuncia
     * @param $id_avaliacao
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function removerUsuario($id_denuncia, $id_avaliacao, $id_usuario)
    {
        $queryResult = DB::select('CALL DeletarUsuario(?, ?, ?)',[$id_denuncia, $id_avaliacao,$id_usuario]);

        $response = [
            'message' => 'Comando executado com sucesso',
        ];

        return response($response, 201);
    }

    /**
     * @param $id_avaliacao
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function denunciarAvaliacao($id_avaliacao, $id_usuario)
    {
        $dados = '"'.$id_avaliacao.'","'.$id_usuario.'","'.date('Y-m-d').'"';
        $sql = 'INSERT INTO denuncias (id_avaliacao,id_usuario,dt_cadastro) VALUES('.$dados.')';

        DB::insert($sql);
        $response = [
            'message'=> 'Denuncia cadastrada com sucesso'
        ];
        return response($response, 201);
    }

    /**
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getAvaliacoesProfessoresUsuario($id_usuario)
    {
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

    /**
     * @param Request $request
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function criarAvaliacaoProfessor(Request $request, $id_usuario)
    {
        $dados = '"'.$request->int_estrelas.'","'.$request->st_avaliacao.'","'.$request->id_professor.'","'.$id_usuario.'","'.date('Y-m-d').'"';
        $sql = 'INSERT INTO avaliacoes (int_estrelas,st_avaliacao, st_nomeProfessor,id_usuario,dt_cadastro) VALUES('.$dados.')';

        DB::insert($sql);
        $response = [
            'message'=> 'Avaliação cadastrada com sucesso'
        ];
        return response($response, 201);
    }

    /**
     * @param $id_departamento
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getProfessores($id_departamento)
    {
        $QueryProfessores = 'SELECT st_nomeProfessor FROM professores where id_codDepartamento = '.$id_departamento;
        $QueryAvaliacoes = 'SELECT a.id_avaliacao, int_estrelas,st_avaliacao,st_nomeProfessor,id_denuncia FROM avaliacoes as a LEFT JOIN denuncias as d on d.id_avaliacao = a.id_avaliacao
                           where st_nomeProfessor in ( SELECT st_nomeProfessor FROM professores where id_codDepartamento = '.$id_departamento.')';
        $professores = DB::select($QueryProfessores);
        $avaliacoes = DB::select($QueryAvaliacoes);

        $response = [
            'professores' => $professores,
            'avaliacoes' => $avaliacoes,
        ];

        return response($response, 201);
    }

    /**
     * @param $id_departamento
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getDisciplinas($id_departamento)
    {
        $sql = 'SELECT DISTINCT id_codDisciplina, st_nomeDisciplina FROM disciplinas WHERE id_codDepartamento ="'.$id_departamento.'"';

        $disciplinas = DB::select($sql);

        $response = [
            'disciplinas' => $disciplinas,
        ];

        return response($response, 201);
    }

    /**
     * @param $id_avaliacao
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
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

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getAllDepartamento()
    {
        $sql = 'SELECT * FROM departamentos';

        $departamentos = DB::select($sql);

        $response = [
            'departamentos' => $departamentos,
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function updateAvaliacao(Request $request)
    {
        $sql = 'UPDATE avaliacoes set st_avaliacao = "'.$request->st_avaliacao.'", int_estrelas = '.$request->int_estrelas.' where id_avaliacao = '.$request->id_avaliacao;
        DB::update($sql);
        $response = [
            'message' => "Avaliação atualizada com sucesso",

        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function filtrarTurmas(Request $request)
    {
        $sql = 'SELECT * FROM vw_turmasavaliacao where id_codDisciplina = "'
            .$request->id_codDisciplina.
            '" and st_periodoLetivo = "'
            .$request->st_periodoletivo.'"';

        $sql2 = 'SELECT id_turma,st_horario,st_nomeProfessor,st_nomeDisciplina,st_turma FROM turmas
            join disciplinas on turmas.id_codDisciplina = disciplinas.id_codDisciplina
            where id_turma in ( SELECT distinct id_turma FROM turmas where id_codDisciplina = "' .
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

    /**
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function getAvaliacoesUsuario($id_usuario)
    {
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

    /**
     * @param Request $request
     * @param $id_usuario
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function criarAvaliacao(Request $request,$id_usuario)
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

        $dados = '"'.$request->int_estrelas.'","'.$request->st_avaliacao.'","'.$request->id_turma.'","'.$id_usuario.'","'.date('Y-m-d').'"';
        $sql = 'INSERT INTO avaliacoes (int_estrelas,st_avaliacao, id_turma,id_usuario,dt_cadastro) VALUES('.$dados.')';
        DB::insert($sql);
        $response = [
            'message'=> 'Avaliação cadastrada com sucesso'
        ];
        return response($response, 201);
    }

    /**
     * @param $id_avaliacao
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
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
