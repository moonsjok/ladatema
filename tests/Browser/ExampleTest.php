<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * Test que la page d'accueil est accessible.
     */
    public function test_homepage_is_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPathIs('/');
        });
    }

    /**
     * Test que la page de connexion s'affiche avec le formulaire.
     */
    public function test_login_page_displays_form(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->assertPresent('#email')
                    ->assertPresent('#password');
        });
    }
}
