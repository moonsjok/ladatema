<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Formation;
use App\Models\Subscription;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\DB;

class GuestSubscriptionTest extends DuskTestCase
{
    protected $formation;

    protected function setUp(): void
    {
        parent::setUp();

        // Nettoyage préalable des données de test
        $testEmail = 'guest.subscription.dusk@example.com';
        $userIds = DB::table('users')->where('email', $testEmail)->pluck('id');
        if ($userIds->isNotEmpty()) {
            DB::table('profiles')->whereIn('user_id', $userIds)->delete();
            DB::table('subscriptions')->whereIn('user_id', $userIds)->delete();
            DB::table('attempts')->whereIn('user_id', $userIds)->delete();
            DB::table('users')->whereIn('id', $userIds)->delete();
        }

        // Créer ou récupérer une formation de test (gratuite pour souscription directe)
        $this->formation = Formation::firstOrCreate(
            ['title' => 'Formation Test Dusk Souscription'],
            [
                'description' => 'Formation de test pour le processus de souscription sans compte.',
                'price' => 0,
            ]
        );
        $this->formation->price = 0;
        $this->formation->save();
    }

    /**
     * Test complet : Un utilisateur non connecté s'inscrit durant la souscription et poursuit le processus jusqu'au bout.
     */
    public function test_guest_can_create_account_during_subscription_and_complete_process(): void
    {
        $formationId = $this->formation->id;
        $testEmail = 'guest.subscription.dusk@example.com';

        $this->browse(function (Browser $browser) use ($formationId, $testEmail) {
            // 1. Accès à la page de création de compte liée à la formation
            $browser->visit("/subscriptions/create-account?type=formation&typeid={$formationId}")
                    ->waitForText('Créer un compte pour continuer', 10)
                    ->assertSee('Créer un compte pour continuer')
                    
                    // 2. Saisie du formulaire d'inscription
                    ->type('#prenoms', 'Jean-Marc')
                    ->type('#nom', 'Koffi')
                    ->type('#email', $testEmail)
                    ->type('#phone_call', '+22990000000')
                    ->type('#phone_whatsapp', '+22990000000')
                    ->type('#password', 'password123')
                    ->type('#password_confirmation', 'password123')
                    
                    // 3. Soumission du formulaire d'inscription
                    ->press('button[type="submit"]')
                    
                    // 4. Redirection vers la page de confirmation de souscription
                    ->waitForLocation('/subscriptions/confirm', 15)
                    ->assertPathIs('/subscriptions/confirm')
                    ->assertSee('Jean-Marc')
                    ->assertSee('Koffi')
                    ->assertSee($testEmail)
                    ->assertSee($this->formation->title)
                    
                    // 5. Finalisation de la souscription via le formulaire de souscription
                    ->press('form[action*="subscriptions/store"] button[type="submit"]')
                    ->pause(3000);
        });

        // 7. Vérifications en base de données
        $user = User::where('email', $testEmail)->first();
        $this->assertNotNull($user, "L'utilisateur n'a pas été créé en base de données.");
        $this->assertEquals('student', $user->role, "Le rôle par défaut doit être 'student'.");
        $this->assertEquals('Jean-Marc', $user->prenoms);
        $this->assertEquals('Koffi', $user->nom);

        $subscription = Subscription::where('user_id', $user->id)
            ->where('formation_id', $formationId)
            ->first();
        $this->assertNotNull($subscription, "La souscription n'a pas été enregistrée en base de données.");
        $this->assertTrue((bool)$subscription->is_validated, "La souscription gratuite doit être validée automatiquement.");
    }
}
