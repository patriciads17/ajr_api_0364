<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class Driver extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'drivers';
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id', 'nama_driver', 'alamat_driver', 'tgl_lahir_driver', 'gender_driver', 'email', 'no_telp_driver', 'rerata_rating', 'kemampuan_bahasa', 
        'tarif_harian_driver', 'status_ketersediaan_driver', 'url_foto_driver', 'url_sim_driver', 'url_bebas_napza', 'url_sehat_jiwa', 
        'url_sehat_fisik', 'url_skck', 'status_akun', 'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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
