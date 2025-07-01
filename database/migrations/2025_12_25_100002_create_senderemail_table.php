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
        Schema::create('senderemails', function (Blueprint $table) {
            $table->id();
			$table->string('driver', 35)->default('smtp');
			$table->string('host');
			$table->integer('port')->default(587);
			$table->string('username');
			$table->string('password');
			$table->string('encryption'); // 'tls', 'ssl'
			$table->string('from_address');
			$table->string('from_name')->nullable();
			$table->string('reply_to_address')->nullable();
			$table->integer('status')->default(1);
			$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senderemails');
    }
};
