<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des souscriptions...');
        
        $subscriptions = [
            [
                'user_id' => 2,
                'formation_id' => 1,
                'course_id' => null,
                'chapter_id' => null,
                'type' => 'formation',
                'price' => 299.99,
                'payment_reference' => 'PAY-' . uniqid(),
                'is_validated' => true,
            ],
            [
                'user_id' => 2,
                'formation_id' => null,
                'course_id' => 1,
                'chapter_id' => 1,
                'type' => 'course',
                'price' => 99.99,
                'payment_reference' => 'PAY-' . uniqid(),
                'is_validated' => true,
            ],
            [
                'user_id' => 3,
                'formation_id' => 2,
                'course_id' => null,
                'chapter_id' => null,
                'type' => 'formation',
                'price' => 199.99,
                'payment_reference' => 'PAY-' . uniqid(),
                'is_validated' => true,
            ],
        ];
        
        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
        
        $this->commander('✅ ' . count($subscriptions) . ' souscriptions créées');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
