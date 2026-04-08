<?php

namespace Database\Seeders;

use App\Models\Formation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des formations...');
        
        $formations = [
            [
                'category_id' => 1,
                'sub_category_id' => 2,
                'title' => 'Développement Web Complet',
                'description' => 'Formation complète du frontend au backend avec Laravel, React et bases de données',
                'price' => 299.99
            ],
            [
                'category_id' => 2,
                'sub_category_id' => 4,
                'title' => 'Design UI/UX Avancé',
                'description' => 'Maîtrise des outils de design moderne et expérience utilisateur',
                'price' => 199.99
            ],
            [
                'category_id' => 3,
                'sub_category_id' => 6,
                'title' => 'Marketing Digital Pro',
                'description' => 'Stratégies marketing avancées pour les entreprises',
                'price' => 399.99
            ],
            [
                'category_id' => 4,
                'sub_category_id' => 7,
                'title' => 'Data Science Fondamental',
                'description' => 'Introduction à la science des données avec Python',
                'price' => 499.99
            ],
        ];
        
        foreach ($formations as $formation) {
            Formation::create($formation);
        }
        
        $this->commander('✅ ' . count($formations) . ' formations créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
