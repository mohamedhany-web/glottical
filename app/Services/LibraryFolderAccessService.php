<?php

namespace App\Services;

use App\Models\LibraryFolder;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

/**
 * بوابة ظهور فولدرات المكتبة (ماتريال/فيديو) حسب السنة وباقة المكتبات.
 */
class LibraryFolderAccessService
{
    public static function canAccessFolder(?User $user, LibraryFolder $folder): bool
    {
        if (! $user || ! $folder->is_active) {
            return false;
        }

        if (! $folder->requires_library_entitlement) {
            return true;
        }

        return self::hasLibraryEntitlementForYear($user, $folder->academic_year_id ? (int) $folder->academic_year_id : null);
    }

    public static function hasLibraryEntitlementForYear(User $user, ?int $academicYearId): bool
    {
        if (! Schema::hasTable('student_service_entitlements')) {
            return false;
        }

        StudentEntitlementService::expireStaleForUser((int) $user->id);

        $query = StudentServiceEntitlement::query()
            ->forUser((int) $user->id)
            ->active()
            ->where('includes_libraries', true);

        $ents = $query->get();
        if ($ents->isEmpty()) {
            return false;
        }

        foreach ($ents as $e) {
            // باقة عامة بدون سنة = تفتح كل السنوات
            if (! $e->academic_year_id) {
                return true;
            }
            if ($academicYearId && (int) $e->academic_year_id === (int) $academicYearId) {
                return true;
            }
        }

        return false;
    }

    /**
     * سنوات يمكن للطالب رؤية فولدراتها.
     *
     * @return list<int|null> null يعني «كل السنوات» عبر باقة عامة
     */
    public static function accessibleYearIds(User $user): array
    {
        if (! Schema::hasTable('student_service_entitlements')) {
            return [];
        }

        StudentEntitlementService::expireStaleForUser((int) $user->id);

        $ents = StudentServiceEntitlement::query()
            ->forUser((int) $user->id)
            ->active()
            ->where('includes_libraries', true)
            ->get(['academic_year_id']);

        if ($ents->isEmpty()) {
            return [];
        }

        if ($ents->contains(fn ($e) => ! $e->academic_year_id)) {
            return ['*'];
        }

        return $ents->pluck('academic_year_id')->map(fn ($id) => (int) $id)->unique()->values()->all();
    }

    public static function foldersVisibleTo(User $user, string $kind = 'materials')
    {
        $years = self::accessibleYearIds($user);
        if ($years === []) {
            return LibraryFolder::query()->whereRaw('1=0');
        }

        $q = LibraryFolder::query()
            ->active()
            ->ordered()
            ->where(function ($inner) use ($kind) {
                $inner->where('kind', $kind)->orWhere('kind', 'both');
            });

        if (in_array('*', $years, true)) {
            return $q;
        }

        return $q->where(function ($inner) use ($years) {
            $inner->whereIn('academic_year_id', $years)
                ->orWhere('requires_library_entitlement', false);
        });
    }
}
