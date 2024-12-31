<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->bigInteger('sort_order')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->change();
        });
    }
};