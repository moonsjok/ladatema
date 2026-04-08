<?php

namespace Database\Seeders;

use App\Models\SubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des sous-catégories...');
        
        $subCategories = [
            ['category_id' => 1, 'name' => 'HTML/CSS/JavaScript', 'description' => 'Frontend et développement web'],
            ['category_id' => 1, 'name' => 'PHP/Laravel', 'description' => 'Développement backend avec PHP et Laravel'],
            ['category_id' => 1, 'name' => 'React/Vue', 'description' => 'Frameworks JavaScript modernes'],
            ['category_id' => 2, 'name' => 'Photoshop', 'description' => 'Design graphique et retouche photo'],
            ['category_id' => 2, 'name' => 'Illustrator', 'description' => 'Création de vecteurs et illustrations'],
            ['category_id' => 2, 'name' => 'Figma', 'description' => 'Design d\'interface et prototypage'],
            ['category_id' => 3, 'name' => 'SEO/SEM', 'description' => 'Optimisation pour les moteurs de recherche'],
            ['category_id' => 3, 'name' => 'Social Media', 'description' => 'Marketing sur les réseaux sociaux'],
            ['category_id' => 4, 'name' => 'Python', 'description' => 'Programmation Python et analyse de données'],
            ['category_id' => 4, 'name' => 'Machine Learning', 'description' => 'Algorithmes d\'apprentissage automatique'],
            ['category_id' => 5, 'name' => 'Sécurité Réseau', 'description' => 'Protection des infrastructures réseau'],
            ['category_id' => 5, 'name' => 'Sécurité Applicative', 'description' => 'Sécurité des applications web'],
        ];
        
        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
        
        $this->commander('✅ ' . count($subCategories) . ' sous-catégories créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
