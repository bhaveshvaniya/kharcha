<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained()->onDelete('cascade');
            $table->date('price_date');
            $table->decimal('open_price', 12, 2)->nullable();
            $table->decimal('high_price', 12, 2)->nullable();
            $table->decimal('low_price', 12, 2)->nullable();
            $table->decimal('close_price', 12, 2)->nullable();
            $table->bigInteger('volume')->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);
            $table->decimal('change_percent', 8, 4)->default(0);
            $table->timestamps();

            $table->unique(['stock_id', 'price_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
