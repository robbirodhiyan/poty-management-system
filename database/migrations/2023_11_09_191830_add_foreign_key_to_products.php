<?php

// database/migrations/xxxx_xx_xx_add_foreign_key_to_products.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToProducts extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('production_id')->nullable();
            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['production_id']);
            $table->dropColumn('production_id');
        });
    }
}
