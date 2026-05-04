{{--
  مزايا الاشتراك: النص من أوصاف المدير (teacher_features) إن وُجدت، وإلا ترجمة الميزة.
  $featureKeysOrder: قائمة مفاتيح المزايا بالترتيب
  $featureDisplayLines: [key => نص يظهر بجانب الصندوق]
  $checkedKeys: مفاتيح يجب أن تكون مفعّلة (مصفوفة نصوص)
--}}
@php
    $checkedKeys = array_values(array_unique(array_map('strval', $checkedKeys ?? [])));
@endphp
<div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
    @foreach($featureKeysOrder as $fk)
        <label class="inline-flex items-start gap-2">
            <input type="checkbox" name="features[{{ $fk }}]" value="1" data-sub-feature="{{ $fk }}"
                   class="mt-0.5 ml-2 rounded border-gray-300 text-sky-600 focus:ring-sky-500 shrink-0"
                   {{ in_array($fk, $checkedKeys, true) ? 'checked' : '' }}>
            <span class="min-w-0">
                <span class="block font-medium text-gray-900 leading-snug">{{ $featureDisplayLines[$fk] ?? $fk }}</span>
            </span>
        </label>
    @endforeach
</div>
