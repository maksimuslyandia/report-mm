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
        Schema::create('wan_meta_data', function (Blueprint $table) {
            $table->id();
            $table->string('airport_code');
            $table->string('isp')->nullable(); // isp-a or isp-b
            $table->string('isp_type')->nullable(); // ibrd, pfr, mpls, vsat
            $table->foreignId('wan_stat_total_id')->constrained()->onDelete('cascade');
            $table->boolean('is_ibo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wan_meta_data');
    }
};
