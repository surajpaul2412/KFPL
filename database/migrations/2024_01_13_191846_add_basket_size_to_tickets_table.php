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
            $table->string('basket_size')->after('basket_no')->nullable();
            $table->string('security_price')->after('rate')->nullable();
            $table->string('markup_percentage')->after('security_price')->nullable()->comment('%');
            $table->double('actual_total_amt')->after('total_amt')->default(0);
            $table->double('nav')->after('actual_total_amt')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('basket_size');
            $table->dropColumn('security_price');
            $table->dropColumn('markup_percentage');
            $table->dropColumn('actual_total_amt');
            $table->dropColumn('nav');
        });
    }
};
