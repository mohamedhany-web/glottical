<?php

namespace Tests\Feature;

use App\Models\LiveServer;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\Support\BuildsFeatureSchema;
use Tests\TestCase;

class LiveKitRoomProviderTest extends TestCase
{
    use BuildsFeatureSchema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildFeatureSchema();

        Schema::create('live_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain');
            $table->string('provider', 32)->default('livekit');
            $table->string('status')->default('active');
            $table->string('ip_address')->nullable();
            $table->unsignedInteger('max_participants')->default(100);
            $table->unsignedInteger('current_load')->default(0);
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable();
            $table->foreignId('instructor_id');
            $table->foreignId('server_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('room_name');
            $table->string('status')->default('scheduled');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('max_participants')->nullable();
            $table->boolean('is_recorded')->default(false);
            $table->boolean('allow_chat')->default(true);
            $table->boolean('allow_screen_share')->default(true);
            $table->boolean('require_enrollment')->default(false);
            $table->boolean('mute_on_join')->default(false);
            $table->boolean('video_off_on_join')->default(false);
            $table->string('password')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('session_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id');
            $table->foreignId('user_id');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('left_at')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('role_in_session')->nullable();
            $table->timestamps();
        });

        config([
            'livekit.provider' => 'livekit',
            'livekit.livekit.api_key' => 'APItestkey',
            'livekit.livekit.api_secret' => 'test-secret-value-1234567890',
            'livekit.livekit.url' => 'wss://live.glottical.com',
            'livekit.livekit.host' => 'live.glottical.com',
        ]);
    }

    public function test_instructor_room_uses_livekit_client_not_jitsi(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instructor',
            'is_active' => true,
            'password' => Hash::make('secret'),
        ]);

        $server = LiveServer::create([
            'name' => 'Glottical LiveKit',
            'domain' => 'live.glottical.com',
            'provider' => 'livekit',
            'status' => 'active',
            'ip_address' => '187.124.36.228',
            'max_participants' => 100,
        ]);

        $session = LiveSession::create([
            'instructor_id' => $instructor->id,
            'server_id' => $server->id,
            'title' => 'اختبار LiveKit',
            'room_name' => 'glottical-livekit-test',
            'status' => 'live',
            'started_at' => now(),
            'require_enrollment' => false,
        ]);

        $response = $this->actingAs($instructor)
            ->get(route('instructor.live-sessions.room', $session));

        $response->assertOk();
        $response->assertSee('livekit-client', false);
        $response->assertSee('live.glottical.com', false);
        $response->assertDontSee('external_api.js', false);
        $response->assertDontSee('JitsiMeetExternalAPI', false);
    }
}
