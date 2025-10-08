<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function generate()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('welcome'))->setPriority(1.0)->setChangeFrequency('daily'))
            ->add(Url::create(route('nos.services'))->setPriority(0.8)->setChangeFrequency('weekly'))
            ->add(Url::create(route('contact.form'))->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create(route('guest.formationsList'))->setPriority(0.7)->setChangeFrequency('weekly'))
            ->add(Url::create(route('guest.formations'))->setPriority(0.7)->setChangeFrequency('weekly'));

        // Ajouter toutes les formations dynamiques
        foreach (\App\Models\Formation::all() as $formation) {
            $slug = Str::slug($formation->title); // Générer le slug à la volée

            $sitemap->add(Url::create(route('guest.formations.show', ['formation' => $formation->id, 'slug' => $slug]))
                ->setPriority(0.6)
                ->setChangeFrequency('monthly'));
        }
        // Sauvegarde du fichier sitemap.xml dans public/
        $sitemap->writeToFile(public_path('sitemap.xml'));
        return response($sitemap->render(), 200)->header('Content-Type', 'application/xml');
    }
}
