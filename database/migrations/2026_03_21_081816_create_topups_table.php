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
    public function up(): void
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference_code')->unique(); // ID unik transaksi
            $table->integer('amount_credits');          // Berapa kredit yang dibeli
            $table->decimal('price_total', 12, 2);      // Harga dalam Rupiah
            $table->string('payment_proof')->nullable(); // Foto bukti bayar
            $table->tinyInteger('status')->default(0);  // 0:Pending, 1:Success, 2:Rejected
            $table->text('admin_note')->nullable();
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
        Schema::dropIfExists('topups');
    }
};
