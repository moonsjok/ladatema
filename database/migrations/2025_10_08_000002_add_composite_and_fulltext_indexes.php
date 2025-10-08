<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up()
    {
        // Composite index on prenoms + nom to accelerate ordering and lookups
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'prenoms') && Schema::hasColumn('users', 'nom')) {
                $table->index(['prenoms', 'nom'], 'users_prenoms_nom_index');
            }

            // Fulltext index for fuzzy name search - note: only available on MySQL/MariaDB and requires the storage engine to support fulltext
            if (Schema::hasColumn('users', 'prenoms') && Schema::hasColumn('users', 'nom') && Schema::hasColumn('users', 'name')) {
                // Only attempt to create fulltext if using MySQL
                try {
                    $driver = DB::getDriverName();
                    if ($driver === 'mysql') {
                        $table->fullText(['prenoms', 'nom', 'name'], 'users_name_fulltext');
                    }
                } catch (\Exception $e) {
                    // ignore if not supported
                    Log::warning('Could not create fulltext index for users: ' . $e->getMessage());
                }
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'is_validated')) return;
            $table->index('is_validated', 'subscriptions_is_validated_index');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'prenoms') && Schema::hasColumn('users', 'nom')) {
                $table->dropIndex('users_prenoms_nom_index');
            }

            try {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    $table->dropFullText('users_name_fulltext');
                }
            } catch (\Exception $e) {
                // ignore
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subscriptions_is_validated_index');
        });
    }
};
