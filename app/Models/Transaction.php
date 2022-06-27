<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id', 'idCustomer', 'idEmployee', 'idCar', 'idDriver', 'idPromo', 'tgl_transaksi', 'tgl_mulai_sewa', 'tgl_selesai_sewa', 'tgl_pengembalian', 'note', 'status_transaksi', 'jenis_transaksi', 
        'url_bukti_pembayaran', 'metode_pembayaran', 'komentar_driver', 'komentar_ajr', 'rating_driver', 'rating_ajr', 'sub_total_pembayaran', 'total_potongan_promo', 'total_pembayaran', 
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function car(){
        return $this->belongsTo(Car::class);
    }

    public function driver(){
        return $this->belongsTo(Driver::class);
    }

    public function promo(){
        return $this->belongsTo(Promo::class);
    }
    
    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdateAtAttribute(){
        if(!is_null($this->attributes['update_at'])){
            return Carbon::parse($this->attributes['update_at'])->format('Y-m-d H:i:s');
        }
    }
}
