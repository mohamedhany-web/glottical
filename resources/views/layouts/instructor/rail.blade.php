@php
    $railUser = auth()->user();
    $railNotifs = collect();
    try {
        $railNotifs = $railUser->customNotifications()
            ->whereIn('audience', [null, 'instructor', 'teacher'])
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    } catch (\Throwable $e) {
    }

    $railBookings = collect();
    if (\Illuminate\Support\Facades\Schema::hasTable('tutoring_group_bookings')) {
        $railBookings = \App\Models\TutoringGroupBooking::query()
            ->where('instructor_id', $railUser->id)
            ->where('status', 'confirmed')
            ->where('starts_at', '>=', now()->subHours(2))
            ->orderBy('starts_at')
            ->limit(5)
            ->with(['user:id,name,profile_image', 'tutoringGroup:id,title'])
            ->get();
    }

    $icoToggle = true;
@endphp

{{-- SnowUI RightSidebar 280px --}}
<div>
    <h3 class="su-rail-h">{{ __('instructor.notifications') }}</h3>
    @forelse($railNotifs as $n)
        @php $icoToggle = !$icoToggle; @endphp
        <a href="{{ $n->action_url ?: (Route::has('instructor.notifications.index') ? route('instructor.notifications.index') : '#') }}" class="su-rail-item">
            <span class="su-rail-ico {{ $icoToggle ? 'su-rail-ico--a' : 'su-rail-ico--b' }}"><i class="fas fa-bell"></i></span>
            <div class="min-w-0">
                <div class="su-rail-t truncate">{{ $n->title }}</div>
                <div class="su-rail-m">{{ optional($n->created_at)->diffForHumans() }}</div>
            </div>
        </a>
    @empty
        <div class="su-rail-item">
            <span class="su-rail-ico su-rail-ico--a"><i class="fas fa-bug"></i></span>
            <div>
                <div class="su-rail-t">{{ __('instructor.no_notifications') }}</div>
                <div class="su-rail-m">{{ __('instructor.just_now') }}</div>
            </div>
        </div>
        <div class="su-rail-item">
            <span class="su-rail-ico su-rail-ico--b"><i class="fas fa-user"></i></span>
            <div>
                <div class="su-rail-t">{{ __('instructor.welcome') }}</div>
                <div class="su-rail-m">{{ __('instructor.just_now') }}</div>
            </div>
        </div>
    @endforelse
</div>

<div>
    <h3 class="su-rail-h">{{ __('instructor.activities') }}</h3>
    @forelse($railBookings as $b)
        <a href="{{ Route::has('instructor.tutoring-bookings.show') ? route('instructor.tutoring-bookings.show', $b) : '#' }}" class="su-rail-item">
            <span class="su-rail-avatar">
                @if($b->user?->profile_image)
                    <img src="{{ $b->user->profile_image_url }}" alt="">
                @else
                    {{ mb_substr($b->user?->name ?? 'G', 0, 1) }}
                @endif
            </span>
            <div class="min-w-0">
                <div class="su-rail-t truncate">{{ $b->tutoringGroup?->title ?? __('instructor.group_session') }}</div>
                <div class="su-rail-m">{{ optional($b->starts_at)->diffForHumans() }}</div>
            </div>
        </a>
    @empty
        <p class="su-rail-m" style="padding:8px">{{ __('instructor.no_upcoming_activity') }}</p>
    @endforelse
</div>

<div>
    <h3 class="su-rail-h">{{ __('instructor.contacts') }}</h3>
    @php
        $contacts = $railBookings->pluck('user')->filter()->unique('id')->take(6);
    @endphp
    @forelse($contacts as $c)
        <div class="su-rail-item">
            <span class="su-rail-avatar">
                @if($c->profile_image)
                    <img src="{{ $c->profile_image_url }}" alt="">
                @else
                    {{ mb_substr($c->name, 0, 1) }}
                @endif
            </span>
            <div class="su-rail-t truncate">{{ $c->name }}</div>
        </div>
    @empty
        <p class="su-rail-m" style="padding:8px">{{ __('instructor.contacts_from_bookings') }}</p>
    @endforelse
</div>
