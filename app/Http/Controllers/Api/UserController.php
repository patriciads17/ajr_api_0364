<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Models\User;
use Carbon\Carbon;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();

        if(count($users)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $users
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $customer = User::find($id);

        if(!is_null($customer)){
            return response([
                'message' => 'Retrieve Customer Succes',
                'data' => $customer
            ],200);
        }

        return response([
            'message' => 'Customer Not Found',
            'data' => null
        ],404);
    }

    public function updateByCs(Request $request,$id)
    {
        $customer = User::find($id);
        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ],404);
        }

        $validate = Validator::make($request->all(), [
            'status_akun' => 'in:Active,Inactive,Deleted',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $customer->status_akun = $request->status_akun;
        
        if($customer->save()){
            return response([
                'message' => 'Update Customer Success',
                'data' => $customer
            ],200);
        }

        return response([
            'message' => 'Update Customer Failed',
            'data' => null
        ],400);
    }

    public function update(Request $request)
    {
        $customer = User::find($request->user()->id);
        if(is_null($customer)){
            return response([
                'message' => 'Customer Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();

        $validate = Validator::make($dataUpdate, [
            'nama_customer' => 'required',
            'alamat_customer' => 'required',
            'tgl_lahir_customer' => 'required|date|date_format:Y-m-d',
            'gender_customer' => 'required|in:Male,Female',
            'no_telp_customer' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'email' => [
                'required', 'email:rfc,dns',
                Rule::unique('users')->ignore($customer->id),
            ],
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        if($request->file('url_tanda_pengenal')){
            $validate = Validator::make($dataUpdate, [
                'url_tanda_pengenal' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_tanda_pengenal')->store('customer/tanda_pengenal','public');
            $customer->url_tanda_pengenal =$request->file('url_tanda_pengenal')->store('customer/tanda_pengenal','public');
        }

        if($request->file('url_sim_customer')){
            $validate = Validator::make($dataUpdate, [
                'url_sim_customer' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_sim_customer')->store('customer/sim','public');
            $customer->url_sim_customer = $request->file('url_sim_customer')->store('customer/sim','public');
        }

        if($request->file('url_pp_customer')){
            $validate = Validator::make($dataUpdate, [

                'url_pp_customer' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_pp_customer')->store('customer/pp','public');
            $customer->url_pp_customer = $request->file('url_pp_customer')->store('customer/pp','public');
        }

        if($request->password){
            $validate = Validator::make($dataUpdate, [
                'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{9,}$/',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $customer->password = bcrypt($request->password);
        }

        $customer->nama_customer = $dataUpdate['nama_customer'];
        $customer->alamat_customer = $dataUpdate['alamat_customer'];
        $customer->tgl_lahir_customer = $dataUpdate['tgl_lahir_customer'];
        $customer->gender_customer = $dataUpdate['gender_customer'];
        $customer->no_telp_customer = $dataUpdate['no_telp_customer'];
        $customer->email = $dataUpdate['email'];

        if($customer->save()){
            return response([
                'message' => 'Update Customer Success',
                'data' => $customer
            ],200);
        }

        return response([
            'message' => 'Update Customer Failed',
            'data' => null
        ],400);
    }

    public function destroy($id)
    {
        //
    }
}


