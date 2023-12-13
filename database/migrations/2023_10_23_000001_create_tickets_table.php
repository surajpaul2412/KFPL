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
            $table->foreignId('user_id');
            $table->foreignId('security_id');
            $table->foreignId('status_id');
            $table->integer('type')->comment('1=>buy,2=>sell');
            $table->integer('payment_type')->comment('1=>Cash,2=>Basket,3=>Net Settlement');
            $table->integer('basket_no')->default(0);
            $table->double('rate')->default(0);
            $table->double('total_amt')->default(0);
            $table->string('utr_no')->nullable();
            $table->string('screenshot')->nullable();
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
