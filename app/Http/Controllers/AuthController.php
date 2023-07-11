<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function updateUsuario(User $id_usuario,Request $request)
    {
        if(isset($request->password)){
            // Validando os dados do request
            $rules = [
                'name' => 'required|string|min:8',
                'email' => 'required|string|email',
                'password' => 'required|string|confirmed',
            ];

            $messages = [
                'required' =>'O campo deve ser preenchido',
                'name.min' =>'O nome deve ter no mínimo 8 caracteres',
                'email.unique'=>'O email já está em uso',
                'password.confirmed' =>'As senhas não conferem'
            ];
        }
        if(!isset($request->password)){
            // Validando os dados do request
            $rules = [
                'name' => 'required|string|min:8',
                'email' => 'required|string|email',
            ];

            $messages = [
                'required' =>'O campo deve ser preenchido',
                'name.min' =>'O nome deve ter no mínimo 8 caracteres',
                'email.unique'=>'O email já está em uso',
            ];
        }

        $request->validate($rules, $messages);

        $id_usuario->update($request->all());

        $response = [
            'message' => 'Usuário cadastrado com sucesso.'
        ];
        return response($response, 201);
    }

    public function teste()
    {

        $response = [
            'usuarios' => AuthResource::collection(User::all()),
            'mensagem' => 'usuarios encontrados'
        ];
        return response($response, 201);
    }

    /**
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
            'required' =>'O campo deve ser preenchido',
            'name.min' =>'O nome deve ter no mínimo 8 caracteres',
            'email.unique'=>'O email já está em uso',
            'password.confirmed' =>'As senhas não conferem'
        ];

        $request->validate($rules, $messages);

        // Criando o usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $response = [
            'message' => 'Usuário cadastrado com sucesso',
            'user' => $user,
        ];

        return response($response, 201);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validando os dados do usuario para realizar o login
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = [
                'errors' => 'Dados informados estão inválidos',
            ];
            return response($response, 422);
        }

        $token = $user->createToken($user->name)->plainTextToken;

        $response = [
            'message' => 'Usuario autenticado com sucesso',
            'user' => $user,
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


    public function editUsuario()
    {
        $response = [
            'usuario' => auth()->user(),
        ];

        return response($response, 201);

    }

}
