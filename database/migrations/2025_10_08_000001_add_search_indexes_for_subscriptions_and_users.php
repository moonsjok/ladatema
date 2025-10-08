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
            $table->dropIndex('users_prenoms_index');
            $table->dropIndex('users_nom_index');
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_phone_call_index');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_payment_reference_index');
            $table->dropIndex('subscriptions_created_at_index');
            $table->dropIndex('subscriptions_user_id_index');
        });
    }
};
