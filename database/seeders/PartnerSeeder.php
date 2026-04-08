<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des partenaires...');
        
        $partners = [
            ['name' => 'TechCorp Solutions'],
            ['name' => 'Digital Marketing Pro'],
            ['name' => 'DataScience Institute'],
            ['name' => 'Creative Studio'],
            ['name' => 'CyberSec Global'],
        ];
        
        foreach ($partners as $partner) {
            Partner::create($partner);
        }
        
        $this->commander('✅ ' . count($partners) . ' partenaires créés');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
