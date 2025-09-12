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
        Schema::table('wan_stat_totals', function (Blueprint $table) {
            $table->string('is_wan_stat')->nullable()->after('region'); // adjust after which column
        });
    }

    public function down(): void
    {
        Schema::table('wan_stat_totals', function (Blueprint $table) {
            $table->dropColumn('is_wan_stat');
        });
    }
};
