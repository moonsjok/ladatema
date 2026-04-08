<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaReplacementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_media_replacement_keeps_same_id()
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        
        // Créer un média initial
        $originalFile = UploadedFile::fake()->image('original.jpg', 100, 100)->size(100);
        $media = $user->addMedia($originalFile)
            ->withCustomProperties(['name' => 'Original', 'description' => 'Original desc'])
            ->toMediaCollection('images');
        
        $originalId = $media->id;
        $originalFileName = $media->file_name;
        
        // Préparer le nouveau fichier
        $newFile = UploadedFile::fake()->image('replacement.jpg', 200, 200)->size(200);
        
        // Simuler le remplacement
        $response = $this->actingAs($user)->put('/media/images/' . $media->id, [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'is_public' => true,
            'file' => $newFile
        ]);
        
        // Vérifier que le média a le même ID
        $updatedMedia = Media::find($originalId);
        $this->assertNotNull($updatedMedia);
        $this->assertEquals($originalId, $updatedMedia->id);
        
        // Vérifier que le fichier a été remplacé (nom différent)
        $this->assertEquals('replacement.jpg', $updatedMedia->file_name);
        $this->assertNotEquals($originalFileName, $updatedMedia->file_name);
        
        // Vérifier que les métadonnées sont mises à jour
        $this->assertEquals('Updated Name', $updatedMedia->getCustomProperty('name'));
        $this->assertEquals('Updated Description', $updatedMedia->getCustomProperty('description'));
        $this->assertTrue($updatedMedia->getCustomProperty('is_public'));
        
        // Vérifier la redirection
        $response->assertRedirect('/media/images');
        $response->assertSessionHas('success');
    }
}
