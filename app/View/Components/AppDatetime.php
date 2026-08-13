<?php

namespace App\View\Components;

use App\Support\AppTimezone;
use Carbon\CarbonInterface;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppDatetime extends Component
{
    public string $primary;

    public ?string $secondary;

    public function __construct(
        public ?CarbonInterface $at = null,
        public ?string $timezone = null,
        public string $pattern = 'D j M · g:i A',
    ) {
        $viewer = $timezone
            ?: AppTimezone::forUser(auth()->user());
        $parts = AppTimezone::dualLabel($at, $viewer, app()->getLocale(), $pattern);
        $this->primary = $parts['primary'];
        $this->secondary = $parts['secondary'];
    }

    public function render(): View|Closure|string
    {
        return view('components.app-datetime');
    }
}
