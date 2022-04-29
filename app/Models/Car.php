<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use carbon\Carbon;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable =[
        'id', 'idMitra', 'no_plat', 'nama_mobil', 'jenis_transmisi', 'jenis_bahan_bakar', 'warna_mobil', 'kapasitas_penumpang', 'fasilitas_mobil', 'no_stnk', 'kategori_aset', 'tgl_terakhir_service',
        'ketersediaan_mobil', 'tarif_harian_mobil', 'tgl_mulai_kontrak', 'tgl_selesai_kontrak', 'vol_bagasi', 'tipe_mobil', 'status_kontrak', 'url_car_img'
    ];

    public function partner() {
        return $this->belongsTo(Partner::class);
    }

    public function transaction() {
        return $this->hasMany(Transaction::class);
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
