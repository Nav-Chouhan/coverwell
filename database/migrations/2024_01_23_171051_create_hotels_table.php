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
        Schema::create('hotels', function (Blueprint $table) {
              $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->integer('city_id');
            $table->integer('state_id');
            $table->integer('country_id')->default(1);
            $table->string('address');
            $table->string('room_initials_no')->unique();
            $table->string('room_current_no')->unique();
            $table->boolean('status')->default(1)->comment(' 0 = deactivate,1 = active');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
