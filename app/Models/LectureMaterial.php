<?php

namespace App\Models;

use App\Services\LectureMaterialStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureMaterial extends Model
{
    protected $fillable = [
        'lecture_id',
        'library_folder_id',
        'file_name',
        'file_path',
        'storage_disk',
        'title',
        'is_visible_to_student',
        'sort_order',
    ];

    protected $casts = [
        'is_visible_to_student' => 'boolean',
    ];

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(LibraryFolder::class, 'library_folder_id');
    }

    public function resolvedDisk(): string
    {
        return LectureMaterialStorage::diskFor($this);
    }

    public function downloadUrl(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        if (\Illuminate\Support\Facades\Route::has('student.library.materials.download')) {
            return route('student.library.materials.download', $this);
        }

        return LectureMaterialStorage::publicUrl($this);
    }
}
