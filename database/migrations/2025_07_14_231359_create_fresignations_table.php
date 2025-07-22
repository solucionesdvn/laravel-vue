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
        Schema::create('fresignations', function (Blueprint $table) {
            $table->id();
            $table->date('resignation_date'); // Resignation date
            $table->string('city');
            $table->string('company_name');   // Name of the company (at the time of resignation)
            $table->string('position');       // Employee position
            $table->date('start_date');       // Hiring date
            $table->date('end_date');         // End of contract
            $table->string('reason');         // Reason for resignation
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Foreign key
            $table->timestamp('expires_at')->nullable(); // Auto-delete timestamp
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resignations');
    }
};
