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
        if (Schema::hasTable('academic_years')) {
            Schema::table('academic_years', function (Blueprint $table) {
                if (! Schema::hasColumn('academic_years', 'slug')) {
                    $table->string('slug')->nullable()->after('code');
                }
                if (! Schema::hasColumn('academic_years', 'tagline')) {
                    $table->string('tagline')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('academic_years', 'level_number')) {
                    $table->unsignedTinyInteger('level_number')->nullable()->after('tagline');
                }
            });

            // Backfill slug for existing academic years so public school pages work
            $missingSlugs = DB::table('academic_years')->where(function ($q) {
                $q->whereNull('slug')->orWhere('slug', '');
            })->get();
            foreach ($missingSlugs as $row) {
                $slug = Str::slug($row->name ?: ($row->code ?: 'year-'.$row->id)) ?: ('year-'.$row->id);
                $base = $slug;
                $n = 2;
                while (DB::table('academic_years')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                    $slug = $base.'-'.$n;
                    $n++;
                }
                DB::table('academic_years')->where('id', $row->id)->update(['slug' => $slug]);
            }

            $indexes = collect(DB::select('SHOW INDEX FROM academic_years'))->pluck('Key_name')->unique();
            if (! $indexes->contains('academic_years_slug_unique') && Schema::hasColumn('academic_years', 'slug')) {
                Schema::table('academic_years', function (Blueprint $table) {
                    $table->unique('slug');
                });
            }
            if (! $indexes->contains('academic_years_level_number_unique') && Schema::hasColumn('academic_years', 'level_number')) {
                Schema::table('academic_years', function (Blueprint $table) {
                    $table->unique('level_number');
                });
            }
        }

        $idMap = [];

        if (Schema::hasTable('school_years') && Schema::hasTable('academic_years')) {
            $schoolYears = DB::table('school_years')->orderBy('sort_order')->orderBy('level_number')->get();

            foreach ($schoolYears as $sy) {
                $existing = null;
                if (! empty($sy->slug)) {
                    $existing = DB::table('academic_years')->where('slug', $sy->slug)->first();
                }
                if (! $existing && $sy->level_number !== null) {
                    $existing = DB::table('academic_years')->where('level_number', $sy->level_number)->first();
                }
                if (! $existing) {
                    $existing = DB::table('academic_years')->where('name', $sy->name)->first();
                }

                if ($existing) {
                    DB::table('academic_years')->where('id', $existing->id)->update([
                        'slug' => $existing->slug ?: ($sy->slug ?: null),
                        'tagline' => $existing->tagline ?: ($sy->tagline ?? null),
                        'level_number' => $existing->level_number ?: ($sy->level_number ?? null),
                        'description' => $existing->description ?: ($sy->description ?? null),
                        'thumbnail' => $existing->thumbnail ?: ($sy->image_path ?? null),
                        'order' => $existing->order ?: ((int) ($sy->sort_order ?? 0)),
                        'is_active' => (bool) ($sy->is_active ?? $existing->is_active),
                        'updated_at' => now(),
                    ]);
                    $idMap[(int) $sy->id] = (int) $existing->id;
                    continue;
                }

                $code = 'SCH-L'.(int) $sy->level_number;
                $baseCode = $code;
                $n = 2;
                while (DB::table('academic_years')->where('code', $code)->exists()) {
                    $code = $baseCode.'-'.$n;
                    $n++;
                }

                $slug = $sy->slug ?: Str::slug($sy->name ?: $code);
                $baseSlug = $slug;
                $n = 2;
                while (DB::table('academic_years')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.$n;
                    $n++;
                }

                $newId = DB::table('academic_years')->insertGetId([
                    'name' => $sy->name,
                    'code' => $code,
                    'slug' => $slug,
                    'tagline' => $sy->tagline ?? null,
                    'level_number' => $sy->level_number ?? null,
                    'description' => $sy->description ?? null,
                    'thumbnail' => $sy->image_path ?? null,
                    'price' => 0,
                    'icon' => 'fas fa-school',
                    'color' => '#0B3D91',
                    'order' => (int) ($sy->sort_order ?? $sy->level_number ?? 0),
                    'is_active' => (bool) ($sy->is_active ?? true),
                    'created_at' => $sy->created_at ?? now(),
                    'updated_at' => $sy->updated_at ?? now(),
                ]);

                $idMap[(int) $sy->id] = (int) $newId;
            }
        }

        if (Schema::hasTable('tutoring_groups')) {
            Schema::table('tutoring_groups', function (Blueprint $table) {
                if (! Schema::hasColumn('tutoring_groups', 'academic_year_id')) {
                    $table->foreignId('academic_year_id')->nullable()->after('learning_path')
                        ->constrained('academic_years')->nullOnDelete();
                }
            });

            if (Schema::hasColumn('tutoring_groups', 'school_year_id') && $idMap !== []) {
                foreach ($idMap as $oldId => $newId) {
                    DB::table('tutoring_groups')->where('school_year_id', $oldId)->update([
                        'academic_year_id' => $newId,
                    ]);
                }
            }

            if (Schema::hasColumn('tutoring_groups', 'school_year_id')) {
                Schema::table('tutoring_groups', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('school_year_id');
                });
            }
        }

        if (Schema::hasTable('free_trial_bookings')) {
            Schema::table('free_trial_bookings', function (Blueprint $table) {
                if (! Schema::hasColumn('free_trial_bookings', 'recommended_academic_year_id')) {
                    $table->foreignId('recommended_academic_year_id')->nullable()->after('notes')
                        ->constrained('academic_years')->nullOnDelete();
                }
            });

            if (Schema::hasColumn('free_trial_bookings', 'recommended_school_year_id') && $idMap !== []) {
                foreach ($idMap as $oldId => $newId) {
                    DB::table('free_trial_bookings')->where('recommended_school_year_id', $oldId)->update([
                        'recommended_academic_year_id' => $newId,
                    ]);
                }
            }

            if (Schema::hasColumn('free_trial_bookings', 'recommended_school_year_id')) {
                Schema::table('free_trial_bookings', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('recommended_school_year_id');
                });
            }
        }

        Schema::dropIfExists('school_years');
    }

    public function down(): void
    {
        if (! Schema::hasTable('school_years')) {
            Schema::create('school_years', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('tagline')->nullable();
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('level_number');
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
                $table->index(['is_active', 'sort_order']);
                $table->unique('level_number');
            });
        }

        // Best-effort restore from academic years that look like school levels
        if (Schema::hasTable('academic_years')) {
            $rows = DB::table('academic_years')
                ->whereNotNull('level_number')
                ->orderBy('order')
                ->get();

            $map = [];
            foreach ($rows as $ay) {
                $id = DB::table('school_years')->insertGetId([
                    'name' => $ay->name,
                    'slug' => $ay->slug ?: ('year-'.$ay->id),
                    'tagline' => $ay->tagline,
                    'description' => $ay->description,
                    'level_number' => $ay->level_number,
                    'image_path' => $ay->thumbnail,
                    'is_active' => $ay->is_active,
                    'sort_order' => $ay->order ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $map[(int) $ay->id] = (int) $id;
            }

            if (Schema::hasTable('tutoring_groups') && Schema::hasColumn('tutoring_groups', 'academic_year_id')) {
                Schema::table('tutoring_groups', function (Blueprint $table) {
                    if (! Schema::hasColumn('tutoring_groups', 'school_year_id')) {
                        $table->foreignId('school_year_id')->nullable()->after('learning_path')
                            ->constrained('school_years')->nullOnDelete();
                    }
                });
                foreach ($map as $ayId => $syId) {
                    DB::table('tutoring_groups')->where('academic_year_id', $ayId)->update([
                        'school_year_id' => $syId,
                    ]);
                }
                Schema::table('tutoring_groups', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('academic_year_id');
                });
            }

            if (Schema::hasTable('free_trial_bookings') && Schema::hasColumn('free_trial_bookings', 'recommended_academic_year_id')) {
                Schema::table('free_trial_bookings', function (Blueprint $table) {
                    if (! Schema::hasColumn('free_trial_bookings', 'recommended_school_year_id')) {
                        $table->foreignId('recommended_school_year_id')->nullable()->after('notes')
                            ->constrained('school_years')->nullOnDelete();
                    }
                });
                foreach ($map as $ayId => $syId) {
                    DB::table('free_trial_bookings')->where('recommended_academic_year_id', $ayId)->update([
                        'recommended_school_year_id' => $syId,
                    ]);
                }
                Schema::table('free_trial_bookings', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('recommended_academic_year_id');
                });
            }
        }

        if (Schema::hasTable('academic_years')) {
            Schema::table('academic_years', function (Blueprint $table) {
                if (Schema::hasColumn('academic_years', 'level_number')) {
                    $table->dropUnique(['level_number']);
                    $table->dropColumn('level_number');
                }
                if (Schema::hasColumn('academic_years', 'tagline')) {
                    $table->dropColumn('tagline');
                }
                if (Schema::hasColumn('academic_years', 'slug')) {
                    $table->dropUnique(['slug']);
                    $table->dropColumn('slug');
                }
            });
        }
    }
};
