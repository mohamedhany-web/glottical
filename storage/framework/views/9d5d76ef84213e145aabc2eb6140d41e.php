
<div class="mt-4 space-y-4 border-t border-gray-200 pt-4">
    <div>
        <h3 class="text-sm font-bold text-gray-900">حدود الاستهلاك (Muallimx Classroom والتسويق)</h3>
        <p class="text-xs text-gray-500 mt-1">
            تُحفظ مع الاشتراك وتُستخدم بدل قيم الباقة الافتراضية من الإعدادات. عند اختيار باقة من القائمة تُملأ هذه الحقول تلقائياً ويمكنك تعديلها قبل الحفظ.
        </p>
    </div>

    <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
        <p class="text-xs font-bold text-gray-800 mb-2">قيود Classroom</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">عدد الجلسات المسموح شهرياً</label>
                <input type="number" min="0" max="10000" step="1" name="limits[classroom_meetings_per_month]"
                       x-model.number="limits.classroom_meetings_per_month"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">الحد الأقصى للحضور في الجلسة</label>
                <input type="number" min="1" max="1000" step="1" name="limits[classroom_max_participants]"
                       x-model.number="limits.classroom_max_participants"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">المدة الافتراضية للجلسة (دقائق)</label>
                <input type="number" min="15" max="1440" step="1" name="limits[classroom_default_duration_minutes]"
                       x-model.number="limits.classroom_default_duration_minutes"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">الحد الأقصى لمدة الجلسة (دقائق)</label>
                <input type="number" min="30" max="1440" step="1" name="limits[classroom_max_duration_minutes]"
                       x-model.number="limits.classroom_max_duration_minutes"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
        </div>
    </div>

    <div class="border border-gray-200 rounded-xl p-3 bg-gray-50">
        <p class="text-xs font-bold text-gray-800 mb-2">التسويق الشخصي للمعلم</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">أقسام الملف التسويقي المفعّلة</label>
                <input type="number" min="1" max="20" step="1" name="limits[personal_marketing_profile_sections]"
                       x-model.number="limits.personal_marketing_profile_sections"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">درجة أولوية الظهور (0–100)</label>
                <input type="number" min="0" max="100" step="1" name="limits[personal_marketing_priority_score]"
                       x-model.number="limits.personal_marketing_priority_score"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-[11px] font-semibold text-gray-600 mb-1">أيام إبراز الملف شهرياً (0–31)</label>
                <input type="number" min="0" max="31" step="1" name="limits[personal_marketing_monthly_featured_days]"
                       x-model.number="limits.personal_marketing_monthly_featured_days"
                       class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\subscriptions\_subscription-limit-fields.blade.php ENDPATH**/ ?>