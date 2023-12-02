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
        Schema::create('securities', function (Blueprint $table) {
            $table->id();
			$table->integer('amc_id');
            $table->string('name');
			$table->string('symbol')->nullable();
			$table->string('isin')->nullable();
			$table->biginteger('basket_size')->default(0);
			$table->double('markup_percentage')->default(0);		
			$table->double('price')->default(0);
			$table->integer('status')->default(1);      // 1 -- Active, 0 -- Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('securities');
    }
};
