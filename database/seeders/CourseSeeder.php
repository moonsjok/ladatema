<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des cours...');
        
        $courses = [
            ['formation_id' => 1, 'title' => 'Introduction à HTML et CSS', 'description' => 'Les bases du développement web'],
            ['formation_id' => 1, 'title' => 'JavaScript Fondamental', 'description' => 'Programmation JavaScript moderne'],
            ['formation_id' => 1, 'title' => 'Frameworks React', 'description' => 'Composants React et gestion d\'état'],
            ['formation_id' => 2, 'title' => 'Principes du Design', 'description' => 'Théorie du design et composition visuelle'],
            ['formation_id' => 2, 'title' => 'Outils de Design', 'description' => 'Maîtrise de Photoshop, Illustrator et Figma'],
            ['formation_id' => 3, 'title' => 'Stratégies de Contenu', 'description' => 'Création de contenu engageant'],
            ['formation_id' => 4, 'title' => 'Python pour Data Science', 'description' => 'Manipulation et analyse de données'],
        ];
        
        foreach ($courses as $course) {
            Course::create($course);
        }
        
        $this->commander('✅ ' . count($courses) . ' cours créés');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
