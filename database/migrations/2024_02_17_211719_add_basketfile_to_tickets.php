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
        Schema::table('tickets', function (Blueprint $table) {
            $table->double('cashcomp')->after('refund')->default(0);
			$table->double('totalstampduty')->after('cashcomp')->default(0);
            $table->string('basketfile')->after('deal_ticket')->nullable();
			
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('cashcomp');
            $table->dropColumn('basketfile');
            $table->dropColumn('totalstampduty');
        });
    }
};
