<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('idMitra')->references('id_mitra')->on('partners')->nullable();
            $table->string('no_plat');
            $table->string('nama_mobil');
            $table->string('jenis_transmisi');
            $table->string('jenis_bahan_bakar');
            $table->string('warna_mobil');
            $table->integer('kapasitas_penumpang');
            $table->string('fasilitas_mobil');
            $table->string('no_stnk');
            $table->enum('kategori_aset', ['Company','Partner']);
            $table->date('tgl_terakhir_service');
            $table->enum('ketersediaan_mobil', ['Available','Occupied','Unavailable']);
            $table->float('tarif_harian_mobil',10,2);
            $table->date('tgl_mulai_kontrak')->nullable();
            $table->date('tgl_selesai_kontrak')->nullable();
            $table->float('vol_bagasi');
            $table->string('tipe_mobil');
            $table->enum('status_kontrak',['Active','Inactive'])->nullable();
            $table->string('url_car_img')->default('default/noimgcar.png');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cars');
    }
}
