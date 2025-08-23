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
        Schema::table('sales', function (Blueprint $table) {
            // Foreign key for clients
            $table->foreign('client_id')
                  ->references('id')
                  ->on('clients')
                  ->nullOnDelete();

            // Foreign key for payment methods
            $table->foreign('payment_method_id')
                  ->references('id')
                  ->on('payment_methods')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['payment_method_id']);
        });
    }
};