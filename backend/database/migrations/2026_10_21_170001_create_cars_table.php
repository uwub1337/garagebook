<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('make', 50);
            $table->string('model', 50);
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('plate', 20)->nullable();
            $table->unsignedInteger('current_mileage')->default(0);
            $table->string('fuel_type', 20)->default('gasoline');
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
