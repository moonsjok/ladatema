<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add last name and given names as NOT NULL with a safe default for existing records
            if (!Schema::hasColumn('users', 'nom')) {
                $table->string('nom')->default('')->after('name');
            }
            if (!Schema::hasColumn('users', 'prenoms')) {
                $table->string('prenoms')->default('')->after('nom');
            }

            // Phone fields
            if (!Schema::hasColumn('users', 'phone_call')) {
                $table->string('phone_call')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'phone_whatsapp')) {
                $table->string('phone_whatsapp')->nullable()->after('phone_call');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone_whatsapp')) {
                $table->dropColumn('phone_whatsapp');
            }
            if (Schema::hasColumn('users', 'phone_call')) {
                $table->dropColumn('phone_call');
            }
            if (Schema::hasColumn('users', 'prenoms')) {
                $table->dropColumn('prenoms');
            }
            if (Schema::hasColumn('users', 'nom')) {
                $table->dropColumn('nom');
            }
        });
    }
};
