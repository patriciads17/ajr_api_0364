<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('nama_customer');
            $table->string('alamat_customer')->nullable();
            $table->enum('gender_customer',['Male','Female',''])->nullable();
            $table->date('tgl_lahir_customer');
            $table->string('no_telp_customer')->nullable();
            $table->string('email');
            $table->string('password');
            $table->string('url_tanda_pengenal')->default('default/noimg.jpg');
            $table->string('url_sim_customer')->default('default/noimg.jpg');
            $table->string('url_pp_customer')->default('default/nopp.png');
            $table->enum('status_akun',['Active','Inactive'])->nullable();
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
        Schema::dropIfExists('users');
    }
}
