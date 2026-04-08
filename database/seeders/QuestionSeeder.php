<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des questions...');
        
        $questions = [
            [
                'evaluation_id' => 1,
                'type' => 'multiple_choice',
                'question_text' => 'Quelle est la différence entre HTML et HTML5?',
            ],
            [
                'evaluation_id' => 2,
                'type' => 'text',
                'question_text' => 'Quel est le principe du "Golden Ratio" en design?',
            ],
        ];
        
        foreach ($questions as $question) {
            Question::create($question);
        }
        
        $this->commander('✅ ' . count($questions) . ' questions créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
