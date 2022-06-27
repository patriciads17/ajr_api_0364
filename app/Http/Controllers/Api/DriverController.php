<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Validator;
use Carbon\Carbon;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();

        if(count($drivers)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $drivers
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function store(Request $request)
    {
        $newDriver = $request->all();
        $validate = Validator::make($newDriver,[
            'nama_driver' => 'required',
            'alamat_driver' => 'required',
            'tgl_lahir_driver' => 'required|date|date_format:Y-m-d',
            'gender_driver' => 'required|in:Male,Female',
            'email' => 'required|email:rfc,dns|unique:drivers',
            'no_telp_driver' => 'required|numeric|regex:/(08)[0-9]{8,11}/',
            'kemampuan_bahasa' => 'required',
            'tarif_harian_driver' => 'required',
            'url_foto_driver' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            'url_sim_driver' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            'url_bebas_napza' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            'url_sehat_jiwa' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            'url_sehat_fisik' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            'url_skck' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $newDriver['password'] = bcrypt($newDriver['tgl_lahir_driver']);

        $joinDate = substr(str_replace( '-', '', Carbon::now()->format('d-m-y')), 0,8);
        $prefix = 'DRV-'.$joinDate;
        $newDriver['id'] = IdGenerator::generate(['table' => 'drivers', 'length' => 13, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

        if($request->file('url_foto_driver')){
            $request->file('url_foto_driver')->store('driver/profilepic','public');
            $newDriver['url_foto_driver'] = $request->file('url_foto_driver')->store('driver/profilepic','public');
        }

        if($request->file('url_sim_driver')){
            $request->file('url_sim_driver')->store('driver/sim','public');
            $newDriver['url_sim_driver'] = $request->file('url_sim_driver')->store('driver/sim','public');
        }

        if($request->file('url_bebas_napza')){
            $request->file('url_bebas_napza')->store('driver/napza','public');
            $newDriver['url_bebas_napza'] = $request->file('url_bebas_napza')->store('driver/napza','public');
        }

        if($request->file('url_sehat_jiwa')){
            $request->file('url_sehat_jiwa')->store('driver/sehatjiwa','public');
            $newDriver['url_sehat_jiwa'] = $request->file('url_sehat_jiwa')->store('driver/sehatjiwa','public');
        }

        if($request->file('url_sehat_fisik')){
            $request->file('url_sehat_fisik')->store('driver/sehatraga','public');
            $newDriver['url_sehat_fisik'] = $request->file('url_sehat_fisik')->store('driver/sehatraga','public');
        }

        if($request->file('url_skck')){
            $request->file('url_skck')->store('driver/skck','public');
            $newDriver['url_skck'] = $request->file('url_skck')->store('driver/skck','public');
        }

        $driver = Driver::create($newDriver);
        
        return response([
            'message' => 'Registration Success',
            'data' => $driver
        ], 200);
    }

    public function show($id)
    {
        $driver = Driver::find($id);

        if(!is_null($driver)){
            return response([
                'message' => 'Retrieve Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Driver Not Found',
            'data' => null
        ],404);
    }

    public function showActiveDriver()
    {
        $drivers = Driver::where('status_ketersediaan_driver', 'Available')->where('status_akun', 'Active')->get();
        if(count($drivers)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $drivers
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function update(Request $request)
    {
        $driver = Driver::find($request->user()->id);

        $updateDriver = $request->all();
        $validate = Validator::make($updateDriver,[
            'nama_driver' => 'required',
            'alamat_driver' => 'required',
            'tgl_lahir_driver' => 'required',
            'gender_driver' => 'required',
            'email' => 'required',
            'no_telp_driver' => 'required',
            'kemampuan_bahasa' => 'required',
            'tarif_harian_driver' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $driver->nama_driver  = $updateDriver['nama_driver'];
        $driver->alamat_driver  = $updateDriver['alamat_driver'];
        $driver->tgl_lahir_driver  = $updateDriver['tgl_lahir_driver'];
        $driver->gender_driver  = $updateDriver['gender_driver'];
        $driver->email  = $updateDriver['email'];
        $driver->no_telp_driver  = $updateDriver['no_telp_driver'];
        $driver->kemampuan_bahasa  = $updateDriver['kemampuan_bahasa'];
        $driver->tarif_harian_driver  = $updateDriver['tarif_harian_driver'];

        if($request->password){
            $validate = Validator::make($updateDriver, [
                'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{9,}$/',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $driver->password = bcrypt($request->password);
        }

        if($request->file('url_foto_driver')){
            $validate = Validator::make($updateDriver, [
                'url_foto_pegawai' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_foto_driver')->store('driver/profilepic','public');
            $driver->url_foto_driver = $request->file('url_foto_driver')->store('driver/profilepic','public');
        }

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ],400);
    }

    public function updatebyAdmin(Request $request)
    {
        $driver = Driver::find($request->id);

        $updateDriver = $request->all();
        $validate = Validator::make($updateDriver,[
            'nama_driver' => 'required',
            'alamat_driver' => 'required',
            'tgl_lahir_driver' => 'required',
            'gender_driver' => 'required',
            'email' => 'required',
            'no_telp_driver' => 'required',
            'kemampuan_bahasa' => 'required',
            'tarif_harian_driver' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $driver->nama_driver  = $updateDriver['nama_driver'];
        $driver->alamat_driver  = $updateDriver['alamat_driver'];
        $driver->tgl_lahir_driver  = $updateDriver['tgl_lahir_driver'];
        $driver->gender_driver  = $updateDriver['gender_driver'];
        $driver->email  = $updateDriver['email'];
        $driver->no_telp_driver  = $updateDriver['no_telp_driver'];
        $driver->kemampuan_bahasa  = $updateDriver['kemampuan_bahasa'];
        $driver->tarif_harian_driver  = $updateDriver['tarif_harian_driver'];

        if($request->password){
            $validate = Validator::make($updateDriver, [
                'password' => 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*([^a-zA-Z\d\s])).{9,}$/',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $driver->password = bcrypt($request->password);
        }

        if($request->file('url_foto_driver')){
            $validate = Validator::make($updateDriver, [
                'url_foto_pegawai' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_foto_driver')->store('driver/profilepic','public');
            $driver->url_foto_driver = $request->file('url_foto_driver')->store('driver/profilepic','public');
        }
        if($request->file('url_sim_driver')){
            $validate = Validator::make($updateDriver, [
                'url_sim_driver' => 'required|image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_sim_driver')->store('driver/sim','public');
            $driver->url_sim_driver = $request->file('url_sim_driver')->store('driver/sim','public');
        }

        if($request->file('url_bebas_napza')){
            $validate = Validator::make($updateDriver, [
                'url_bebas_napza' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_bebas_napza')->store('driver/napza','public');
            $driver->url_bebas_napza = $request->file('url_bebas_napza')->store('driver/napza','public');
        }

        if($request->file('url_sehat_jiwa')){
            $validate = Validator::make($updateDriver, [
                'url_sehat_jiwa' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_sehat_jiwa')->store('driver/sehatjiwa','public');
            $driver->url_sehat_jiwa = $request->file('url_sehat_jiwa')->store('driver/sehatjiwa','public');
        }

        if($request->file('url_sehat_fisik')){
            $validate = Validator::make($updateDriver, [
                'url_sehat_fisik' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_sehat_fisik')->store('driver/sehatraga','public');
            $driver->url_sehat_fisik = $request->file('url_sehat_fisik')->store('driver/sehatraga','public');
        }

        if($request->file('url_skck')){
            $validate = Validator::make($updateDriver, [
                'url_skck' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_skck')->store('driver/skck','public');
            $driver->url_skck = $request->file('url_skck')->store('driver/skck','public');
        }

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ],400);
    }

    public function updateAccountDriver(Request $request, $id)
    {
        $driver = Driver::find($id);
        if(is_null($driver)){
            return response([
                'data' => null
            ],404);
        }

        $driver->status_akun = $request->status_akun;

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ],400);
    }

    public function updateAvailabilityDriver(Request $request, $id)
    {
        $driver = Driver::find($id);
        if(is_null($driver)){
            return response([
                'data' => null
            ],404);
        }

        $driver->status_ketersediaan_driver = $request->status_ketersediaan_driver;

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ],400);
    }

    public function updateAvailabilityDriverMobile(Request $request)
    {
        $driver = Driver::find($request->id);
        if(is_null($driver)){
            return response([
                'message' => 'Driver Not Found',
                'data' => null
            ],404);
        }

        $driver->status_ketersediaan_driver = $request->status_ketersediaan_driver;

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver
            ],200);
        }

        return response([
            'message' => 'Update Driver Failed',
            'data' => null
        ],400);
    }
    
    public function destroy($id)
    {
        //
    }
}
