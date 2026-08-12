<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * تسعير الكورسات المسجّلة من المعلم: EGP للداخل و USD للخارج.
 */
class CoursePricingController extends Controller
{
    public function edit(AdvancedCourse $course): View
    {
        $this->assertOwns($course);

        return view('instructor.courses.pricing', compact('course'));
    }

    public function update(Request $request, AdvancedCourse $course): RedirectResponse
    {
        $this->assertOwns($course);

        $data = $request->validate([
            'price_egp' => ['nullable', 'numeric', 'min:0'],
            'price_egp_after_discount' => ['nullable', 'numeric', 'min:0'],
            'price_usd' => ['nullable', 'numeric', 'min:0'],
            'price_usd_after_discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $payload = [
            'price_egp' => $data['price_egp'] ?? null,
            'price_egp_after_discount' => $data['price_egp_after_discount'] ?? null,
            'price_usd' => $data['price_usd'] ?? null,
            'price_usd_after_discount' => $data['price_usd_after_discount'] ?? null,
        ];

        // مزامنة الحقل القديم price من EGP للتوافق مع الشاشات القديمة
        if (isset($payload['price_egp']) && $payload['price_egp'] !== null) {
            $payload['price'] = $payload['price_egp'];
            $payload['price_after_discount'] = $payload['price_egp_after_discount'];
        }

        if (Schema::hasColumn('advanced_courses', 'price_egp')) {
            $course->update($payload);
        } else {
            $course->update([
                'price' => $payload['price'] ?? $course->price,
                'price_after_discount' => $payload['price_after_discount'] ?? $course->price_after_discount,
            ]);
        }

        return back()->with('success', 'تم تحديث أسعار الكورس (جنيه / دولار).');
    }

    private function assertOwns(AdvancedCourse $course): void
    {
        $user = request()->user();
        $ids = $user->teachingAdvancedCourseIds();
        abort_unless($ids->contains((int) $course->id), 403);
    }
}
