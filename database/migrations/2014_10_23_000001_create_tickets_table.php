<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
			$table->integer('type');      // 1 -- BUY, 2 -- SELL
			$table->integer('pay_mode');  // 1 - cash 2 - Basket 3 - Net Settlement
			$table->integer('no_basket')->default(0);
			$table->double('rate')->default(0);
			$table->double('total_amt')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
