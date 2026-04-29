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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->morphs('evaluatable'); // Pour associer une évaluation à une formation, un cours ou un chapitre
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('duration')->nullable()->comment('Durée de l\'évaluation en minutes');
            $table->integer('total_questions')->nullable()->comment('Nombre total de questions à répondre');
            $table->enum('scoring_mode', ['pourcentage', 'points'])->default('pourcentage')->comment('Mode de notation : pourcentage ou points');
            $table->integer('passing_score')->nullable()->comment('Score minimum pour réussir (en % ou points selon le mode)');
            $table->integer('max_attempts')->default(3)->comment('Nombre maximum de tentatives autorisées');
           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
