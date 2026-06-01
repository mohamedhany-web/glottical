<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CurriculumLibraryItemFile extends Model
{
    protected $fillable = [
        'curriculum_library_item_id',
        'path',
        'storage_disk',
        'label',
        'file_type',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(CurriculumLibraryItem::class, 'curriculum_library_item_id');
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->path) return null;
        $disk = $this->storage_disk ?: config('filesystems.default');

        return \App\Services\PublicStorageUrl::fromPath($this->path, is_string($disk) ? $disk : null);
    }

    public function scopePresentations($query)
    {
        return $query->where('file_type', 'presentation');
    }

    public function scopeAssignments($query)
    {
        return $query->where('file_type', 'assignment');
    }
}
