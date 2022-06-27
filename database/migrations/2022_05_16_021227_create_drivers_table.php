<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama_driver');
            $table->string('alamat_driver');
            $table->date('tgl_lahir_driver');
            $table->enum('gender_driver',  ['Male','Female']);
            $table->string('email');
            $table->string('password');
            $table->string('no_telp_driver');
            $table->decimal('rerata_rating',11,2)->nullable();
            $table->string('kemampuan_bahasa');
            $table->float('tarif_harian_driver',15,2);
            $table->enum('status_ketersediaan_driver',  ['Available','Occupied','Unavailable'])->default('Available');
            $table->string('url_foto_driver')->default('default/nopp.png');
            $table->string('url_sim_driver')->default('default/noimg.jpg');
            $table->string('url_bebas_napza')->default('default/noimg.jpg');
            $table->string('url_sehat_jiwa')->default('default/noimg.jpg');
            $table->string('url_sehat_fisik')->default('default/noimg.jpg');
            $table->string('url_skck')->default('default/noimg.jpg');
            $table->enum('status_akun',['Active','Inactive'])->default('Inactive');
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
        Schema::dropIfExists('drivers');
    }
}
