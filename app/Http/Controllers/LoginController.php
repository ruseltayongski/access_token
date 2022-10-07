<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{

    public function invalidAccessToken() {
        return response([
            'error' => 'Unauthorized',
            'message' => 'Full authentication is required to access this resource',
            'status' => '401'
        ]);
    }

    public function getUserProfile() {
        return Auth::user();
    }

    public function login(Request $request) {
        $login = [
            'username' => $request->username,
            'password' => $request->password
        ];

        if( !Auth::attempt($login) ) {
            return response([
                'message' => 'Invalid Login Credentials'
            ]);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response([
            'access_token' => $accessToken
        ]);

    }
}
