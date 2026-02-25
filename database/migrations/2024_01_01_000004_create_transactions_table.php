<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['buy', 'sell']);
            $table->integer('quantity');
            $table->decimal('price_per_share', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('brokerage', 10, 2)->default(0);
            $table->date('transaction_date');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
