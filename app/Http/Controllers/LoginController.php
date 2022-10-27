<?php

namespace App\Http\Controllers;

use App\Models\PersonalInformation;
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

    public function getActiveUser(Request $request) {
        $pis = PersonalInformation::where('employee_status',1);
        $keyword = $request->search_keyword;
        if($keyword) {
            $pis = $pis->where(function($q) use ($keyword){
                $q->where('fname','like',"%$keyword%")
                    ->orWhere('mname','like',"%$keyword%")
                    ->orWhere('lname','like',"%$keyword%")
                    ->orWhere('userid','like',"%$keyword%")
                    ->orWhereRaw("concat(fname,' ',lname,', ',mname) like '%$keyword%' ")
                    ->orWhereRaw("concat(fname,' ',lname) like '%$keyword%' ")
                    ->orWhereRaw("concat(lname,', ',mname) like '%$keyword%' ")
                    ->orWhereRaw("concat(fname,', ',mname) like '%$keyword%' ");
            });
        }
        $pis =  $pis->paginate(15);
        return $pis;
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
