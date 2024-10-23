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
        Schema::table('amcs', function (Blueprint $table) {
			
            $table->integer('buycashtmpl')->nullable()->after('amc_pdf');
			$table->integer('sellcashtmpl')->nullable()->after('buycashtmpl');
			$table->integer('sellcashwosstmpl')->nullable()->after('sellcashtmpl');
			$table->integer('mailtoselftmpl')->nullable()->after('sellcashwosstmpl');
			$table->text('investordetails')->nullable()->after('mailtoselftmpl');
			$table->text('bankdetails')->nullable()->after('investordetails');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('amcs', function (Blueprint $table) {
            $table->dropColumn('buycashtmpl');
            $table->dropColumn('sellcashtmpl');
            $table->dropColumn('sellcashwosstmpl');
            $table->dropColumn('mailtoselftmpl');
            $table->dropColumn('investordetails');
            $table->dropColumn('bankdetails');
        });
    }
};
