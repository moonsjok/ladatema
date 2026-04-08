<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->commander('Création des utilisateurs...');
        
        $users = [
            [
                'name' => 'Super Admin',
                'nom' => 'Admin',
                'prenoms' => 'Super',
                'email' => 'moonsjokcorp@gmail.com',
                'password' => Hash::make('Joel@6758/*-'),
                'phone_call' => '+221123456789',
                'phone_whatsapp' => '+221123456789',
                'role' => 'dev',
            ],
            [
                'name' => 'Ladatema',
                'nom' => 'Ladatema',
                'prenoms' => 'Owner',
                'email' => 'ladatema@example.com',
                'password' => Hash::make('password123'),
                'phone_call' => '+221987654321',
                'phone_whatsapp' => '+221987654321',
                'role' => 'owner',
            ],
            [
                'name' => 'Admin',
                'nom' => 'Super',
                'prenoms' => 'Admin',
                'email' => 'admin@ladatema.kom',
                'password' => Hash::make('password123'),
                'phone_call' => '+221123456789',
                'phone_whatsapp' => '+221123456789',
            ],
            [
                'name' => 'Jean Dupont',
                'nom' => 'Dupont',
                'prenoms' => 'Jean',
                'email' => 'jean.dupont@example.com',
                'password' => Hash::make('password123'),
                'phone_call' => '+221987654321',
                'phone_whatsapp' => '+221987654321',
            ],
            [
                'name' => 'Marie Curie',
                'nom' => 'Curie',
                'prenoms' => 'Marie',
                'email' => 'marie.curie@example.com',
                'password' => Hash::make('password123'),
                'phone_call' => '+221112233445',
                'phone_whatsapp' => '+221112233445',
            ],
            [
                'name' => 'Pierre Martin',
                'nom' => 'Martin',
                'prenoms' => 'Pierre',
                'email' => 'pierre.martin@example.com',
                'password' => Hash::make('password123'),
                'phone_call' => '+221554433222',
                'phone_whatsapp' => '+221554433222',
            ],
            [
                'name' => 'Sophie Bernard',
                'nom' => 'Bernard',
                'prenoms' => 'Sophie',
                'email' => 'sophie.bernard@example.com',
                'password' => Hash::make('password123'),
                'phone_call' => '+221998877665',
                'phone_whatsapp' => '+221998877665',
            ],
        ];
        
        foreach ($users as $userData) {
            $user = User::create($userData);
            
            // Créer le profil associé
            Profile::create([
                'user_id' => $user->id,
                'photo' => 'default-avatar.jpg',
                'phone' => $userData['phone_call'],
                'address' => '123 Rue de la République, 75001 Paris',
                'birth_date' => '1990-01-01',
                'bio' => 'Passionné par la technologie et l\'innovation.',
            ]);
        }
        
        $this->commander('✅ ' . count($users) . ' utilisateurs avec profils créés');
    }
    
    /**
     * Afficher un message de commande
     */
    private function commander($message): void
    {
        echo $message . PHP_EOL;
    }
}
