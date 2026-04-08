<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SecurityPathsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_no_system_paths_exposed_in_errors()
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        
        // Créer un média
        $file = UploadedFile::fake()->image('test.jpg', 100, 100);
        $media = $user->addMedia($file)
            ->withCustomProperties(['name' => 'Test', 'is_public' => true])
            ->toMediaCollection('images');
        
        // Accéder à une URL qui n'existe pas
        $response = $this->actingAs($user)->get('/media-file/999999');
        
        // Vérifier que la page d'erreur ne contient pas de chemins système
        $response->assertStatus(404);
        $response->assertSee('Le fichier demandé n\'existe pas');
        
        // Vérifier l'absence de chemins système dans la réponse
        $this->assertStringNotContainsString('D:\www\ladatema', $response->getContent());
        $this->assertStringNotContainsString('storage/app', $response->getContent());
        $this->assertStringNotContainsString('.mp4', $response->getContent());
        $this->assertStringNotContainsString('.jpg', $response->getContent());
    }

    public function test_media_replacement_error_no_path_exposure()
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        
        // Créer un média initial
        $file = UploadedFile::fake()->image('original.jpg', 100, 100);
        $media = $user->addMedia($file)
            ->withCustomProperties(['name' => 'Original'])
            ->toMediaCollection('images');
        
        // Simuler une erreur en utilisant un mauvais type de fichier
        $invalidFile = UploadedFile::fake()->create('test.txt', 100);
        
        $response = $this->actingAs($user)->put('/media/images/' . $media->id, [
            'name' => 'Updated',
            'file' => $invalidFile
        ]);
        
        // Vérifier que la redirection fonctionne
        $response->assertRedirect();
        
        // Suivre la redirection pour voir le message d'erreur
        $followResponse = $this->followRedirects($response);
        
        // Vérifier l'absence de chemins système dans les messages d'erreur
        $this->assertStringNotContainsString('D:\www\ladatema', $followResponse->getContent());
        $this->assertStringNotContainsString('storage/app', $followResponse->getContent());
        $this->assertStringNotContainsString('D:\\', $followResponse->getContent());
        $this->assertStringNotContainsString('/var/www', $followResponse->getContent());
        // Les extensions dans les messages de validation sont acceptables
    }
}
