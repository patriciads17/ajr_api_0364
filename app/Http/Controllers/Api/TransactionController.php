<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Driver;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::all();
        if(count($transactions)>0){
            return response([
                'message' => 'Retrieve All Success!',
                'data' => $transactions
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showPayment($id)
    {
        $transactions = Transaction::where('idCustomer', $id)
                        ->where('status_transaksi', '!=', 'Havent placed an order yet')
                        ->where('status_transaksi', '!=', 'Transaction completed!')
                        ->where('status_transaksi', '!=', 'Your request is being processed! Waiting CS response')
                        ->where('status_transaksi', '!=', 'Your request has been accepted!')
                        ->where('status_transaksi', '!=', 'Your request has been rejected!')
                        ->get();
        if(count($transactions)>0){
            return response([
                'message' => 'Retrieve All Payment Success!',
                'data' => $transactions
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function showRent($id)
    {
        $transactions = Transaction::where('idCustomer', $id)
                        ->where('status_transaksi', '!=', 'Please make payment immediately and complete the data payment!')
                        ->where('status_transaksi', '!=', 'Transaction completed!')
                        ->where('status_transaksi', '!=', 'Your payment is being processed! Waiting CS response')
                        ->where('status_transaksi', '!=', 'Your payment has been accepted!')
                        ->where('status_transaksi', '!=', 'Your payment has been rejected!')
                        ->get();

        if(count($transactions) > 0){
            return response([
                'message' => 'Retrieve All Rent Success!',
                'data' => $transactions
            ],200);
        }

        return response([
            'message' => 'No Data',
            'data' => null
        ],400);
    }

    public function showTransaction($id)
    {
        $transactions = Transaction::where('idCustomer', $id)
                        ->orWhere('status_transaksi', 'Transaction completed!')
                        ->where('status_transaksi', 'Your payment has been accepted!')
                        ->get();
        if(count($transactions)>0){
            return response([
                'message' => 'Retrieve All Transaction Success!',
                'data' => $transactions
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function storeRenting(Request $request)
    {
        $newTransaction = $request->all();

        $transactionDate = substr(str_replace( '-', '', Carbon::now()->format('y-m-d')), 0,8);
        if($newTransaction['jenis_transaksi'] == 'Only Car') {
            $prefix = 'TRN'.$transactionDate.'00'.'-';
        }else if($newTransaction['jenis_transaksi'] == 'Car + Driver') {
            $prefix = 'TRN'.$transactionDate.'01'.'-';
        }

        $newTransaction['id'] = IdGenerator::generate(['table' => 'transactions', 'length' => 15, 'prefix' => $prefix]);
        
        $validate = Validator::make($newTransaction,[
            'idCustomer' => 'required',
            'idCar' => 'required',
            'tgl_transaksi' => 'required|date|date_format:Y-m-d H:i',
            'tgl_mulai_sewa' => 'required|date|date_format:Y-m-d H:i',
            'tgl_selesai_sewa' => 'required|date|date_format:Y-m-d H:i',
            'jenis_transaksi' => 'required|in:Only Car, Car + Driver',
            'status_transaksi' => 'required',
            'sub_total_pembayaran' => 'required',
        ]);

        if($newTransaction['jenis_transaksi'] == 'Car + Driver'){
            $validate = Validator::make($newTransaction,[
                'idDriver' => 'required',
            ]);
        }

        if($validate->fails())
            return response([
                'message' => $validate->errors()
            ], 400);

        $transaction = Transaction::create($newTransaction);
        return response([
            'message' => 'Add Transaction-Renting Success',
            'data' => $transaction
        ], 200);
    }

    public function storePayment(Request $request)
    {
        $updatePayment = $request->all();
        $transaction = Transaction::where('id', $updatePayment['id'])->first();
        $validate = Validator::make($updatePayment,[
            'idPromo' => 'required',
            'total_potongan_promo' => 'required',
            'total_pembayaran' => 'required',
            'total_denda' => 'required',
            'status_transaksi' => 'required',
            'metode_pembayaran' => 'required|in:Cash,Cashless',
            'sub_total_pembayaran' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ], 400);
        }

        if($transaction['metode_pembayaran'] == 'Cashless' && $request->file('url_bukti_pembayaran')){
            $validate = Validator::make($updatePayment, [

                'url_bukti_pembayaran' => 'image|mimes:jpeg,png,jpg,gif,svg|file|max:2048'
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }
            $request->file('url_bukti_pembayaran')->store('transaction','public');
            $transaction->url_bukti_pembayaran = $request->file('url_bukti_pembayaran')->store('transaction','public');
        }

        $transaction->idPromo  = $updatePayment['idPromo'];
        $transaction->status_transaksi  = $updatePayment['status_transaksi'];
        $transaction->total_potongan_promo  = $updatePayment['total_potongan_promo'];
        $transaction->total_pembayaran  = $updatePayment['total_pembayaran'];
        $transaction->total_denda  = $updatePayment['total_denda'];
        $transaction->sub_total_pembayaran  = $updatePayment['sub_total_pembayaran'];
        $transaction->metode_pembayaran  = $updatePayment['metode_pembayaran'];
        $transaction->note  = $request->note;
    
    
        if($transaction->save()){
            return response([
                'message' => 'Add Transaction-Payment Success',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Add Transaction-Payment Failed',
            'data' => null
        ],400);
    }

    public function storeRating(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if(is_null($transaction)){
            return response([
                'message' => 'Transaction Not Found',
                'data' => null
            ],404);
        }

        $rating = $request->all();
        $validate = Validator::make( $rating, [
            'rating_ajr' => 'required',
            'komentar_ajr' => 'required|max: 100',
        ]);

        if($request->rating_driver){
            $validate = Validator::make( $rating, [
                'rating_driver' => 'required',
                'komentar_driver' => 'required|max: 100',
            ]);

            $transaction->rating_driver = $rating['rating_driver'];
            $transaction->komentar_driver = $rating['komentar_driver'];
        }

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        $transaction->rating_ajr = $rating['rating_ajr'];
        $transaction->komentar_ajr = $rating['komentar_ajr'];
        $transaction->status_transaksi = 'Transaction completed!';
        
        if($transaction->save()){
            return response([
                'message' => 'Rating successfully added!',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Rating failed added!',
            'data' => null
        ],400);
    }

    public function show($id)
    {
        $transaction = Transaction::find($id);

        if(!is_null($transaction)){
            return response([
                'message' => 'Retrieve Transaction Success',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Transaction Not Found',
            'data' => null
        ],404);
    }

    public function updateStatusRent(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if(is_null($transaction)){
            return response([
                'message' => 'Transaction Not Found',
                'data' => null
            ],404);
        }

        $transaction->status_transaksi = $request->status_transaksi;
        $transaction->note = $request->note;

        if($transaction->save()){
            return response([
                'message' => 'Update Transaction Success',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Update Transaction Failed',
            'data' => null
        ],400);
    }

    public function updateRent(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if(is_null($transaction)){
            return response([
                'message' => 'Transaction Not Found',
                'data' => null
            ],404);
        }

        $rentUpdate = $request->all();
        $validate = Validator::make( $rentUpdate, [
            'tgl_mulai_sewa' => 'required|date|date_format:Y-m-d H:i',
            'tgl_selesai_sewa' => 'required|date|date_format:Y-m-d H:i',
            'jenis_transaksi' => 'required|in:Only Car, Car + Driver',
            'status_transaksi' => 'required',
            'sub_total_pembayaran' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }

        if($rentUpdate['idDriver'] != null){
            $validate = Validator::make($rentUpdate,[
                'idDriver' => 'required',
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }

            $transaction->idDriver = $rentUpdate['idDriver'];
        }

        if($rentUpdate['idCar'] != null){
            $validate = Validator::make($rentUpdate,[
                'idCar' => 'required',
            ]);

            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }

            $transaction->idCar = $rentUpdate['idCar'];
        }

        $transaction->status_transaksi = $rentUpdate['status_transaksi'];
        $transaction->tgl_mulai_sewa = $rentUpdate['tgl_mulai_sewa'];
        $transaction->tgl_selesai_sewa = $rentUpdate['tgl_selesai_sewa'];
        $transaction->jenis_transaksi = $rentUpdate['jenis_transaksi'];
        $transaction->sub_total_pembayaran = $rentUpdate['sub_total_pembayaran'];
        $transaction->note = $request->note;
        
        if($transaction->save()){
            return response([
                'message' => 'Update Transaction Success',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Update Transaction Failed',
            'data' => null
        ],400);
    }

    public function updateRentByCs(Request $request, $id)
    {
        $transaction = Transaction::find($id);
        if(is_null($transaction)){
            return response([
                'message' => 'Transaction Not Found',
                'data' => null
            ],404);
        }

        $validate = Validator::make($request->all(),[
            'status_transaksi' => 'required',
            'idEmployee' => 'required',
        ]);

        if($validate->fails()){
            return response([
                'message' => $validate->errors()
            ],400);
        }
        
        if($request->tgl_pengembalian){
            $validate = Validator::make($request->all(),[
                'tgl_pengembalian' => 'required|date|date_format:Y-m-d H:i',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }

            $transaction->tgl_pengembalian = $request->tgl_pengembalian;
        }

        if($request->note){
            $validate = Validator::make($request->all(),[
                'note' => 'required',
            ]);
            
            if($validate->fails()){
                return response([
                    'message' => $validate->errors()
                ],400);
            }

            $transaction->note = $request->note;
        }

        $transaction->status_transaksi = $request->status_transaksi;
        $transaction->idEmployee = $request->idEmployee;

        if($transaction->save()){
            return response([
                'message' => 'Update Transaction Success',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Update Transaction Failed',
            'data' => null
        ],400);
    }

    public function delete($id)
    {
        $transaction = Transaction::find($id);

        if(is_null($transaction)){
            return response([
                'message' => 'Transaction Not Found',
                'data' => null
            ],404);
        }

        if($transaction->delete()){
            return response([
                'message' => 'Delete Transaction Success!',
                'data' => $transaction
            ],200);
        }

        return response([
            'message' => 'Delete Transaction Failed!',
            'data' => null
        ],400);
    }

    public function averageDriverRate(Request $request)
    {
        $drivers = Transaction::where('idDriver', $request->id)->where('status_transaksi', 'Transaction completed!')->get();
        $driver = Driver::find($request->id);
        if(count($drivers) == 0){
            $avg = 0;
        }else{
            $amount = $drivers->count();
            $value = Transaction::where('idDriver', $request->id)->pluck('rating_driver')->sum();
            $avg = $value / $amount;
        }

        $driver->rerata_rating = $avg;

        if($driver->save()){
            return response([
                'message' => 'Update Driver Success',
                'data' => $driver,
                'amount_order' => (int) $amount,

            ],200);
        }
    }

    public function findUpcomingOrder($id){

        $now = Carbon::now()->format('Y-m-d H:i');

        $transaction = Transaction::where('status_transaksi', 'Your request has been accepted!')
                                    ->where('idDriver', $id)
                                    ->first();

        if(!is_null($transaction)){
            return response([
                'message' => 'Successfully found!',
                'data' => $transaction,
            ],200);  
        }
        return response([
            'message' => 'Empty!',
            'data' => $transaction,
        ],200);            
    }

    public function getNewestRate($id)
    {
        $rate = Transaction::where('idDriver', $id)->where('status_transaksi', 'Transaction completed!')->orderBy('tgl_transaksi','DESC')->first();
        return response([
            'message' => 'Here is newest rate',
            'data' => $rate,
        ],200); 
        
    }

    public function showTransactionDriver($id)
    {
        $transaction = Transaction::where('idDriver', $id)-> where('status_transaksi', 'Transaction completed!')->get();
        if(count($transaction)>0){
            return response([
                'message' => 'Successfully found!',
                'data_array' => $transaction,
            ],200);  
        }
        return response([
            'message' => 'Empty!',
            'data_array' => $transaction,
        ],200); 
    }
}
