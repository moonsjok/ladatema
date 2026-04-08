<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des catégories...');
        
        $categories = [
            ['name' => 'Développement Web', 'description' => 'Formation en développement web, mobile et desktop'],
            ['name' => 'Design & Créativité', 'description' => 'Design graphique, UI/UX et créativité numérique'],
            ['name' => 'Marketing Digital', 'description' => 'Stratégies marketing en ligne et réseaux sociaux'],
            ['name' => 'Data Science', 'description' => 'Analyse de données, machine learning et IA'],
            ['name' => 'Cybersécurité', 'description' => 'Sécurité informatique et protection des données'],
        ];
        
        foreach ($categories as $category) {
            Category::create($category);
        }
        
        $this->commander('✅ ' . count($categories) . ' catégories créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
