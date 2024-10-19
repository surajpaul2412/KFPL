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
        Schema::create('emailtemplates', function (Blueprint $table) {
            $table->id();
			
			$table->integer('amc_id');
			$table->string('name');
			$table->integer('type')->nullable()->comment('1=>buy, 2=>sell');
			$table->integer('status')->default(1);
			$table->text('template')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emailtemplates');
    }
};
