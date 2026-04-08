<?php

namespace Database\Seeders;

use App\Models\Evaluation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des évaluations...');
        
        $evaluations = [
            [
                'evaluatable_type' => 'App\Models\Course',
                'evaluatable_id' => 1,
                'title' => 'Évaluation HTML & CSS',
                'description' => 'Test sur les bases du développement web',
            ],
            [
                'evaluatable_type' => 'App\Models\Course',
                'evaluatable_id' => 2,
                'title' => 'Évaluation Design',
                'description' => 'Test sur les principes du design',
            ],
            [
                'evaluatable_type' => 'App\Models\Chapter',
                'evaluatable_id' => 1,
                'title' => 'Évaluation Chapitre',
                'description' => 'Test sur le contenu du chapitre',
            ],
            [
                'evaluatable_type' => 'App\Models\Formation',
                'evaluatable_id' => 1,
                'title' => 'Évaluation Formation',
                'description' => 'Test sur la formation complète',
            ],
        ];
        
        foreach ($evaluations as $evaluation) {
            Evaluation::create($evaluation);
        }
        
        $this->commander('✅ ' . count($evaluations) . ' évaluations créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
