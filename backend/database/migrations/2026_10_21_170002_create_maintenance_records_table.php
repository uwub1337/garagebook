<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained()->cascadeOnDelete();
            $table->string('item', 100);
            $table->date('date');
            $table->unsignedInteger('mileage');
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('next_due_date')->nullable();
            $table->unsignedInteger('next_due_mileage')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
