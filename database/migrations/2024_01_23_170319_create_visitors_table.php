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
            $table->string('barcode')->unique();
            $table->string('qrcode')->nullable();
            $table->string('ticket_invite_code')->nullable();
            $table->unsignedInteger('category_id')->nullable();
            $table->string('name')->nullable();
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->text('photo')->nullable();
            $table->string('idproof')->nullable();
            $table->text('idproof_back')->nullable();
            $table->text('vaccine')->nullable();
            $table->string('member_of')->nullable();
            $table->string('membership_card')->nullable();
            $table->string('membership_no')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('company_barcode')->nullable();
            $table->dateTime('arival')->nullable();
            $table->dateTime('departure')->nullable();
            $table->tinyInteger('threat')->default(0);
            $table->integer('current_location')->nullable();
            $table->integer('current_seat')->nullable();
            $table->dateTime('last_movement')->nullable();
            $table->text('last_visited_years')->nullable();
            $table->dateTime('registered_on')->nullable();
            $table->dateTime('verified_on')->nullable();
            $table->dateTime('printed_on')->nullable();
            $table->text('src')->nullable();
            $table->string('receipt_no')->nullable();
            $table->integer('amount')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('docket_no')->nullable();
            $table->string('docket_status')->nullable();
            $table->text('extra')->nullable();
            $table->string('travel_ticket')->nullable();
            $table->string('iata_airline')->nullable();
            $table->string('iata_airport')->nullable();
            $table->integer('arrival_city_id')->nullable();
            $table->integer('departure_city_id')->nullable();
            $table->string('departure_city')->nullable();
            $table->timestamp('check_in_date')->nullable();
            $table->timestamp('check_out_date')->nullable();
            $table->timestamp('arrival_date_time')->nullable();
            $table->timestamp('departure_date_time')->nullable();
            $table->string('travel_by')->nullable();
            $table->string('return_flight_train_number')->nullable();
            $table->string('return_flight_train_name')->nullable();
            $table->string('return_date')->nullable();
            $table->string('return_city')->nullable();
            $table->string('return_pnr')->nullable();
            $table->string('flight_train_number')->nullable();
            $table->string('flight_train_name')->nullable();
            $table->string('departure_pnr')->nullable();
            $table->string('travel_booked_by')->nullable();
            $table->integer('hotel_id')->nullable();
            $table->string('room_number')->nullable();
            $table->string('car_number')->nullable();
            $table->string('hospitality_status')->nullable();
            $table->enum('hosted_buyer', ['0', '1']);
            $table->dateTime('hosted_on')->nullable();
            $table->integer('hosted_by')->nullable();
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
