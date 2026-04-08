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
            // Vérifier si l'index composite existe avant de le supprimer
            if (Schema::hasColumn('users', 'prenoms') && Schema::hasColumn('users', 'nom')) {
                try {
                    $table->dropIndex('users_prenoms_nom_index');
                } catch (\Exception $e) {
                    // L'index n'existe pas, on continue
                }
            }

            // Vérifier si l'index fulltext existe avant de le supprimer
            try {
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    // Vérifier si l'index fulltext existe dans la base de données
                    $indexExists = DB::select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.statistics 
                        WHERE table_schema = DATABASE() 
                        AND table_name = 'users' 
                        AND index_name = 'users_name_fulltext'
                    ");
                    
                    if ($indexExists[0]->count > 0) {
                        $table->dropFullText('users_name_fulltext');
                    }
                }
            } catch (\Exception $e) {
                // L'index n'existe pas ou erreur, on continue
            }
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            try {
                $table->dropIndex('subscriptions_is_validated_index');
            } catch (\Exception $e) {
                // L'index n'existe pas, on continue
            }
        });
    }
};
