<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * Atualiza os dados do usuário
     * @param $id_usuario
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function updateUsuario($id_usuario, Request $request)
    {
        if (isset($request->password)) {
            // Validando os dados do request
            $rules = [
                'name' => 'required|string|min:8',
                'email' => 'required|string|email',
                'password' => 'required|string|confirmed',
            ];

            $messages = [
                'required' => 'O campo deve ser preenchido',
                'name.min' => 'O nome deve ter no mínimo 8 caracteres',
                'password.confirmed' => 'As senhas não conferem'
            ];
            $sql = 'UPDATE users set name = "' . $request->name . '",email = "' . $request->email . '", st_curso ="' . $request->st_curso . '", matricula = "' . $request->matricula . '", password ="' . bcrypt($request->password) . '" where id =' . $id_usuario;
        }
        if (!isset($request->password)) {
            // Validando os dados do request
            $rules = [
                'name' => 'required|string|min:8',
                'email' => 'required|string|email',
            ];

            $messages = [
                'required' => 'O campo deve ser preenchido',
                'name.min' => 'O nome deve ter no mínimo 8 caracteres',
            ];

            $sql = 'UPDATE users set name = "' . $request->name . '",email = "' . $request->email . '", st_curso ="' . $request->st_curso . '", matricula = "' . $request->matricula . '" where id =' . $id_usuario;
        }


        $request->validate($rules, $messages);
        DB::update($sql);

        $response = [
            'message' => 'Usuário cadastrado com sucesso.'
        ];
        return response($response, 201);
    }

    /**
     * registra um usuario na base de dados
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validando os dados do request
        $rules = [
            'name' => 'required|string|min:8',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed',
        ];

        $messages = [
            'required' => 'O campo deve ser preenchido',
            'name.min' => 'O nome deve ter no mínimo 8 caracteres',
            'email.unique' => 'O email já está em uso',
            'password.confirmed' => 'As senhas não conferem'
        ];

        $request->validate($rules, $messages);

        $dados = '"' . $request->name . '","' . $request->email . '","' . bcrypt($request->password) . '","' . $request->st_curso . '",' . $request->matricula;
        $sql = 'INSERT INTO users (name,email, password,st_curso,matricula) VALUES(' . $dados . ')';
        DB::insert($sql);

        $response = [
            'message' => 'Usuário cadastrado com sucesso',
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {

        $sql = 'SELECT * FROM users
                    WHERE email = "' . $request->email . '"';

        $user = DB::select($sql);

        if (!$user || !Hash::check($request->password, $user[0]->password)) {
            $response = [
                'errors' => 'Dados informados estão inválidos',
            ];
            return response($response, 422);
        }
        if (!$user[0]->bl_ativo) {
            $response = [
                'errors' => 'Usuário banido',
            ];
            return response($response, 422);
        }

        $user2 = User::where('email', $user[0]->email)->first();
        $token = $user2->createToken($user2->name)->plainTextToken;

        $response = [
            'message' => 'Usuario autenticado com sucesso',
            'usuario' => $user2,
            'token' => $token
        ];

        return response($response, 201);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function logout()
    {
        // Realizando o logout do usuario
        auth()->user()->tokens()->delete();
        $response = [
            'message' => 'Logout efetuado com sucesso',
        ];

        return response($response, 201);
    }

}
