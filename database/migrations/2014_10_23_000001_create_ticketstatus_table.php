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
        Schema::create('ticketstatus', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id');
			$table->integer('employee_id');
			$table->string('stage')->nullable();  //  Current Stage, 1 - Raised TIcket 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticketstatus');
    }
};
