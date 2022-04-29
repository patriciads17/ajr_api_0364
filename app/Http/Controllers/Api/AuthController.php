<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\User;
use App\Models\Employee;
use Validator;
use Carbon\Carbon;
use Laravel\Passport\TokenRepository;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();

        $joinDate = substr(str_replace( '-', '', Carbon::now()->format('y-m-d')), 0,8);
        $prefix = 'CUS'.$joinDate.'-';
        $idCust = IdGenerator::generate(['table' => 'users', 'length' => 13, 'prefix' => $prefix]);
        
        $validate = Validator::make($registrationData, [
            'nama_customer' => 'required|min:1',
            'email' => 'required|email:rfc,dns|unique:users',
            'tgl_lahir_customer' => 'required|date',//jgn lupa di UI tetap dibuat saja nama fieldnya tgl lahir.
        ]);

        $registrationData['id'] = $idCust;

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $registrationData['password'] = bcrypt($registrationData['tgl_lahir_customer']);

        $user = User::create($registrationData);
        
        return response([
            'message' => 'Registration Success!',
            'user' => $user
        ],200);
    }
    
    public function login(Request $request)
    {
        $loginData = $request->all();
         $validate = Validator::make($loginData, [
            'email' => 'required|email:rfc,dns',
            'password' => 'required'
         ]);
        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);
    
        if(Auth::guard('user')->attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::guard('user')->user();
            $token = $user->createToken('Authentication Token')->accessToken;
            
            return response([
                'message' => 'Login Success!',
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token
            ]);
        }elseif(Auth::guard('employee')->attempt(['email' => request('email'), 'password' => request('password')] )){
            
            $user = Auth::guard('employee')->user();

            // still not perfectly working
            if($user->idRole == 'MGR'){
                $tokenData = $user->createToken('Authentication Token', ['manager']);
            }elseif($user->idRole == 'ADM'){
                $tokenData = $user->createToken('Authentication Token', ['admin']);
            }elseif($user->idRole == 'CSV'){
                $tokenData = $user->createToken('Authentication Token', ['cs']);
            };
            //

            $token = $tokenData->token;

            if($token->save()){
                return response([
                    'message' => 'Login Success!',
                    'user' => $user,
                    'token_type' => 'Bearer',
                    'access_token' => $tokenData->accessToken,
                    'token_scope' => $tokenData->token->scopes[0],
                ],200);
            }
            
        }else{
            return response([
                'message' => 'Login Failed!'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        if(Auth::guard('user')){
            auth()->user()->token()->revoke();
            return response([
                'message' => 'Logout Success!',
            ], 200);
        }elseif(Auth::guard('employee')){
            auth()->user()->token()->revoke();
            return response([
                'message' => 'Logout Success!',
            ], 200);
        }
    }
}
