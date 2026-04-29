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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('duration_in_days')->nullable()->after('is_validated')->comment('Durée de validité de la souscription en jours');
            $table->timestamp('expires_at')->nullable()->after('duration_in_days')->comment('Date d\'expiration de la souscription');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['duration_in_days', 'expires_at']);
        });
    }
};
