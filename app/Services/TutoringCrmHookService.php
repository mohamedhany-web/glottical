<?php

namespace App\Services;

use App\Models\SalesLead;
use App\Models\TutoringGroupBooking;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TutoringCrmHookService
{
    public static function onBookingCreated(TutoringGroupBooking $booking): void
    {
        if (! Schema::hasTable('sales_leads')) {
            return;
        }

        try {
            $email = $booking->contactEmail();
            $phone = $booking->contactPhone();
            $name = $booking->contactName();

            if ($name === '—' && ! $email && ! $phone) {
                return;
            }

            $existing = null;
            if ($email) {
                $existing = SalesLead::query()->where('email', $email)->latest('id')->first();
            }
            if (! $existing && $phone) {
                $existing = SalesLead::query()->where('phone', $phone)->latest('id')->first();
            }

            $noteLine = 'حجز مجموعة tutoring #'.$booking->id.' — '.($booking->tutoringGroup?->title ?? '').' @ '.$booking->starts_at?->format('Y-m-d H:i');

            if ($existing) {
                $existing->update([
                    'notes' => trim(($existing->notes ? $existing->notes."\n" : '').$noteLine),
                ]);

                return;
            }

            $creatorId = $booking->user_id
                ?: User::query()->whereIn('role', ['admin', 'super_admin'])->value('id');

            if (! $creatorId) {
                return;
            }

            SalesLead::create([
                'name' => $name !== '—' ? $name : ($email ?: $phone ?: 'Guest'),
                'email' => $email,
                'phone' => $phone,
                'source' => SalesLead::SOURCE_WEBSITE,
                'status' => SalesLead::STATUS_NEW,
                'notes' => $noteLine.' | source=tutoring_booking',
                'created_by' => $creatorId,
                'marketing_owner_id' => $creatorId,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Tutoring CRM hook failed: '.$e->getMessage(), ['booking_id' => $booking->id]);
        }
    }
}
