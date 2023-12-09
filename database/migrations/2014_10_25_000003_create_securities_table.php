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
      			$table->foreignId('amc_id')->constrained(); // Add foreign key constraint
            $table->string('name');
            $table->string('symbol')->nullable();
            $table->string('isin')->nullable();
      			$table->bigInteger('basket_size')->default(0);
            $table->decimal('markup_percentage', 8, 2)->default(0); // Use decimal for percentage
            $table->decimal('price', 10, 2)->default(0); // Use decimal for price
            $table->boolean('status')->default(1);      // 1 -- Active, 0 -- Inactive
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
