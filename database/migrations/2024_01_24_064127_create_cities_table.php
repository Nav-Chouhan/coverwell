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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->string("slug")->nullable();
            $table->integer("state_id")->nullable();
            $table->tinyInteger("status")->default(1)->comment('0 = deactivate,1 = active');
            $table->unsignedInteger("created_by");
            $table->unsignedInteger("updated_by")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
