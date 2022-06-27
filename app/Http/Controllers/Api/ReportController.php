<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
   
    public function topCustomer($id)
    {
        $data = DB::table('users')
                    ->leftJoin('transactions', 'users.id', '=', 'transactions.idCustomer')
                    ->select('users.id', 'users.nama_customer', DB::raw('count(transactions.id) as jumlah_transaksi'))
                    ->whereMonth('tgl_transaksi', $id)
                    ->groupBy('users.id')
                    ->orderByDesc('jumlah_transaksi')
                    ->limit(5)
                    ->get();

        if(count($data)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $data
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function topDriver($id)
    {
        $data = DB::table('drivers')
                    ->leftJoin('transactions', 'drivers.id', '=', 'transactions.idDriver')
                    ->select('drivers.id', 'drivers.nama_driver', DB::raw('count(transactions.id) as jumlah_transaksi'))
                    ->whereMonth('tgl_transaksi', $id)
                    ->groupBy('drivers.id')
                    ->orderByDesc('jumlah_transaksi')
                    ->limit(5)
                    ->get();

        if(count($data)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $data
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    
    public function driverReport($id)
    {
        $data = DB::table('drivers')
                    ->leftJoin('transactions', 'drivers.id', '=', 'transactions.idDriver')
                    ->select('drivers.id', 'drivers.nama_driver', DB::raw('count(transactions.id) as jumlah_transaksi'), 'drivers.rerata_rating')
                    ->whereMonth('tgl_transaksi',$id)
                    ->groupBy('drivers.id')
                    ->orderByDesc('jumlah_transaksi')
                    ->limit(5)
                    ->get();

        if(count($data)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $data
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function incomeReport($id)
    {
        $data = DB::table('transactions')
                    ->join('users', 'transactions.idCustomer', '=', 'users.id')
                    ->join('cars', 'transactions.idCar', '=', 'cars.id')
                    ->select('users.nama_customer', 'cars.nama_mobil', 'transactions.jenis_transaksi', DB::raw('count(transactions.id) as jumlah_transaksi'), DB::raw('sum(transactions.total_pembayaran) as jumlah_pendapatan') )
                    ->whereMonth('tgl_transaksi', $id)
                    ->groupBy('users.id','transactions.jenis_transaksi')
                    ->get();

        if(count($data)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $data
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    
    public function carReport($id)
    {
        $data = DB::table('transactions')
                    ->join('cars', 'transactions.idCar', '=', 'cars.id')
                    ->leftjoin('drivers', 'transactions.idDriver', '=', 'drivers.id')                                                     //cara dapatin diffday lalu dikaliin sma tarif harian (?)
                    ->select('cars.tipe_mobil', 'cars.nama_mobil', DB::raw('COUNT(transactions.id) as jumlah_peminjaman'), DB::raw('TIMESTAMPDIFF(day,transactions.tgl_mulai_sewa, transactions.tgl_pengembalian)*cars.tarif_harian_mobil + CASE WHEN drivers.tarif_harian_driver is null THEN 0 ELSE transactions.total_denda-drivers.tarif_harian_driver END  as jumlah_pendapatan'))
                    ->whereMonth('tgl_transaksi', $id)
                    ->groupBy('cars.id')
                    ->orderByDesc('jumlah_peminjaman')
                    ->get();

        if(count($data)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $data
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }
}
