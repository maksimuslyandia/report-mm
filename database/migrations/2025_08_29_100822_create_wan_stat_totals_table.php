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
        Schema::create('wan_stat_totals', function (Blueprint $table) {
            $table->id();
            $table->string('link_name');
            $table->string('link_type');
            $table->string('region');
            $table->integer('bandwidth_bits');
            $table->foreignId('pool_id');
            $table->bigInteger('traffic_in');
            $table->bigInteger('traffic_out');
            $table->bigInteger('q_95_in');
            $table->bigInteger('q_95_out');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wan_stat_totals');
    }
};
