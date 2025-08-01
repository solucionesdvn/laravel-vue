<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('user_id'); // quién abre la caja

            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();

            $table->decimal('opening_amount', 12, 2)->default(0);  // monto inicial
            $table->decimal('closing_amount', 12, 2)->nullable();  // monto final calculado
            $table->decimal('total_sales', 12, 2)->default(0);     // ventas realizadas
            $table->decimal('total_expenses', 12, 2)->default(0);  // egresos registrados

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
