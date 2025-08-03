<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resignation_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            // Campos del formato
            $table->string('full_name');
            $table->date('resignation_date');
            $table->text('reason');
            $table->string('signature')->nullable();

            // Enlace público
            $table->uuid('token')->unique();

            $table->timestamps(); // created_at lo usaremos para eliminar después de 1h
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resignation_forms');
    }
};
