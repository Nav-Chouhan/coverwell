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
        Schema::create('invites', function (Blueprint $table) {
            $table->id();
            $table->string("code")->unique();
            $table->string("for")->unique();
            $table->integer("max")->default(1);
            $table->integer("uses")->default(0);
            $table->timestamp("valid_until")->nullable();
            $table->integer("category_id")->nullable(0);
            $table->string("name",50)->nullable();
            $table->string("contact",10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invites');
    }
};
