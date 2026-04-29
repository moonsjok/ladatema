<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Configurer la taille maximale des fichiers pour MediaLibrary
        config(['media-library.max_file_size' => 2 * 1024 * 1024 * 1024]); // 2GB
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('fr'); // Utiliser la date et leur en francais
        Paginator::useBootstrap(); // For Bootstrap 5
    }
}
