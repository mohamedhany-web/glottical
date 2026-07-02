<?php

namespace App\Services\Crm;

use App\Models\SalesLead;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CrmLeadService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function createLead(array $data, User $creator): SalesLead
    {
        $role = CrmAccessService::crmRole($creator);
        if (! in_array($role, ['super_admin', 'marketing'], true)) {
            throw new InvalidArgumentException('غير مصرح بإنشاء Lead.');
        }

        $lead = SalesLead::create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'company' => $data['company'] ?? null,
            'source' => $data['source'] ?? SalesLead::SOURCE_OTHER,
            'status' => SalesLead::STATUS_NEW,
            'notes' => $data['notes'] ?? null,
            'interested_advanced_course_id' => $data['interested_advanced_course_id'] ?? null,
            'created_by' => $creator->id,
            'marketing_owner_id' => $creator->id,
            'crm_group_id' => $data['crm_group_id'] ?? null,
        ]);

        CrmAuditService::log('lead_created', $lead, $creator, null, $lead->only([
            'name', 'email', 'phone', 'source', 'status', 'marketing_owner_id',
        ]));

        return $lead;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function updateLead(SalesLead $lead, array $data, User $actor): SalesLead
    {
        if (! CrmAccessService::canEditLead($actor, $lead)) {
            throw new InvalidArgumentException('لا يمكن تعديل هذا الـ Lead بعد التحويل أو التعيين.');
        }

        $old = $lead->only(['name', 'email', 'phone', 'company', 'source', 'notes', 'interested_advanced_course_id']);
        $lead->fill(collect($data)->only([
            'name', 'email', 'phone', 'company', 'source', 'notes', 'interested_advanced_course_id',
        ])->all());
        $lead->save();

        CrmAuditService::log('lead_updated', $lead, $actor, $old, $lead->only(array_keys($old)));

        return $lead->fresh();
    }

    public static function assignToSales(SalesLead $lead, User $salesUser, User $actor, ?int $groupId = null): SalesLead
    {
        if (! CrmAccessService::canAssignLead($actor)) {
            throw new InvalidArgumentException('غير مصرح بتعيين الـ Lead.');
        }
        if ($lead->isClosed()) {
            throw new InvalidArgumentException('الـ Lead مغلق.');
        }

        $old = $lead->only(['assigned_to', 'status', 'crm_group_id', 'assigned_at']);

        $lead->update([
            'assigned_to' => $salesUser->id,
            'assigned_at' => now(),
            'status' => SalesLead::STATUS_ASSIGNED,
            'crm_group_id' => $groupId ?? $lead->crm_group_id,
        ]);

        CrmAuditService::log('lead_assigned', $lead, $actor, $old, [
            'assigned_to' => $salesUser->id,
            'assigned_to_name' => $salesUser->name,
            'status' => SalesLead::STATUS_ASSIGNED,
        ]);

        return $lead->fresh();
    }

    public static function transitionStatus(SalesLead $lead, string $toStatus, User $actor, ?string $note = null): SalesLead
    {
        if (! CrmAccessService::canTransitionStatus($actor, $lead, $toStatus)) {
            throw new InvalidArgumentException('انتقال الحالة غير مسموح.');
        }

        return DB::transaction(function () use ($lead, $toStatus, $actor, $note) {
            $oldStatus = $lead->status;
            $updates = ['status' => $toStatus];

            if ($toStatus === SalesLead::STATUS_CLOSED_LOST && $note) {
                $updates['lost_reason'] = $note;
            }
            if ($toStatus === SalesLead::STATUS_CLOSED_WON) {
                $updates['converted_at'] = now();
            }

            if ($note && $toStatus !== SalesLead::STATUS_CLOSED_LOST) {
                $prefix = '['.now()->toDateTimeString().'] ';
                $updates['notes'] = trim(($lead->notes ?? '')."\n\n".$prefix.$note);
            }

            $lead->update($updates);

            $action = match ($toStatus) {
                SalesLead::STATUS_CLOSED_WON => 'lead_closed_won',
                SalesLead::STATUS_CLOSED_LOST => 'lead_closed_lost',
                default => 'lead_status_changed',
            };

            CrmAuditService::log($action, $lead, $actor, ['status' => $oldStatus], [
                'status' => $toStatus,
                'note' => $note,
            ]);

            return $lead->fresh();
        });
    }

    public static function linkOrder(SalesLead $lead, int $orderId, User $actor): SalesLead
    {
        $lead->update([
            'order_id' => $orderId,
            'converted_order_id' => $orderId,
        ]);

        CrmAuditService::log('lead_updated', $lead, $actor, null, ['order_id' => $orderId]);

        return $lead->fresh();
    }

    public static function addNote(SalesLead $lead, string $note, User $actor): SalesLead
    {
        if (! CrmAccessService::canViewLead($actor, $lead)) {
            throw new InvalidArgumentException('غير مصرح.');
        }

        $prefix = '['.now()->toDateTimeString().'] '.$actor->name.': ';
        $lead->update([
            'notes' => trim(($lead->notes ?? '')."\n\n".$prefix.$note),
        ]);

        CrmAuditService::log('lead_note_added', $lead, $actor, null, ['note' => $note]);

        return $lead->fresh();
    }
}
