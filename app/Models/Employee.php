<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;


class Employee extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $table = "employees";
    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id', 'idRole', 'nama_pegawai', 'alamat_pegawai', 'tgl_lahir_pegawai', 'gender_pegawai', 'no_telp_pegawai', 'email', 'password', 'url_foto_pegawai'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
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

