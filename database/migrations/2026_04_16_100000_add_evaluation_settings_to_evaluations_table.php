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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->integer('duration')->nullable()->comment('Durée de l\'évaluation en minutes');
            $table->integer('total_questions')->nullable()->comment('Nombre total de questions à répondre');
            $table->enum('scoring_mode', ['pourcentage', 'points'])->default('pourcentage')->comment('Mode de notation : pourcentage ou points');
            $table->integer('passing_score')->default(60)->nullable()->comment('Score minimum pour réussir (en % ou points selon le mode)');
            $table->integer('max_attempts')->nullable()->comment('Nombre maximum de tentatives autorisées');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropColumn(['duration', 'total_questions', 'scoring_mode', 'passing_score']);
        });
    }
};
