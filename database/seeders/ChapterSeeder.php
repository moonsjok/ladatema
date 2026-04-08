<?php

namespace Database\Seeders;

use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChapterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des chapitres...');
        
        $chapters = [
            ['course_id' => 1, 'numero' => 1, 'title' => 'Introduction et Setup', 'content' => 'Bienvenue dans le cours et configuration de l\'environnement de développement.'],
            ['course_id' => 1, 'numero' => 2, 'title' => 'HTML et CSS Fondamentaux', 'content' => 'Structure HTML5 et styles CSS3 modernes.'],
            ['course_id' => 2, 'numero' => 1, 'title' => 'Théorie du Design', 'content' => 'Principes fondamentaux du design graphique et composition visuelle.'],
            ['course_id' => 3, 'numero' => 1, 'title' => 'Introduction au Marketing Digital', 'content' => 'Vue d\'ensemble du marketing digital et ses composantes.'],
            ['course_id' => 4, 'numero' => 1, 'title' => 'Introduction à Python', 'content' => 'Installation de Python et concepts de base de programmation.'],
        ];
        
        foreach ($chapters as $chapter) {
            Chapter::create($chapter);
        }
        
        $this->commander('✅ ' . count($chapters) . ' chapitres créés');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
