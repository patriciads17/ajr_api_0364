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
            'tgl_lahir_customer' => 'required|date|date_format:Y-m-d',
            'alamat_customer' => 'required',
            'no_telp_customer' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'gender_customer' => 'required|in:Male,Female',
            'url_tanda_pengenal' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
        ]);

        $registrationData['id'] = $idCust;

        if($validate->fails())
            return response(['message' => $validate->errors()], 400);
        
        $registrationData['password'] = bcrypt($registrationData['tgl_lahir_customer']);

        if($request->file('url_tanda_pengenal')){
            $request->file('url_tanda_pengenal')->store('customer/tanda_pengenal','public');
            $registrationData['url_tanda_pengenal'] =$request->file('url_tanda_pengenal')->store('customer/tanda_pengenal','public');
        }

        if($request->file('url_sim_customer')){
            $validate = Validator::make($registrationData, [
                'url_sim_customer' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_sim_customer')->store('customer/sim','public');
            $registrationData['url_sim_customer'] = $request->file('url_sim_customer')->store('customer/sim','public');
        }

        if($request->file('url_pp_customer')){
            $validate = Validator::make($registrationData, [

                'url_pp_customer' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_pp_customer')->store('customer/pp','public');
            $registrationData['url_pp_customer'] = $request->file('url_pp_customer')->store('customer/pp','public');
        }

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
        }elseif(Auth::guard('driver')->attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::guard('driver')->user();
            $token = $user->createToken('Authentication Token')->accessToken;
            
            return response([
                'message' => 'Login Success!',
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token
            ]);
        }elseif(Auth::guard('employee')->attempt(['email' => request('email'), 'password' => request('password')] )){
            
            $user = Auth::guard('employee')->user();

            if($user->idRole == 'MGR'){
                $tokenData = $user->createToken('Authentication Token', ['manager']);
            }elseif($user->idRole == 'ADM'){
                $tokenData = $user->createToken('Authentication Token', ['admin']);
            }elseif($user->idRole == 'CSV'){
                $tokenData = $user->createToken('Authentication Token', ['cs']);
            };
            

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
        if(Auth::guard($request->guard)){
            auth()->user()->token()->revoke();
            return response([
                'message' => 'Logout Success!',
            ], 200);
        }elseif(Auth::guard($request->guard)){
            auth()->user()->token()->revoke();
            return response([
                'message' => 'Logout Success!',
            ], 200);
        }elseif(Auth::guard($request->guard)){
            auth()->user()->token()->revoke();
            return response([
                'message' => 'Logout Success!',
            ], 200);
        }else{
            return response([
                'message' => 'Logout Failed!',
            ], 200);
        }
    }
}
