<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add indexes to speed up searches on prenoms, nom, email and phone
            if (!Schema::hasColumn('users', 'prenoms')) return;
            $table->index('prenoms', 'users_prenoms_index');
            $table->index('nom', 'users_nom_index');
            $table->index('email', 'users_email_index');
            $table->index('phone_call', 'users_phone_call_index');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Add indexes on payment_reference and created_at
            if (!Schema::hasColumn('subscriptions', 'payment_reference')) return;
            $table->index('payment_reference', 'subscriptions_payment_reference_index');
            $table->index('created_at', 'subscriptions_created_at_index');
            $table->index('user_id', 'subscriptions_user_id_index');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Supprimer les index seulement s'ils existent
            $indexes = [
                'users_prenoms_index',
                'users_nom_index',
                'users_email_index',
                'users_phone_call_index'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('users', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Supprimer les index seulement s'ils existent
            $indexes = [
                'subscriptions_payment_reference_index',
                'subscriptions_created_at_index',
                'subscriptions_user_id_index'
            ];

            foreach ($indexes as $index) {
                if (Schema::hasIndex('subscriptions', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
