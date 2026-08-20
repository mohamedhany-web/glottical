<?php

namespace App\Services;

use App\Models\LiveServer;
use App\Models\LiveSetting;
use App\Models\LiveSession;
use App\Models\User;

/**
 * مزوّد غرف البث لمنصة Glottical — LiveKit فقط.
 */
class LiveMeetingProvider
{
    public function __construct(
        private readonly LiveKitTokenService $liveKit,
    ) {}

    public function defaultProvider(): string
    {
        return 'livekit';
    }

    public function resolveProvider(?LiveServer $server = null): string
    {
        return 'livekit';
    }

    public function usesLiveKit(?LiveServer $server = null): bool
    {
        return true;
    }

    /**
     * @return array{
     *   provider: string,
     *   livekitUrl: string|null,
     *   livekitToken: string|null,
     *   livekitHost: string|null,
     *   livekitConfigured: bool
     * }
     */
    public function roomPayload(LiveSession $session, User $user, bool $isHost = false): array
    {
        $server = $session->server ?: $this->preferredLiveKitServer();

        $payload = [
            'provider' => 'livekit',
            'livekitUrl' => $this->liveKit->wsUrl($server),
            'livekitToken' => null,
            'livekitHost' => $this->liveKit->publicHost($server),
            'livekitConfigured' => $this->liveKit->isConfigured(),
        ];

        if ($this->liveKit->isConfigured()) {
            $payload['livekitToken'] = $this->liveKit->createJoinToken(
                $session->room_name,
                $user,
                [
                    'canPublish' => true,
                    'canSubscribe' => true,
                    'canPublishData' => true,
                    'roomAdmin' => $isHost,
                ]
            );
        }

        return $payload;
    }

    /**
     * @return array{provider: string, livekitUrl: string|null, livekitToken: string|null, livekitHost: string|null, livekitConfigured: bool, roomName: string}
     */
    public function classroomPayload(string $roomName, User $user, bool $isHost = false): array
    {
        $server = $this->preferredLiveKitServer();
        $payload = [
            'provider' => 'livekit',
            'livekitUrl' => $this->liveKit->wsUrl($server),
            'livekitToken' => null,
            'livekitHost' => $this->liveKit->publicHost($server),
            'livekitConfigured' => $this->liveKit->isConfigured(),
            'roomName' => $roomName,
        ];

        if ($this->liveKit->isConfigured()) {
            $payload['livekitToken'] = $this->liveKit->createJoinToken(
                $roomName,
                $user,
                [
                    'canPublish' => true,
                    'canSubscribe' => true,
                    'canPublishData' => true,
                    'roomAdmin' => $isHost,
                ]
            );
        }

        return $payload;
    }

    /**
     * @return array{provider: string, livekitUrl: string|null, livekitToken: string|null, livekitHost: string|null, livekitConfigured: bool, roomName: string}
     */
    public function classroomGuestPayload(string $roomName, string $guestToken, string $displayName): array
    {
        $server = $this->preferredLiveKitServer();
        $payload = [
            'provider' => 'livekit',
            'livekitUrl' => $this->liveKit->wsUrl($server),
            'livekitToken' => null,
            'livekitHost' => $this->liveKit->publicHost($server),
            'livekitConfigured' => $this->liveKit->isConfigured(),
            'roomName' => $roomName,
        ];

        if ($this->liveKit->isConfigured()) {
            $payload['livekitToken'] = $this->liveKit->createIdentityToken(
                $roomName,
                'guest-'.substr(hash('sha256', $guestToken), 0, 16),
                $displayName,
                [
                    'canPublish' => true,
                    'canSubscribe' => true,
                    'canPublishData' => true,
                    'metadata' => ['guest' => true, 'display_name' => $displayName],
                ]
            );
        }

        return $payload;
    }

    public function preferredLiveKitServer(): ?LiveServer
    {
        return LiveServer::query()
            ->where('status', 'active')
            ->where('provider', 'livekit')
            ->orderByDesc('id')
            ->first();
    }
}
