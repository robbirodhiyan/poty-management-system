<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('gambar')->nullable();
            $table->string('kode_product');
            $table->string('name_product');
            $table->date('exp_date');
            $table->integer('hpp');
            $table->integer('harga_jual');
            $table->string('stok_masuk');
            $table->string('stok_keluar');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
