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
        $pis = PersonalInformation::
              select(
                'personal_information.fname',
                'personal_information.mname',
                'personal_information.lname',
                'personal_information.sex',
                'personal_information.picture',
                'dts.designation.description as designation',
                'dts.division.description as division',
                'dts.section.description as section'
              )
            ->leftJoin('dts.designation','dts.designation.id','=','personal_information.designation_id')
            ->leftJoin('dts.division','dts.division.id','=','personal_information.division_id')
            ->leftJoin('dts.section','dts.section','=','personal_information.section_id')
            ->where('personal_information.employee_status',1)
            ->where('personal_information.userid','!=','admin');
        $keyword = $request->search_keyword;
        if($keyword) {
            $pis = $pis->where(function($q) use ($keyword){
                $q->where('personal_information.fname','like',"%$keyword%")
                    ->orWhere('personal_information.mname','like',"%$keyword%")
                    ->orWhere('personal_information.lname','like',"%$keyword%")
                    ->orWhere('personal_information.userid','like',"%$keyword%")
                    ->orWhereRaw("concat(personal_information.fname,' ',personal_information.lname,', ',personal_information.mname) like '%$keyword%' ")
                    ->orWhereRaw("concat(personal_information.fname,' ',personal_information.lname) like '%$keyword%' ")
                    ->orWhereRaw("concat(personal_information.lname,', ',personal_information.mname) like '%$keyword%' ")
                    ->orWhereRaw("concat(personal_information.fname,', ',personal_information.mname) like '%$keyword%' ");
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
