<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class StudentAuthTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $userIds = \DB::table('users')->where('email', 'student.dusk@example.com')->pluck('id');
        if ($userIds->isNotEmpty()) {
            \DB::table('profiles')->whereIn('user_id', $userIds)->delete();
            \DB::table('subscriptions')->whereIn('user_id', $userIds)->delete();
            \DB::table('attempts')->whereIn('user_id', $userIds)->delete();
            \DB::table('users')->whereIn('id', $userIds)->delete();
        }
    }

    /**
     * Test complet : Inscription d'un nouvel étudiant.
     */
    public function test_student_registration(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('#prenoms', 'Jean')
                    ->type('#nom', 'Dupont')
                    ->type('#email', 'student.dusk@example.com')
                    ->type('#phone_call', '+22990000000')
                    ->type('#phone_whatsapp', '+22990000000')
                    ->type('#password', 'password123')
                    ->type('#password-confirm', 'password123')
                    ->press('button[type="submit"]')
                    ->waitForLocation('/email/verify', 15)
                    ->assertPathIs('/email/verify');
        });

        // Vérification en base de données que l'utilisateur est bien créé avec le rôle étudiant
        $user = User::where('email', 'student.dusk@example.com')->first();
        $this->assertNotNull($user, "L'utilisateur n'a pas été créé en base de données.");
        $this->assertEquals('student', $user->role, "Le rôle par défaut doit être 'student'.");
        $this->assertEquals('Jean', $user->prenoms);
        $this->assertEquals('Dupont', $user->nom);
    }

    /**
     * Test complet : Connexion et accès au tableau de bord étudiant.
     */
    public function test_student_login_and_access_dashboard(): void
    {
        // Création de l'utilisateur étudiant et validation de son email
        $user = User::create([
            'name' => 'Jean',
            'prenoms' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'student.dusk@example.com',
            'role' => 'student',
            'password' => bcrypt('password123'),
        ]);

        $user->markEmailAsVerified();

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('#email', 'student.dusk@example.com')
                    ->type('#password', 'password123')
                    ->press('button[type="submit"]')
                    ->waitForLocation('/dashboard', 15)
                    ->assertPathIs('/dashboard')
                    ->waitForText('Mes Formations', 15)
                    ->assertSee('Mes Formations');
        });
    }
}
