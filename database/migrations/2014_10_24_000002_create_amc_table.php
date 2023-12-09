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
        Schema::create('amcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
      			$table->string('email');
      			$table->string('pdf')->nullable();
      			$table->integer('status')->default(1);      // 1 -- Active, 0 -- Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amcs');
    }
};
