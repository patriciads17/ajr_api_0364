<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\DetailShift;
use Validator;
use Carbon\Carbon;

class DetailShiftController extends Controller
{
    public function index()
    {
        $details = DetailShift::all();
        if(count($details)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $details
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function getShiftEmployee($id)
    {
        $shift = DetailShift::where('id_pegawai', $id)->get('id_shift');
        if(count($shift)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $shift
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function countShiftEmployee($id)
    {
        $shift = DetailShift::where('id_pegawai', $id)->get('id_shift');
        if(count($shift)>5){
            return response([
                'message' => 'Already past the weekly shift limit!',
                'data' => false,
            ],200);
        }else{
            return response([
                'data' => true,
            ],200);
        }
    }

    public function store(Request $request)
    {
        $newDetail = $request->all();
        $validate = Validator::make($newDetail,[
            'id_pegawai' => 'required',
            'id_shift' => 'required',
        ]);

        $newDetail['id'] = IdGenerator::generate(['table' => 'detail_shifts', 'length' => 7, 'prefix' => 'SHF-']);
        
        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $detail = DetailShift::create($newDetail);
        return response([
            'message' => 'Add Detail Shift Success',
            'data' => $detail
        ], 200);
    }


    public function show($id)
    {
        $detail = DetailShift::find($id);

        if(!is_null($detail)){
            return response([
                'message' => 'Retrieve Detail Shift Success',
                'data' => $detail
            ],200);
        }

        return response([
            'message' => 'Detail Shift Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request, $id)
    {
        $detail = DetailShift::find($id);

        if(is_null($detail)){
            return response([
                'message' => 'Detail Shift Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();
        $validate = Validator::make($dataUpdate,[
            'id_pegawai' => 'required',
            'id_shift' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $detail->id_pegawai = $dataUpdate['id_pegawai'];
        $detail->id_shift = $dataUpdate['id_shift'];

        if($detail->save()){
            return response([
                'message' => 'Update Detail Shift Success',
                'data' => $detail
            ],200);
        }

        return response([
            'message' => 'Update Detail Shift Failed',
            'data' => null
        ],400);
    }

    public function destroy($id)
    {
        $detail = DetailShift::find($id);

        if(is_null($detail)){
            return response([
                'message' => 'Detail Shift Not Found',
                'data' => null
            ],404);
        }

        if($detail->delete()){
            return response([
                'message' => 'Delete Detail Shift Success!',
                'data' => $detail
            ],200);
        }

        return response([
            'message' => 'Delete Detail Shift Failed!',
            'data' => null
        ],400);
    }
}
