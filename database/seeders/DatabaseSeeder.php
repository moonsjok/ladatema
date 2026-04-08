<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->commander('🌱 Ladatema Database Seeder - Menu Principal');
        $this->commander('=====================================');
        
        $seeders = [
            '📂 Catégories & Sous-Catégories' => [
                CategorySeeder::class,
                SubCategorySeeder::class,
            ],
            '🤝 Partenaires Commerciaux' => [
                PartnerSeeder::class,
            ],
            '👥 Utilisateurs & Profils' => [
                UserSeeder::class,
            ],
            '🎓 Formations & Cours' => [
                FormationSeeder::class,
                CourseSeeder::class,
                ChapterSeeder::class,
            ],
            '📝 Évaluations & Questions' => [
                EvaluationSeeder::class,
                QuestionSeeder::class,
                AnswerSeeder::class,
            ],
            '💳 Souscriptions Utilisateurs' => [
                SubscriptionSeeder::class,
            ],
        ];

        $this->command->newLine();
        $this->commander('📋 Seeders Disponibles:');
        foreach ($seeders as $groupName => $seederClasses) {
            $this->commander("  📁 {$groupName}");
            foreach ($seederClasses as $index => $seederClass) {
                $className = class_basename($seederClass);
                $this->commander("    " . ($index + 1) . ". {$className}");
            }
            $this->commander('');
        }

        $this->commander('=====================================');
        $this->commander('');

        if ($this->command->confirm('❌ Skip all seeders?', false)) {
            $this->command->warn('⏭ Tous les seeders ont été ignorés.');
            return;
        }

        $this->commander('🚀 Lancement du seeding interactif...');
        $this->commander('');

        $executedSeeders = 0;
        $totalSeeders = 0;

        foreach ($seeders as $groupName => $seederClasses) {
            $this->commander("📂 Traitement du groupe: {$groupName}");
            
            foreach ($seederClasses as $seederClass) {
                $className = class_basename($seederClass);
                $totalSeeders++;
                
                if ($this->command->confirm("👉 Exécuter '{$className}'?", true)) {
                    $this->command->info("  ⏳ Exécution de {$className} en cours...");
                    
                    try {
                        $this->call($seederClass);
                        $this->command->info("  ✅ {$className} terminé avec succès!");
                        $executedSeeders++;
                    } catch (\Exception $e) {
                        $this->command->error("  ❌ Erreur dans {$className}: " . $e->getMessage());
                    }
                } else {
                    $this->command->warn("  ⏭ {$className} ignoré.");
                }
            }
            $this->commander('');
        }

        // Résumé final
        $this->commander('=====================================');
        $this->commander('📊 RÉSUMÉ DU SEEDING');
        $this->commander("=====================================");
        $this->commander("📈 Seeders exécutés: {$executedSeeders}/{$totalSeeders}");
        $this->commander("📈 Seeders ignorés: " . ($totalSeeders - $executedSeeders));
        
        if ($executedSeeders > 0) {
            $this->commander('🎉 Seeding terminé avec succès!');
        } else {
            $this->commander('⚠️ Aucun seeder n\'a été exécuté.');
        }
        
        $this->commander('=====================================');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
