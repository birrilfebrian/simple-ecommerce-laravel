<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // Tambahkan ini agar terhubung ke tabel users
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shop_id');

            $table->string('order_code');
            $table->string('name');
            $table->bigInteger('phone');
            $table->string('document_path');
            $table->string('payment_path');
            $table->text('note')->nullable();
            $table->bigInteger('total');
            $table->integer('status');
            $table->string('amandement_path')->nullable();
            $table->string('turnitin_result')->nullable();

            // Foreign key definitions
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('orders');
    }
};
