<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields=$request->validate([
            'name'=>'required|max:255',
            'email'=> 'required|email|unique:users',
            'password'=>'required|confirmed',
        ]);
        $user= User::create($fields);
        $token= $user->createToken($request->name);

        return [
            'user'=>$user,
            'token'=>$token->plainTextToken
        ];
    }
    public function login(Request $request)
    {
        return 'login';
    }
    public function logout(Request $request)
    {
        return 'logout';
    }
}
