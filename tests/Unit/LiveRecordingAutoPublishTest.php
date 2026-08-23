<?php

namespace Tests\Unit;

use App\Models\LiveRecording;
use App\Models\LiveSession;
use App\Models\User;
use App\Services\LiveRecordingAutoPublishService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class LiveRecordingAutoPublishTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();

        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id');
            $table->string('title');
            $table->string('room_name');
            $table->string('status')->default('ended');
            $table->timestamps();
        });

        Schema::create('live_recordings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id');
            $table->string('title')->nullable();
            $table->string('file_path');
            $table->string('storage_disk')->default('r2');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->unsignedInteger('duration_seconds')->default(0);
            $table->string('status')->default('ready');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function test_auto_publish_marks_recording_visible_to_students(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $session = LiveSession::query()->create([
            'instructor_id' => $instructor->id,
            'title' => 'حصة تجريبية',
            'room_name' => 'room-1',
            'status' => 'ended',
        ]);

        $recording = LiveRecording::query()->create([
            'session_id' => $session->id,
            'title' => '',
            'file_path' => 'live-session-audio/2026/01/session-1-audio.webm',
            'storage_disk' => 'r2',
            'status' => 'ready',
            'is_published' => false,
        ]);

        LiveRecordingAutoPublishService::publishForSession($recording, $session);

        $recording->refresh();
        $this->assertTrue($recording->is_published);
        $this->assertStringContainsString('حصة تجريبية', $recording->title);
    }
}
