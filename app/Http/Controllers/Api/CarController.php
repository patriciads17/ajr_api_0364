<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all();

        if(count($cars)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $cars
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showCarByType()
    {
        $cars = DB::table('cars')->select('*')->orderBy('tipe_mobil')->get();
        if(count($cars)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data_array' => $cars
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data_array' => null
        ],400);
    }

    public function showAvailableCar()
    {
        $cars = Car::where('ketersediaan_mobil','Available')->get();
        if(count($cars)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $cars
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function store(Request $request)
    {
        $newCar = $request->all();
        
        $validate = Validator::make($newCar,[
            'no_plat' => 'required|regex:/^[A-Z]{1,2}\s{1}\d{1,4}\s{1}[A-Z]{1,3}+$/',
            'nama_mobil' => 'required',
            'jenis_transmisi' => 'required',
            'warna_mobil' => 'required',
            'kapasitas_penumpang' => 'required|numeric',
            'fasilitas_mobil' => 'required',
            'no_stnk' => 'required',
            'kategori_aset' => 'required|in:Company,Partner', //saat di UI letakkan paling atas untuk pengecekan
            'tgl_terakhir_service' => 'required|date|date_format:Y-m-d',
            'ketersediaan_mobil' => 'required|in:Available,Occupied,Unavailable',
            'tarif_harian_mobil' => 'required',
            'vol_bagasi' => 'required|numeric',
            'tipe_mobil' => 'required',
            'url_car_img' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($newCar['kategori_aset'] == 'Partner'){ // di UI pake dropdown lalu pilihannya Partner/Company
            $validate = Validator::make($newCar,[
                'idMitra' => 'required',
                'tgl_mulai_kontrak' => 'required|date|date_format:Y-m-d',
                'tgl_selesai_kontrak' => 'required|date|date_format:Y-m-d',
                'status_kontrak' => 'required',
            ]);
        }

        if($request->file('url_car_img')){
            $request->file('url_car_img')->store('car/carpic','public');
            $newCar['url_car_img'] = $request->file('url_car_img')->store('car/carpic','public');
        }

        $joinDate = substr(str_replace( '-', '', Carbon::now()->format('y-m-d')), 0,8);
        $prefix = $joinDate.'-';
        $idCar = IdGenerator::generate(['table' => 'cars', 'length' => 10, 'prefix' => $prefix]);
        $newCar['id'] = $idCar;

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $car = Car::create($newCar);
        return response([
            'message' => 'Add Car Success',
            'data' => $car
        ], 200);

    }

    public function show($id)
    {
        $car = Car::find($id);

        if(!is_null($car)){
            return response([
                'message' => 'Retrieve Car Success',
                'data' => $car
            ],200);
        }

        return response([
            'message' => 'Car Not Found',
            'data' => null
        ],404);
    }

    public function updateStatusContract(){
        $now = Carbon::now()->format('Y-m-d');
        $cars = Car::where('kategori_aset', 'Partner')->get();
        foreach($cars as $car){
            $finishDay = Carbon::parse($car->tgl_selesai_kontrak);
            $dateleft = Carbon::now()->diffInDays($finishDay);

            if($dateleft <= 30){
                $car->status_kontrak = 'Warning';
            }else if($dateleft == 0){
                $car->status_kontrak = 'Inactive';
            }else{
                $car->status_kontrak = 'Active';
            }
            
            if($car->save()){
                return response([
                    'message' => 'Update Car Success',
                    'data' => $dateleft
                ],200);
            }
            return response([
                'message' => 'Update Car Failed',
                'data' => null
            ],400);
        }
    }

    public function updateAvailabilityCar(Request $request, $id)
    {
        $car = Car::find($id);
        if(is_null($car)){
            return response([
                'data' => null
            ],404);
        }

        $car->ketersediaan_mobil = $request->ketersediaan_mobil;

        if($car->save()){
            return response([
                'message' => 'Update Car Success',
                'data' => $car
            ],200);
        }

        return response([
            'message' => 'Update Car Failed',
            'data' => null
        ],400);
    }

    public function update(Request $request)
    {
        $dataUpdate = $request->all();

        $car = Car::where('id', $dataUpdate['id'])->first();

        $validate = Validator::make($dataUpdate,[
            'no_plat' => 'required|regex:/^[A-Z]{1,2}\s{1}\d{1,4}\s{1}[A-Z]{1,3}+$/',
            'nama_mobil' => 'required',
            'jenis_transmisi' => 'required',
            'jenis_bahan_bakar' => 'required',
            'warna_mobil' => 'required',
            'kapasitas_penumpang' => 'required|numeric',
            'fasilitas_mobil' => 'required',
            'no_stnk' => 'required',
            'kategori_aset' => 'required|in:Company,Partner', //saat di UI letakkan paling atas untuk pengecekan
            'tgl_terakhir_service' => 'required|date',
            'ketersediaan_mobil' => 'required|in:Available,Occupied,Unavailable',
            'tarif_harian_mobil' => 'required',
            'vol_bagasi' => 'required|numeric',
            'tipe_mobil' => 'required',
        ]);

        if($dataUpdate['kategori_aset']=='Partner'){ // di UI pake dropdown lalu pilihannya Partner/Company
            $validate = Validator::make($dataUpdate,[
                'idMitra' => 'required',
                'tgl_mulai_kontrak' => 'required|date',
                'tgl_selesai_kontrak' => 'required|date',
                'status_kontrak' => 'required',
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }

            $car->idMitra  = $dataUpdate['idMitra'];
            $car->tgl_mulai_kontrak  = $dataUpdate['tgl_mulai_kontrak'];
            $car->tgl_selesai_kontrak  = $dataUpdate['tgl_selesai_kontrak'];
            $car->status_kontrak  = $dataUpdate['status_kontrak'];
        }

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        if($request->file('url_car_img')){
            $validate = Validator::make($dataUpdate, [

                'url_pp_customer' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_car_img')->store('car/carpic','public');
            $car->url_car_img = $request->file('url_car_img')->store('car/carpic','public');
        }

        $car->no_plat  = $dataUpdate['no_plat'];
        $car->nama_mobil  = $dataUpdate['nama_mobil'];
        $car->jenis_transmisi  = $dataUpdate['jenis_transmisi'];
        $car->jenis_bahan_bakar  = $dataUpdate['jenis_bahan_bakar'];
        $car->warna_mobil  = $dataUpdate['warna_mobil'];
        $car->kapasitas_penumpang  = $dataUpdate['kapasitas_penumpang'];
        $car->fasilitas_mobil  = $dataUpdate['fasilitas_mobil'];
        $car->no_stnk  = $dataUpdate['no_stnk'];
        $car->kategori_aset  = $dataUpdate['kategori_aset'];
        $car->tgl_terakhir_service  = $dataUpdate['tgl_terakhir_service'];
        $car->ketersediaan_mobil  = $dataUpdate['ketersediaan_mobil'];
        $car->tarif_harian_mobil  = $dataUpdate['tarif_harian_mobil'];
        $car->vol_bagasi  = $dataUpdate['vol_bagasi'];
        $car->tipe_mobil  = $dataUpdate['tipe_mobil'];

        if($car->save()){
            return response([
                'message' => 'Update Car Success',
                'data' => $car
            ],200);
        }

        return response([
            'message' => 'Update Car Failed',
            'data' => null
        ],400);
    }

    public function destroy($id)
    {
        $car = Car::find($id);

        if(is_null($car)){
            return response([
                'message' => 'Car Not Found',
                'data' => null
            ],404);
        }

        if($car->delete()){
            return response([
                'message' => 'Delete Car Success!',
                'data' => $car
            ],200);
        }

        return response([
            'message' => 'Delete Car Failed!',
            'data' => null
        ],400);
    }
}
