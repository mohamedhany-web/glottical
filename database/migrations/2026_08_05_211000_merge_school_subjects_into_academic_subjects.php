<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academic_subjects')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                if (! Schema::hasColumn('academic_subjects', 'slug')) {
                    $table->string('slug')->nullable()->after('code');
                }
            });

            // Make academic_year_id nullable for global school catalog subjects
            if (Schema::hasColumn('academic_subjects', 'academic_year_id')) {
                try {
                    Schema::table('academic_subjects', function (Blueprint $table) {
                        $table->dropForeign(['academic_year_id']);
                    });
                } catch (\Throwable) {
                    // Try common FK name variants
                    try {
                        Schema::table('academic_subjects', function (Blueprint $table) {
                            $table->dropForeign('academic_subjects_academic_year_id_foreign');
                        });
                    } catch (\Throwable) {
                    }
                }

                DB::statement('ALTER TABLE academic_subjects MODIFY academic_year_id BIGINT UNSIGNED NULL');

                Schema::table('academic_subjects', function (Blueprint $table) {
                    $table->foreign('academic_year_id')
                        ->references('id')
                        ->on('academic_years')
                        ->nullOnDelete();
                });
            }

            // Backfill slugs for existing rows
            $missing = DB::table('academic_subjects')->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            })->get();
            foreach ($missing as $row) {
                $slug = Str::slug($row->name ?: ($row->code ?: 'subject-'.$row->id)) ?: ('subject-'.$row->id);
                if ($row->academic_year_id) {
                    $slug .= '-y'.$row->academic_year_id;
                }
                $base = $slug;
                $n = 2;
                while (DB::table('academic_subjects')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $slug = $base.'-'.$n;
                    $n++;
                }
                DB::table('academic_subjects')->where('id', $row->id)->update(['slug' => $slug]);
            }

            $indexes = collect(DB::select('SHOW INDEX FROM academic_subjects'))->pluck('Key_name')->unique();
            if (! $indexes->contains('academic_subjects_slug_unique')) {
                Schema::table('academic_subjects', function (Blueprint $table) {
                    $table->unique('slug');
                });
            }
        }

        $idMap = [];

        if (Schema::hasTable('school_subjects') && Schema::hasTable('academic_subjects')) {
            $schoolSubjects = DB::table('school_subjects')->orderBy('sort_order')->orderBy('name')->get();

            foreach ($schoolSubjects as $ss) {
                $existing = null;
                if (! empty($ss->slug)) {
                    $existing = DB::table('academic_subjects')
                        ->where('slug', $ss->slug)
                        ->whereNull('academic_year_id')
                        ->first();
                }
                if (! $existing) {
                    $existing = DB::table('academic_subjects')
                        ->where('name', $ss->name)
                        ->whereNull('academic_year_id')
                        ->first();
                }

                if ($existing) {
                    DB::table('academic_subjects')->where('id', $existing->id)->update([
                        'slug' => $existing->slug ?: ($ss->slug ?: null),
                        'description' => $existing->description ?: ($ss->description ?? null),
                        'icon' => $existing->icon ?: ($ss->icon ?? $existing->icon),
                        'order' => $existing->order ?: ((int) ($ss->sort_order ?? 0)),
                        'is_active' => (bool) ($ss->is_active ?? $existing->is_active),
                        'updated_at' => now(),
                    ]);
                    $idMap[(int) $ss->id] = (int) $existing->id;
                    continue;
                }

                $slug = $ss->slug ?: Str::slug($ss->name ?: 'subject');
                $baseSlug = $slug;
                $n = 2;
                while (DB::table('academic_subjects')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$n;
                    $n++;
                }

                $code = 'SCH-'.Str::upper(Str::substr(Str::slug($ss->name ?: $slug, ''), 0, 8));
                $code = $code === 'SCH-' ? ('SCH-'.$ss->id) : $code;
                $baseCode = $code;
                $n = 2;
                while (DB::table('academic_subjects')->where('code', $code)->exists()) {
                    $code = $baseCode.'-'.$n;
                    $n++;
                }

                $icon = trim((string) ($ss->icon ?? ''));
                if ($icon !== '' && ! str_starts_with($icon, 'fa') && ! str_contains($icon, ' ')) {
                    $icon = 'fas fa-'.$icon;
                }
                if ($icon === '') {
                    $icon = 'fas fa-book';
                }

                $newId = DB::table('academic_subjects')->insertGetId([
                    'academic_year_id' => null,
                    'name' => $ss->name,
                    'code' => $code,
                    'slug' => $slug,
                    'description' => $ss->description ?? null,
                    'icon' => $icon,
                    'color' => '#0B3D91',
                    'order' => (int) ($ss->sort_order ?? 0),
                    'is_active' => (bool) ($ss->is_active ?? true),
                    'created_at' => $ss->created_at ?? now(),
                    'updated_at' => $ss->updated_at ?? now(),
                ]);

                $idMap[(int) $ss->id] = (int) $newId;
            }
        }

        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_groups', 'academic_subject_id')) {
                    $after = Schema::hasColumn('tutoring_groups', 'academic_year_id')
                        ? 'academic_year_id'
                        : 'learning_path';
                    $table->foreignId('academic_subject_id')->nullable()->after($after)
                        ->constrained('academic_subjects')->nullOnDelete();
                }
            });

            if (Schema::hasColumn('tutoring_groups', 'school_subject_id') && $idMap !== []) {
                foreach ($idMap as $oldId => $newId) {
                    DB::table('tutoring_groups')->where('school_subject_id', $oldId)->update([
                        'academic_subject_id' => $newId,
                    ]);
                }
            }

            if (Schema::hasColumn('tutoring_groups', 'school_subject_id')) {
                Schema::table('tutoring_groups', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('school_subject_id');
                });
            }
        }

        Schema::dropIfExists('school_subjects');
    }

    public function down(): void
    {
        if (! Schema::hasTable('school_subjects')) {
            Schema::create('school_subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon', 64)->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
            });
        }

        $map = [];
        if (Schema::hasTable('academic_subjects')) {
            $rows = DB::table('academic_subjects')
                ->whereNull('academic_year_id')
                ->whereNotNull('slug')
                ->orderBy('order')
                ->get();

            foreach ($rows as $as) {
                $id = DB::table('school_subjects')->insertGetId([
                    'name' => $as->name,
                    'slug' => $as->slug ?: ('subject-'.$as->id),
                    'description' => $as->description,
                    'icon' => $as->icon,
                    'is_active' => $as->is_active,
                    'sort_order' => $as->order ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $map[(int) $as->id] = (int) $id;
            }
        }

        if (Schema::hasTable('tutoring_groups') && Schema::hasColumn('tutoring_groups', 'academic_subject_id')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_groups', 'school_subject_id')) {
                    $table->foreignId('school_subject_id')->nullable()
                        ->constrained('school_subjects')->nullOnDelete();
                }
            });
            foreach ($map as $asId => $ssId) {
                DB::table('tutoring_groups')->where('academic_subject_id', $asId)->update([
                    'school_subject_id' => $ssId,
                ]);
            }
            Schema::table('tutoring_groups', function (Blueprint $table) {
                $table->dropConstrainedForeignId('academic_subject_id');
            });
        }

        if (Schema::hasTable('academic_subjects') && Schema::hasColumn('academic_subjects', 'slug')) {
            Schema::table('academic_subjects', function (Blueprint $table) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            });
        }
    }
};
