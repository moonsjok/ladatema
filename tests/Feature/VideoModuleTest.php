<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VideoModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_video_utils_file_exists()
    {
        // Vérifier que le fichier videoUtils.js existe
        $this->assertFileExists(resource_path('js/editor/video/videoUtils.js'));
        
        // Vérifier que le contenu contient les fonctions attendues
        $content = file_get_contents(resource_path('js/editor/video/videoUtils.js'));
        $this->assertStringContainsString('function getCurrentDomain', $content);
        $this->assertStringContainsString('export function isLocalVideo', $content);
        $this->assertStringContainsString('export function normalizeLocalVideoUrl', $content);
        $this->assertStringContainsString('export function getVideoEmbedCode', $content);
    }

    public function test_video_media_creation_and_access()
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        
        // Créer un média avec une image pour le test
        $imageFile = UploadedFile::fake()->image('test-image.jpg', 100, 100);
        $media = $user->addMedia($imageFile)
            ->withCustomProperties([
                'name' => 'Test Media',
                'description' => 'Test Description',
                'is_public' => true
            ])
            ->toMediaCollection('images');
        
        // Vérifier que le média est bien créé
        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('images', $media->collection_name);
        
        // Vérifier l'accès sécurisé
        $response = $this->actingAs($user)->get('/media-file/' . $media->id);
        $response->assertStatus(200);
        
        // Vérifier que l'URL est correcte
        $expectedUrl = 'https://ladatema.kom/media-file/' . $media->id;
        $this->assertEquals($expectedUrl, route('media.file.secure', $media->id));
    }

    public function test_video_file_replacement_maintains_url()
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        
        // Créer un média initial
        $originalFile = UploadedFile::fake()->image('original.jpg', 100, 100);
        $media = $user->addMedia($originalFile)
            ->withCustomProperties(['name' => 'Original Media'])
            ->toMediaCollection('images');
        
        $originalId = $media->id;
        $originalUrl = '/media-file/' . $media->id;
        
        // Remplacer le fichier
        $newFile = UploadedFile::fake()->image('replacement.jpg', 200, 200);
        $response = $this->actingAs($user)->put('/media/images/' . $media->id, [
            'name' => 'Updated Media',
            'description' => 'Updated Description',
            'is_public' => true,
            'file' => $newFile
        ]);
        
        // Vérifier que le remplacement a fonctionné
        $response->assertRedirect();
        
        // Vérifier que l'URL reste la même
        $updatedMedia = Media::find($originalId);
        $this->assertEquals($originalId, $updatedMedia->id);
        $this->assertEquals($originalUrl, '/media-file/' . $updatedMedia->id);
        
        // Vérifier que le fichier a été remplacé
        $this->assertEquals('replacement.jpg', $updatedMedia->file_name);
        $this->assertEquals('Updated Media', $updatedMedia->getCustomProperty('name'));
    }
}
