<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CurriculumLibraryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
        'is_restricted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_restricted' => 'boolean',
        'order' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(CurriculumLibraryItem::class, 'category_id')->orderBy('order')->orderBy('title');
    }

    public function activeItems()
    {
        return $this->hasMany(CurriculumLibraryItem::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('title');
    }

    /** مستخدمون يُسمح لهم بمشاهدة «قسم خاص» */
    public function restrictedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'curriculum_library_category_user', 'category_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * تصنيفات يحق للطالب رؤيتها في القوائم والفلترة (عامة أو مُدرَجة لهم).
     */
    public function scopeAccessibleByStudent($query, ?User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('curriculum_library_categories.is_restricted', false);
            if ($user) {
                $q->orWhereExists(function ($sub) use ($user) {
                    $sub->selectRaw('1')
                        ->from('curriculum_library_category_user')
                        ->whereColumn('curriculum_library_category_user.category_id', 'curriculum_library_categories.id')
                        ->where('curriculum_library_category_user.user_id', $user->id);
                });
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }
}
