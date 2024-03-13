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
        Schema::create('quick_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('security_id');
            $table->integer('type')->comment('1=>buy,2=>sell');
            $table->integer('payment_type')->comment('1=>Cash,2=>Basket,3=>Net Settlement');
            $table->integer('basket_no')->default(0);
            $table->string('basket_size')->nullable();
            $table->double('actual_total_amt')->default(0);
            $table->double('nav')->default(0);
            $table->foreignId('trader_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quick_tickets');
    }
};
