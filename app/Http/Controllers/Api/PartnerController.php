<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Partner;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PartnerController extends Controller
{
    public function index()
    {
        $partners = Partner::all();
        if(count($partners)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $partners
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function getId($id){
        $idMitra = Partner::where('nama_mitra', $id)->pluck('id');
        if($idMitra == null){
            return response([
                'message' => 'Partner Not Found',
                'data' => null
            ],400);
        }
        return response([
            'data' => $idMitra
        ],200);
    }

    public function getName($id){
        $namaMitra = Partner::where('id', $id)->pluck('nama_mitra');
        if($namaMitra == null){
            return response([
                'message' => 'Partner Not Found',
                'data' => null
            ],400);
        }
        return response([
            'data' => $namaMitra
        ],200);
    }


    public function store(Request $request)
    {
        $newPartner = $request->all();
        $validate = Validator::make($newPartner,[
            'nama_mitra' => 'required|unique:partners',
            'no_ktp_mitra' => 'required|digits:16',
            'alamat_mitra' => 'required',
            'no_telp_mitra' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
        ]);

        $newPartner['id'] = IdGenerator::generate(['table' => 'partners', 'length' => 7, 'prefix' => 'MTR-']);

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $partner = Partner::create($newPartner);
        return response([
            'message' => 'Add Partner Success',
            'data' => $partner
        ], 200);
    }

    public function show($id)
    {
        $partner = Partner::find($id);

        if(!is_null($partner)){
            return response([
                'message' => 'Retrieve Partner Success',
                'data' => $partners
            ],200);
        }

        return response([
            'message' => 'Partner Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::find($id);

        if(is_null($partner)){
            return response([
                'message' => 'Partner Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();

        $validate = Validator::make($dataUpdate,[
            'nama_mitra' => [
                'required',
                Rule::unique('partners')->ignore($partner->id)
            ],
            'no_ktp_mitra' => 'required|digits:16',
            'alamat_mitra' => 'required',
            'no_telp_mitra' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $partner->nama_mitra = $dataUpdate['nama_mitra'];
        $partner->no_ktp_mitra  = $dataUpdate['no_ktp_mitra'];
        $partner->alamat_mitra  = $dataUpdate['alamat_mitra'];
        $partner->no_telp_mitra  = $dataUpdate['no_telp_mitra'];

        if($partner->save()){
            return response([
                'message' => 'Update Partner Success',
                'data' => $partner
            ],200);
        }

        return response([
            'message' => 'Update Partner Failed',
            'data' => null
        ],400);


    }

    public function destroy($id)
    {
        $partner = Partner::find($id);

        if(is_null($partner)){
            return response([
                'message' => 'Partner Not Found',
                'data' => null
            ],404);
        }

        if($partner->delete()){
            return response([
                'message' => 'Delete Partner Success!',
                'data' => $partner
            ],200);
        }

        return response([
            'message' => 'Delete Partner Failed!',
            'data' => null
        ],400);
    }
}
