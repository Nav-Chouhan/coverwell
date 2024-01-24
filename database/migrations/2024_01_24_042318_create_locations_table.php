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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->smallInteger("valid_times")->nullable();
            $table->tinyInteger("each_day")->default(1);
            $table->integer("parent_id")->nullable();
            $table->integer("lft")->nullable();
            $table->integer("rgt")->nullable();
            $table->integer("depth")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
