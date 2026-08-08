<?php

namespace Tests\Unit;

use App\Services\TutorApplicationStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TutorApplicationStorageTest extends TestCase
{
    public function test_store_photo_and_documents_on_resolved_disk(): void
    {
        Storage::fake('public');
        config(['filesystems.public_media_disk' => 'public']);

        $photo = UploadedFile::fake()->image('teacher.jpg', 400, 400);
        $id = UploadedFile::fake()->create('passport.pdf', 120, 'application/pdf');
        $cert = UploadedFile::fake()->image('certificate.png', 600, 400);
        $video = UploadedFile::fake()->create('intro.mp4', 1024, 'video/mp4');

        $photoPath = TutorApplicationStorage::storePhoto($photo);
        $idPath = TutorApplicationStorage::storeIdDocument($id);
        $certPath = TutorApplicationStorage::storeCertificate($cert);
        $videoPath = TutorApplicationStorage::storeVideo($video);

        $this->assertStringStartsWith('tutor-applications/photos/', $photoPath);
        $this->assertStringStartsWith('tutor-applications/ids/', $idPath);
        $this->assertStringStartsWith('tutor-applications/certificates/', $certPath);
        $this->assertStringStartsWith('tutor-applications/videos/', $videoPath);

        Storage::disk('public')->assertExists($photoPath);
        Storage::disk('public')->assertExists($idPath);
        Storage::disk('public')->assertExists($certPath);
        Storage::disk('public')->assertExists($videoPath);

        $this->assertTrue(TutorApplicationStorage::isPdf($idPath));
        $this->assertFalse(TutorApplicationStorage::isPdf($photoPath));
    }
}
