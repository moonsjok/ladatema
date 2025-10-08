<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Formation;
use App\Models\Course;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Générer le sitemap du site';

    public function handle()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home')))
            ->add(Url::create(route('guest.formations.index')));

        // Ajouter les formations
        Formation::all()->each(function ($formation) use ($sitemap) {
            $sitemap->add(Url::create(route('guest.formations.show', [
                'formation' => $formation->id,
                'slug' => \Illuminate\Support\Str::slug($formation->titre),
            ])));
        });

        // Ajouter les cours
        Course::all()->each(function ($course) use ($sitemap) {
            $sitemap->add(Url::create(route('guest.courses.show', [
                'course' => $course->id,
                'slug' => \Illuminate\Support\Str::slug($course->titre),
            ])));
        });

        // Sauvegarder le sitemap dans public/
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('✅ Sitemap généré avec succès !');
    }
}
