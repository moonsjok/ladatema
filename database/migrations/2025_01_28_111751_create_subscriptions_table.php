<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('formation_id')->nullable()->constrained()->onDelete('cascade'); // Formation optionnelle
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade'); // Si cours
            $table->foreignId('chapter_id')->nullable()->constrained()->onDelete('cascade'); // Si chapitre
            $table->enum('type', ['formation', 'course', 'chapter']); // Type de souscription
            $table->integer('price'); // Coût spécifique
            $table->string('payment_reference')->nullable(); // Référence du paiement
            $table->boolean('is_validated')->default(false); // Validation par l'admin
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
