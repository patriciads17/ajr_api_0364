<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Promo extends Model
{
    use HasFactory;

    protected $table = 'promos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'id', 'kode_promo', 'syarat_promo', 'jenis_promo', 'status_promo', 'besar_potongan'
    ];

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
