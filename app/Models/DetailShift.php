<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetailShift extends Model
{
    use HasFactory;

    protected $table = 'detail_shifts';
    protected $primaryKey = 'id';
    public $incrementing = false;
    
    protected $fillable = [
        'id','id_pegawai','id_shift'
    ];

    public function car() {
        return $this->hasOne(Car::class);
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
