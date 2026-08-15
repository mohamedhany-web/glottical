<?php

namespace Tests\Feature;

use App\Models\ClassroomMeeting;
use App\Models\ClassroomMeetingParticipant;
use App\Models\CurriculumLibraryItem;
use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumLibrarySection;
use App\Models\CurriculumPresentationDerivative;
use App\Models\User;
use App\Services\ClassroomCurriculumPresentService;
use App\Services\CurriculumPresentationViewerService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ClassroomCurriculumPresenterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('app.key', 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=');
        Config::set('filesystems.disks.r2', [
            'driver' => 'local',
            'root' => storage_path('framework/testing/r2-curriculum-present'),
            'url' => 'https://cdn.example.com',
        ]);
        Storage::forgetDisk('r2');
        if (! is_dir(storage_path('framework/testing/r2-curriculum-present'))) {
            mkdir(storage_path('framework/testing/r2-curriculum-present'), 0777, true);
        }

        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('password')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('classroom_meetings')) {
            Schema::create('classroom_meetings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('consultation_request_id')->nullable();
                $table->string('code', 32)->nullable();
                $table->string('room_name')->nullable();
                $table->string('title')->nullable();
                $table->unsignedInteger('max_participants')->nullable();
                $table->unsignedInteger('participants_peak')->nullable();
                $table->unsignedInteger('planned_duration_minutes')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('ended_at')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('classroom_meeting_participants')) {
            Schema::create('classroom_meeting_participants', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('classroom_meeting_id');
                $table->string('token', 64)->unique();
                $table->string('display_name', 120)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('joined_at')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->timestamp('left_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_items')) {
            Schema::create('curriculum_library_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->unsignedBigInteger('category_id')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_sections')) {
            Schema::create('curriculum_library_sections', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_item_id');
                $table->string('title')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_library_materials')) {
            Schema::create('curriculum_library_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('curriculum_library_section_id')->nullable();
                $table->string('title')->nullable();
                $table->string('path');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('original_name')->nullable();
                $table->string('file_kind', 20)->default('other');
                $table->boolean('view_in_platform')->default(true);
                $table->boolean('allow_download')->default(false);
                $table->unsignedInteger('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('curriculum_presentation_derivatives')) {
            Schema::create('curriculum_presentation_derivatives', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 32);
                $table->unsignedBigInteger('source_id');
                $table->string('storage_disk', 32)->default('r2');
                $table->string('manifest_path')->nullable();
                $table->string('status', 32)->default('pending');
                $table->unsignedInteger('slide_count')->nullable();
                $table->unsignedInteger('width')->nullable();
                $table->unsignedInteger('height')->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->string('source_checksum')->nullable();
                $table->text('error_message')->nullable();
                $table->string('engine')->nullable();
                $table->timestamp('ready_at')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('subscription_plan_id')->nullable();
                $table->string('status')->default('active');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_curriculum_presenter_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('student.classroom.curriculum.catalog'));
        $this->assertTrue(Route::has('student.classroom.curriculum.present'));
        $this->assertTrue(Route::has('student.classroom.curriculum.state'));
        $this->assertTrue(Route::has('student.classroom.curriculum.slide.update'));
        $this->assertTrue(Route::has('student.classroom.curriculum.stop'));
        $this->assertTrue(Route::has('student.classroom.curriculum.slide'));
        $this->assertTrue(Route::has('student.classroom.curriculum.thumb'));
        $this->assertTrue(Route::has('instructor.classroom.curriculum.catalog'));
        $this->assertTrue(Route::has('classroom.join.curriculum.state'));
        $this->assertTrue(Route::has('classroom.join.curriculum.slide'));
        $this->assertTrue(Route::has('classroom.join.curriculum.thumb'));
    }

    public function test_public_state_never_includes_original_path_or_r2_keys(): void
    {
        [$meeting, $session] = $this->seedReadyPresentationSession();

        $service = app(ClassroomCurriculumPresentService::class);
        $state = $service->publicState($meeting, 'guest');

        $this->assertNotNull($state);
        $this->assertTrue($state['active']);
        $json = json_encode($state);
        $this->assertStringNotContainsString('originals/deck.pptx', $json);
        $this->assertStringNotContainsString('curriculum-library/derivatives/', $json);
        $this->assertArrayHasKey('manifest', $state);
        $this->assertArrayHasKey('slides', $state['manifest']);
        $this->assertStringContainsString('/curriculum/', $state['manifest']['slides'][0]['image_url']);
        $this->assertSame($session['session_id'], $state['session_id']);
        $this->assertArrayNotHasKey('path', $state);
        $this->assertArrayNotHasKey('manifest_path', $state);
    }

    public function test_guest_state_requires_valid_participant_token(): void
    {
        [$meeting] = $this->seedReadyPresentationSession();

        $this->getJson(route('classroom.join.curriculum.state', ['code' => $meeting->code]))
            ->assertStatus(422);

        $this->getJson(route('classroom.join.curriculum.state', [
            'code' => $meeting->code,
            'token' => 'invalid-token',
        ]))->assertStatus(403);

        $participant = ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'token' => 'guest-token-ok',
            'display_name' => 'ضيف',
            'joined_at' => now(),
            'last_seen_at' => now(),
        ]);

        $this->getJson(route('classroom.join.curriculum.state', [
            'code' => $meeting->code,
            'token' => $participant->token,
        ]))->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('active', true)
            ->assertJsonMissing(['path' => 'originals/deck.pptx']);
    }

    public function test_guest_state_rejects_meeting_code_mismatch_and_left_participants(): void
    {
        [$meeting] = $this->seedReadyPresentationSession();

        $participant = ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'token' => 'guest-left',
            'display_name' => 'غادر',
            'joined_at' => now()->subMinute(),
            'left_at' => now(),
        ]);

        $this->getJson(route('classroom.join.curriculum.state', [
            'code' => $meeting->code,
            'token' => $participant->token,
        ]))->assertStatus(403);

        $other = ClassroomMeeting::create([
            'user_id' => $meeting->user_id,
            'code' => 'OTHER1',
            'room_name' => 'Muallimx-OTHER1',
            'started_at' => now(),
        ]);

        $alive = ClassroomMeetingParticipant::create([
            'classroom_meeting_id' => $meeting->id,
            'token' => 'guest-alive',
            'display_name' => 'ضيف',
            'joined_at' => now(),
        ]);

        $this->getJson(route('classroom.join.curriculum.state', [
            'code' => $other->code,
            'token' => $alive->token,
        ]))->assertStatus(403);
    }

    public function test_js_smoke_strings_and_no_second_room(): void
    {
        $presenter = file_get_contents(public_path('js/classroom-curriculum-presenter.js'));

        $this->assertStringContainsString('MxClassroomCurriculumPresenter', $presenter);
        $this->assertStringContainsString('curriculum_open', $presenter);
        $this->assertStringContainsString('_startPoll', $presenter);
        $this->assertStringContainsString('_applyPolledState', $presenter);
        $this->assertStringContainsString('openCurriculumPresenter', $presenter);
        $this->assertStringContainsString('closeCurriculumPresenter', $presenter);
        $this->assertMatchesRegularExpression(
            '/turl\s*=\s*appendToken\(turl,\s*self\.config\.guestToken\)/',
            $presenter
        );
        $this->assertStringNotContainsString('new Room(', $presenter);
        $this->assertStringNotContainsString('room.connect(', $presenter);
        $this->assertStringContainsString('HTTP Cache polling', $presenter);
    }

    public function test_blade_includes_curriculum_button_and_boot_config(): void
    {
        $host = file_get_contents(resource_path('views/student/classroom/room.blade.php'));
        $guest = file_get_contents(resource_path('views/classroom/join.blade.php'));

        $this->assertStringContainsString('mx-ml-btn-curriculum', $host);
        $this->assertStringContainsString('classroom-curriculum-presenter.js', $host);
        $this->assertStringContainsString('classroom.curriculum.catalog', $host);
        $this->assertStringContainsString('MxClassroomCurriculumPresenter.attach', $host);

        $this->assertStringNotContainsString('mx-ml-btn-curriculum', $guest);
        $this->assertStringContainsString('classroom-curriculum-presenter.js', $guest);
        $this->assertStringContainsString('classroom.join.curriculum.state', $guest);
        $this->assertStringContainsString('guestToken: joinToken', $guest);
    }

    /**
     * @return array{0: ClassroomMeeting, 1: array<string, mixed>}
     */
    private function seedReadyPresentationSession(): array
    {
        $user = User::create([
            'name' => 'Host',
            'email' => 'host-curr-'.uniqid().'@example.com',
            'password' => bcrypt('secret'),
        ]);

        $meeting = ClassroomMeeting::create([
            'user_id' => $user->id,
            'code' => 'CURR'.strtoupper(substr(uniqid(), -4)),
            'room_name' => 'Muallimx-TEST',
            'title' => 'Test',
            'planned_duration_minutes' => 60,
            'started_at' => now(),
        ]);

        $item = CurriculumLibraryItem::create([
            'title' => 'منهج اختبار',
            'slug' => 'item-'.uniqid(),
            'is_active' => true,
        ]);

        $section = CurriculumLibrarySection::create([
            'curriculum_library_item_id' => $item->id,
            'title' => 'قسم',
        ]);

        $originalPath = 'originals/deck.pptx';
        Storage::disk('r2')->put($originalPath, 'fake-pptx');

        $material = CurriculumLibraryMaterial::create([
            'curriculum_library_section_id' => $section->id,
            'title' => 'عرض جاهز',
            'path' => $originalPath,
            'storage_disk' => 'r2',
            'file_kind' => 'pptx',
            'view_in_platform' => true,
            'is_active' => true,
        ]);

        $prefix = 'curriculum-library/derivatives/material/'.$material->id.'/v1';
        $manifestPath = $prefix.'/manifest.json';
        $img1 = $prefix.'/slides/slide-1.png';
        $img2 = $prefix.'/slides/slide-2.png';
        Storage::disk('r2')->put($img1, 'png1');
        Storage::disk('r2')->put($img2, 'png2');
        Storage::disk('r2')->put($manifestPath, json_encode([
            'version' => 1,
            'slide_count' => 2,
            'width' => 1280,
            'height' => 720,
            'format' => 'png',
            'slides' => [
                ['index' => 1, 'path' => $img1],
                ['index' => 2, 'path' => $img2],
            ],
        ]));

        CurriculumPresentationDerivative::create([
            'source_type' => CurriculumPresentationDerivative::SOURCE_MATERIAL,
            'source_id' => $material->id,
            'storage_disk' => 'r2',
            'manifest_path' => $manifestPath,
            'status' => CurriculumPresentationDerivative::STATUS_READY,
            'slide_count' => 2,
            'width' => 1280,
            'height' => 720,
            'version' => 1,
            'ready_at' => now(),
        ]);

        // Confirm viewer validates.
        $viewer = app(CurriculumPresentationViewerService::class);
        $derivative = $viewer->resolveReadyDerivative(
            CurriculumPresentationDerivative::SOURCE_MATERIAL,
            (int) $material->id
        );
        $this->assertNotNull($derivative);
        $this->assertNotNull($viewer->loadAndValidateManifest($derivative));

        $session = [
            'session_id' => 'sess-'.uniqid(),
            'item_id' => (int) $item->id,
            'material_id' => (int) $material->id,
            'title' => $material->displayTitle(),
            'current_slide' => 1,
            'slide_count' => 2,
            'started_at' => now()->timestamp,
            'updated_at' => now()->timestamp,
        ];
        Cache::put(ClassroomCurriculumPresentService::cacheKey($meeting), $session, now()->addHour());

        return [$meeting, $session];
    }
}
