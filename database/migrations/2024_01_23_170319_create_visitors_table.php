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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('travel_ticket');
            $table->string('iata_airline');
            $table->string('iata_airport');
            $table->integer('arrival_city_id');
            $table->integer('departure_city_id');
            $table->timestamp('check_in_date')->nullable();
            $table->timestamp('check_out_date')->nullable();
            $table->timestamp('arrival_date_time')->nullable();
            $table->timestamp('departure_date_time')->nullable();
            $table->string('travel_by')->nullable();
            $table->integer('hotel_id');
            $table->string('room_number');
            $table->string('hospitality_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
