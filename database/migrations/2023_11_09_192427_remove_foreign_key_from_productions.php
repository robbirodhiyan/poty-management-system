<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveForeignKeyFromProductions extends Migration
{
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });
    }

    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
}
