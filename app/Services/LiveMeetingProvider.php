<?php

namespace App\Services;

use App\Models\LiveServer;
use App\Models\LiveSetting;
use App\Models\LiveSession;
use App\Models\User;

class LiveMeetingProvider
{
    public function __construct(
        private readonly LiveKitTokenService $liveKit,
    ) {}

    public function defaultProvider(): string
    {
        return \App\Models\LiveSetting::getLiveProvider();
    }

    public function resolveProvider(?LiveServer $server = null): string
    {
        if ($server && in_array($server->provider, ['livekit', 'jitsi'], true)) {
            return $server->provider;
        }

        return $this->defaultProvider();
    }

    public function usesLiveKit(?LiveServer $server = null): bool
    {
        return $this->resolveProvider($server) === 'livekit';
    }

    /**
     * بيانات الغرفة لواجهات Blade (LiveKit أو Jitsi).
     *
     * @return array{
     *   provider: string,
     *   jitsiDomain: string,
     *   livekitUrl: string|null,
     *   livekitToken: string|null,
     *   livekitHost: string|null,
     *   livekitConfigured: bool
     * }
     */
    public function roomPayload(LiveSession $session, User $user, bool $isHost = false): array
    {
        $server = $session->server;
        $provider = $this->resolveProvider($server);
        $jitsiDomain = $server?->normalized_domain ?: LiveSetting::getJitsiDomain();

        $payload = [
            'provider' => $provider,
            'jitsiDomain' => $jitsiDomain,
            'livekitUrl' => null,
            'livekitToken' => null,
            'livekitHost' => null,
            'livekitConfigured' => $this->liveKit->isConfigured(),
        ];

        if ($provider !== 'livekit') {
            return $payload;
        }

        $payload['livekitUrl'] = $this->liveKit->wsUrl($server);
        $payload['livekitHost'] = $this->liveKit->publicHost($server);

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

    public function preferredLiveKitServer(): ?LiveServer
    {
        return LiveServer::query()
            ->where('status', 'active')
            ->where('provider', 'livekit')
            ->orderByDesc('id')
            ->first();
    }
}
