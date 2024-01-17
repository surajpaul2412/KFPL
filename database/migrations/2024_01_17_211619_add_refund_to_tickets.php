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
            $table->double('refund')->after('nav')->default(0);
            $table->double('expected_refund')->after('refund')->default(0);
            $table->string('deal_ticket')->after('expected_refund')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('refund');
            $table->dropColumn('expected_refund');
            $table->dropColumn('deal_ticket');
        });
    }
};
