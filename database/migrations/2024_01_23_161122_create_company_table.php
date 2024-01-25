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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('barcode');
            $table->string('contact',15)->nullable();
            $table->string('email')->nullable();
            $table->string('gstin')->nullable();
            $table->string('gst_certificate')->nullable();
            $table->string('pan',15)->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('type')->nullable();
            $table->string('stall_no')->nullable();
            $table->string('hall')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
