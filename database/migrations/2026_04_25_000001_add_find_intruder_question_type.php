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
        Schema::table('questions', function (Blueprint $table) {
            // Modifier l'enum pour ajouter le type 'find_intruder'
            $table->enum('type', ['multiple_choice', 'single_choice', 'text', 'find_intruder'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Revenir à l'enum original
            $table->enum('type', ['multiple_choice', 'single_choice', 'text'])->change();
        });
    }
};
