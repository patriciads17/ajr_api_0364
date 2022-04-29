<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('idRole')->reference('id_role')->on('roles');
            $table->string('nama_pegawai');
            $table->string('alamat_pegawai');
            $table->date('tgl_lahir_pegawai');
            $table->enum('gender_pegawai', ['Male','Female']);
            $table->string('no_telp_pegawai');
            $table->string('email');
            $table->string('password');
            $table->string('url_foto_pegawai')->default('default/nopp.png');
            $table->integer('counter_shift')->default(0);
            $table->rememberToken();
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
        Schema::dropIfExists('employees');
    }
}
