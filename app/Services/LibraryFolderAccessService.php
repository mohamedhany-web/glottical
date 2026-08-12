<?php

namespace App\Services;

use App\Models\LibraryFolder;
use App\Models\StudentServiceEntitlement;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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

        if (! Schema::hasColumn('library_folders', 'requires_library_entitlement')) {
            return true;
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

        $ents = StudentServiceEntitlement::query()
            ->forUser((int) $user->id)
            ->active()
            ->where('includes_libraries', true)
            ->get();

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
     * سنوات يمكن للطالب رؤية فولدراتها المقيّدة.
     *
     * @return list<int|string> '*' يعني كل السنوات عبر باقة عامة
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

    /**
     * فولدرات ظاهرة للطالب: المجانية دائماً + المقيّدة حسب باقة السنة.
     */
    public static function foldersVisibleTo(User $user, string $kind = 'materials'): Builder
    {
        $q = LibraryFolder::query()
            ->active()
            ->ordered()
            ->ofKind($kind);

        if (! Schema::hasColumn('library_folders', 'requires_library_entitlement')) {
            return $q;
        }

        $years = self::accessibleYearIds($user);

        return $q->where(function (Builder $inner) use ($years) {
            $inner->where('requires_library_entitlement', false);

            if (in_array('*', $years, true)) {
                $inner->orWhere('requires_library_entitlement', true);
            } elseif ($years !== []) {
                $inner->orWhere(function (Builder $gated) use ($years) {
                    $gated->where('requires_library_entitlement', true)
                        ->where(function (Builder $yearQ) use ($years) {
                            $yearQ->whereIn('academic_year_id', $years)
                                ->orWhereNull('academic_year_id');
                        });
                });
            }
        });
    }

    public static function resolveFolderFromParam(string|int|null $folderId): ?LibraryFolder
    {
        if ($folderId === null || $folderId === '' || $folderId === 'none') {
            return null;
        }

        return LibraryFolder::query()
            ->active()
            ->where(function (Builder $query) use ($folderId) {
                if (ctype_digit((string) $folderId)) {
                    $query->where('id', (int) $folderId);
                } else {
                    $query->where('slug', (string) $folderId);
                }
            })
            ->first();
    }
}
