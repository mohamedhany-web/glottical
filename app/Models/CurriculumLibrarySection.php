<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class CurriculumLibrarySection extends Model
{
    protected $fillable = [
        'curriculum_library_item_id',
        'parent_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(CurriculumLibraryItem::class, 'curriculum_library_item_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CurriculumLibrarySection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CurriculumLibrarySection::class, 'parent_id')->orderBy('order')->orderBy('id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CurriculumLibraryMaterial::class, 'curriculum_library_section_id')
            ->orderBy('order')
            ->orderBy('id');
    }

    /**
     * شجرة أقسام جذرية للمنهج (مع treeChildren و materials لكل عقدة).
     *
     * @param  bool  $onlyActive  للطالب true؛ للأدمن يمكن false لإظهار المعطّل أيضاً
     */
    public static function treeForItem(CurriculumLibraryItem $item, bool $onlyActive = true): Collection
    {
        $materialRelations = [];
        if (Schema::hasTable('curriculum_presentation_derivatives')) {
            $materialRelations[] = 'presentationDerivative';
        }

        $query = static::query()
            ->where('curriculum_library_item_id', $item->id)
            ->with([
                'materials' => function ($q) use ($onlyActive, $materialRelations) {
                    $q->orderBy('order')->orderBy('id');
                    if ($materialRelations !== []) {
                        $q->with($materialRelations);
                    }
                    if ($onlyActive) {
                        $q->where('is_active', true);
                    }
                },
            ])
            ->orderBy('order')
            ->orderBy('id');

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        $all = $query->get();

        $childrenOf = function ($parentId) use (&$childrenOf, $all) {
            return $all
                ->filter(function (CurriculumLibrarySection $s) use ($parentId) {
                    if ($parentId === null) {
                        return $s->parent_id === null;
                    }

                    return (int) $s->parent_id === (int) $parentId;
                })
                ->values()
                ->map(function (CurriculumLibrarySection $section) use (&$childrenOf) {
                    $section->setRelation('treeChildren', $childrenOf($section->id));

                    return $section;
                });
        };

        return $childrenOf(null);
    }

    /** حذف القسم وجميع الأبناء والمواد مع الملفات في التخزين */
    public function deleteWithStorage(): void
    {
        foreach ($this->children()->get() as $child) {
            $child->deleteWithStorage();
        }
        foreach ($this->materials()->get() as $material) {
            // Uses model helper so presentation derivatives are removed with the material.
            $material->deleteWithStorage();
        }
        $this->delete();
    }
}
