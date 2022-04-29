<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Shift;


class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();

        if(count($shifts)>0){
            return response([
                'message' => 'Retrieve Data Success!',
                'data' => $shifts
            ],200);
        }

        return response([
            'message' => 'Empty!',
            'data' => null
        ],400);

    }

    public function getIdShift($id,$id2)
    {
        $shifts = Shift::where('hari_shift', $id)->where('jadwal_shift',$id2)->pluck('id');
        if($shifts == null){
            return response([
                'message' => 'Shift Not Found',
                'data' => null
            ],400);
        }
        return response([
            'data' => $shifts
        ],200);

    }

    public function getShiftArray($id)
    {
        $shift = Shift::whereIn('id', [$id])->get();
        
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

    public function getShift($id)
    {
        $shift = Shift::where('id', $id)->get();
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

    public function store(Request $request)
    {
        $newShift = $request->all();

        $validate = Validator::make($newShift, [
            'hari_shift' => 'required|in:Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jadwal_shift' => 'required|in:Sesi 1,Sesi 2'
        ]);

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $shift = Employee::create($newShift);
        return response([
            'message' => 'Add Shift Success',
            'data' => $shift
        ], 200);
    }

    public function show($id)
    {
        $shift = SHift::find($id);

        if(!is_null($shift)){
            return response([
                'message' => 'Retrieve Shift Success',
                'data' => $shift
            ],200);
        }

        return response([
            'message' => 'Shift Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::find($id);

        if(is_null($shift)){
            return response([
                'message' => 'Shift Not Found',
                'data' => $shift
            ],404);
        }

        $dataUpdate = $request->all();
        $validate = Validator::make($dataUpdate, [
            'hari_shift' => 'required|in:Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jadwal_shift' => 'required|in:Sesi 1,Sesi 2'
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $shift->hari_shift = $dataUpdate['hari_shift'];
        $shift->jadwal_shift = $dataUpdate['jadwal_shift'];

        if($shift->save()){
            return response([
                'message' => 'Update Shift Success',
                'data' => $shift
            ],200);
        }

        return response([
            'message' => 'Update Shift Failed',
            'data' => null
        ],400);
    }

    public function destroy($id)
    {
        $shift = Shift::find($id);

        if(is_null($shift)){
            return response([
                'message' => 'Shift Not Found',
                'data' => null
            ],404);
        }

        if($shift->delete()){
            return response([
                'message' => 'Delete Shift Success!',
                'data' => $shift
            ],200);
        }

        return response([
            'message' => 'Delete Shift Failed!',
            'data' => null
        ],400);
    }
}
