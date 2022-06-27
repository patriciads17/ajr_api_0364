<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rule;
use Validator;


class EmployeeController extends Controller
{

    public function indexEmployee()
    {
        $employees = Employee::where('idRole','CSV')->orWhere('idRole','MGR')->orWhere('idRole','ADM')->get();

        if(count($employees)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $employees
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function indexOwner()
    {
        $owners = Employee::where('idRole','OWN')->get();

        if(count($owners)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $owners
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function getNameEmployee($id)
    {
        $employees = Employee::where('idRole', $id)->pluck('nama_pegawai');
        if(count($employees)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $employees
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function getIdEmployee($id)
    {
        $employee = Employee::where('nama_pegawai', $id)->pluck('id');
        if(count($employee)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function getEmployee($id)
    {
        $employee = Employee::where('id', $id)->get();
        if(count($employee)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function store(Request $request)
    {
        $newEmployee = $request-> all();

        $validate = Validator::make($newEmployee,[
            'nama_pegawai' => 'required',
            'idRole' => 'required',
            'alamat_pegawai' => 'required',
            'tgl_lahir_pegawai' => 'required|date|date_format:Y-m-d',
            'gender_pegawai' => 'required|in:Male,Female',
            'no_telp_pegawai' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'email' => 'required|email:rfc,dns|unique:employees',
        ]);

        if($newEmployee['idRole'] == 'OWN'){
            $prefix = 'OWN-';
            $length = 7;
        }

        if($newEmployee['idRole'] == 'MGR'){
            $prefix = 'MGR-';
            $length = 7;
        }
        
        else if($newEmployee['idRole'] == 'ADM'){
            $prefix = 'ADM-';
            $length = 7;
        }
        
        else if($newEmployee['idRole'] == 'CSV'){
            $prefix = 'CSV-';
            $length = 7;
              
        };
        $idEmp =IdGenerator::generate(['table' => 'employees', 'length' => $length, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);
        $newEmployee['id'] = $idEmp;
        
        $newEmployee['password'] = bcrypt($newEmployee['tgl_lahir_pegawai']);

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $employee = Employee::create($newEmployee);
        return response([
            'message' => 'Add Employee Success',
            'data' => $employee
        ], 200);
    }

    public function show($id)
    {
        $employee = Employee::find($id);

        if(!is_null($employee)){
            return response([
                'message' => 'Retrieve Employee Success',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Employee Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request)
    {
        $employee = Employee::find($request->user()->id);
       
        if(is_null($employee)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();

        $validate = Validator::make($dataUpdate, [
            'nama_pegawai' => 'required',
            'alamat_pegawai' => 'required',
            'tgl_lahir_pegawai' => 'required|date|date_format:Y-m-d',
            'gender_pegawai' => 'required|in:Male,Female',
            'no_telp_pegawai' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'email' => [
                'required', 'email:rfc,dns',
                Rule::unique('employees')->ignore($employee->id),
            ],
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        if($request->file('url_foto_pegawai')){
            $validate = Validator::make($dataUpdate, [
                'url_foto_pegawai' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_foto_pegawai')->store('employee/profilepic','public');
            $employee->url_foto_pegawai = $request->file('url_foto_pegawai')->store('employee/profilepic','public');
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
            $employee->password = bcrypt($request->password);
        }

        $employee->nama_pegawai = $dataUpdate['nama_pegawai'];
        $employee->alamat_pegawai = $dataUpdate['alamat_pegawai'];
        $employee->tgl_lahir_pegawai = $dataUpdate['tgl_lahir_pegawai'];
        $employee->gender_pegawai = $dataUpdate['gender_pegawai'];
        $employee->no_telp_pegawai = $dataUpdate['no_telp_pegawai'];
        $employee->email = $dataUpdate['email'];

        if($employee->save()){
            return response([
                'message' => 'Update Employee Success',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Update Employee Failed',
            'data' => null
        ],400);

    }

    public function updateByAdmin(Request $request,$id)
    {
        $employee = Employee::find($id);
        if(is_null($employee)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ],404);
        }

        $dataUpdate = $request->all();

        $validate = Validator::make($dataUpdate, [
            'nama_pegawai' => 'required',
            'alamat_pegawai' => 'required',
            'tgl_lahir_pegawai' => 'required|date|date_format:Y-m-d',
            'gender_pegawai' => 'required|in:Male,Female',
            'no_telp_pegawai' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $employee->nama_pegawai = $dataUpdate['nama_pegawai'];
        $employee->alamat_pegawai = $dataUpdate['alamat_pegawai'];
        $employee->tgl_lahir_pegawai = $dataUpdate['tgl_lahir_pegawai'];
        $employee->gender_pegawai = $dataUpdate['gender_pegawai'];
        $employee->no_telp_pegawai = $dataUpdate['no_telp_pegawai'];

        if($employee->save()){
            return response([
                'message' => 'Update Employee Success',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Update Employee Failed',
            'data' => null
        ],400);

    }

    public function destroy($id)
    {
        $employee = Employee::find($id);

        if(is_null($employee)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ],404);
        }

        if($employee->delete()){
            return response([
                'message' => 'Delete Employee Success!',
                'data' => $employee
            ],200);
        }

        return response([
            'message' => 'Delete Employee Failed!',
            'data' => null
        ],400);
    }

    
}