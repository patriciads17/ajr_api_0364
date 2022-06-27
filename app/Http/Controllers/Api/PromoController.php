<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Promo;
use Validator;
use Carbon\Carbon;

class PromoController extends Controller
{
    
    public function index()
    {
        $promos = Promo::all();
        if(count($promos)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $promos
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function indexActivePromo()
    {
        $promos = Promo::where('status_promo', 'Available')->get();
        if(count($promos)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $promos
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showSelectedPromo($id)
    {
        $promo = Promo::where('kode_promo', $id)->pluck('besar_potongan');
        if(count($promo)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $promo
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function indexActiveKodePromo()
    {
        $promos = Promo::where('status_promo', 'Available')->pluck('kode_promo');
        if(count($promos)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $promos
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function store(Request $request)
    {
        $newPromo = $request->all();
        $validate = Validator::make($newPromo,[
            'kode_promo' => 'required',
            'syarat_promo' => 'required',
            'jenis_promo' => 'required',
            'status_promo' => 'required|in:Available,Unavailable',
            'besar_potongan' => 'required|numeric'
        ]);

        $newPromo['id'] = IdGenerator::generate(['table' => 'promos', 'length' => 7, 'prefix' => 'PRM-']);

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $promo = Promo::create($newPromo);
        return response([
            'message' => 'Add Promo Success',
            'data' => $promo
        ], 200);
    }

    public function show($id)
    {
        $promo = Promo::find($id);

        if(!is_null($promo)){
            return response([
                'message' => 'Retrieve Promo Success',
                'data' => $promo
            ],200);
        }

        return response([
            'message' => 'Promo Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request, $id)
    {
        $promo = Promo::find($id);

        if(is_null($promo)){
            return response([
                'message' => 'Promo Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();
        $validate = Validator::make($dataUpdate,[
            'kode_promo' => 'required',
            'syarat_promo' => 'required',
            'jenis_promo' => 'required',
            'status_promo' => 'required|in:Available,Unavailable',
            'besar_potongan' => 'required|numeric'
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $promo->kode_promo  = $dataUpdate['kode_promo'];
        $promo->syarat_promo  = $dataUpdate['syarat_promo'];
        $promo->jenis_promo  = $dataUpdate['jenis_promo'];
        $promo->status_promo  = $dataUpdate['status_promo'];
        $promo->besar_potongan  = $dataUpdate['besar_potongan'];

        if($promo->save()){
            return response([
                'message' => 'Update Promo Success',
                'data' => $promo
            ],200);
        }

        return response([
            'message' => 'Update Promo Failed',
            'data' => null
        ],400);
    }

    public function destroy($id)
    {
        $promo = Promo::find($id);

        if(is_null($promo)){
            return response([
                'message' => 'Promo Not Found',
                'data' => null
            ],404);
        }

        if($promo->delete()){
            return response([
                'message' => 'Delete Promo Success!',
                'data' => $promo
            ],200);
        }

        return response([
            'message' => 'Delete Promo Failed!',
            'data' => null
        ],400);
    }
}
