<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('images')->nullable();
            $table->unsignedBigInteger('state');
            $table->foreign('state')->references('id')->on('states');
            $table->string('title'); 
            $table->float('price', 10, 2);
            $table->boolean('price_negotiable');
            $table->string('description')->nullable();
            $table->dateTime('created_at');         
            $table->integer('views');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
}
