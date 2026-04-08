<?php

namespace Database\Seeders;

use App\Models\Answer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des réponses...');
        
        $answers = [
            [
                'question_id' => 1,
                'answer_text' => 'HTML5 introduit des balises sémantiques comme <header>, <nav>, <footer> qui améliorent la structure et le SEO.',
                'is_correct' => true,
            ],
            [
                'question_id' => 2,
                'answer_text' => 'Le Golden Ratio est un principe mathématique (environ 1.618) utilisé pour créer des proportions visuellement agréables et équilibrées.',
                'is_correct' => true,
            ],
        ];
        
        foreach ($answers as $answer) {
            Answer::create($answer);
        }
        
        $this->commander('✅ ' . count($answers) . ' réponses créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
