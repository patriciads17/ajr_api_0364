<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('idCustomer')->references('id')->on('customers');
            $table->string('idEmployee')->references('id')->on('employees')->nullable();
            $table->string('idCar')->references('id')->on('cars');
            $table->string('idDriver')->references('id')->on('drivers')->nullable();
            $table->string('idPromo')->references('id')->on('promos')->nullable();
            $table->dateTime('tgl_transaksi');
            $table->dateTime('tgl_mulai_sewa');
            $table->dateTime('tgl_selesai_sewa');
            $table->dateTime('tgl_pengembalian')->nullable();
            $table->string('note')->nullable();
            $table->string('status_transaksi');
            $table->enum('jenis_transaksi', ['Only Car', 'Car + Driver']);
            $table->string('url_bukti_pembayaran')->default('default/noimg.jpg');
            $table->enum('metode_pembayaran', ['Cash', 'Cashless'])->nullable();
            $table->string('komentar_driver')->nullable();
            $table->string('komentar_ajr')->nullable();
            $table->decimal('rating_driver',11,2)->nullable();
            $table->decimal('rating_ajr',11,2)->nullable();
            $table->float('sub_total_pembayaran',15,2);
            $table->float('total_potongan_promo',15,2)->nullable();
            $table->float('total_denda',15,2)->nullable();
            $table->float('total_pembayaran',15,2)->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
