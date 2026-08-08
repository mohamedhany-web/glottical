<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HiringForm;
use App\Models\HiringFormField;
use App\Services\HiringFormService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HiringFormController extends Controller
{
    public function edit(): View
    {
        $form = HiringFormService::ensureDefaultForm()->load('fields');

        return view('admin.hiring-form.edit', [
            'form' => $form,
            'typeLabels' => HiringFormField::typeLabels(),
            'systemKeys' => HiringFormField::systemKeys(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $form = HiringFormService::ensureDefaultForm();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
            'require_intro_video' => ['nullable', 'boolean'],
        ]);

        $form->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'is_published' => $request->boolean('is_published'),
            'settings' => array_merge($form->settings ?? [], [
                'require_intro_video' => $request->boolean('require_intro_video'),
            ]),
        ]);

        return back()->with('success', 'تم حفظ إعدادات النموذج.');
    }

    public function storeField(Request $request): RedirectResponse
    {
        $form = HiringFormService::ensureDefaultForm();

        $data = $this->validateField($request);
        $maxSort = (int) $form->fields()->max('sort_order');

        HiringFormField::create([
            'hiring_form_id' => $form->id,
            'type' => $data['type'],
            'label' => $data['label'],
            'help_text' => $data['help_text'] ?? null,
            'placeholder' => $data['placeholder'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'options' => $this->parseOptions($data['options_text'] ?? null, $data['type']),
            'system_key' => $data['system_key'] ?: null,
            'sort_order' => $maxSort + 10,
            'is_active' => true,
            'settings' => [
                'file_kind' => $data['file_kind'] ?? 'any',
            ],
        ]);

        return back()->with('success', 'تمت إضافة الخانة.');
    }

    public function updateField(Request $request, HiringFormField $field): RedirectResponse
    {
        $data = $this->validateField($request, true);

        $field->update([
            'type' => $data['type'],
            'label' => $data['label'],
            'help_text' => $data['help_text'] ?? null,
            'placeholder' => $data['placeholder'] ?? null,
            'is_required' => $request->boolean('is_required'),
            'options' => $this->parseOptions($data['options_text'] ?? null, $data['type']),
            'system_key' => $data['system_key'] ?: null,
            'is_active' => $request->boolean('is_active'),
            'settings' => array_merge($field->settings ?? [], [
                'file_kind' => $data['file_kind'] ?? ($field->settings['file_kind'] ?? 'any'),
            ]),
        ]);

        return back()->with('success', 'تم تحديث الخانة.');
    }

    public function destroyField(HiringFormField $field): RedirectResponse
    {
        $field->delete();

        return back()->with('success', 'تم حذف الخانة.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:hiring_form_fields,id'],
        ]);

        $form = HiringFormService::ensureDefaultForm();
        foreach (array_values($data['order']) as $i => $id) {
            HiringFormField::query()
                ->where('hiring_form_id', $form->id)
                ->where('id', $id)
                ->update(['sort_order' => ($i + 1) * 10]);
        }

        return back()->with('success', 'تم تحديث ترتيب الخانات.');
    }

    public function move(HiringFormField $field, string $direction): RedirectResponse
    {
        $formId = $field->hiring_form_id;
        $siblings = HiringFormField::query()
            ->where('hiring_form_id', $formId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $index = $siblings->search(fn ($f) => $f->id === $field->id);
        if ($index === false) {
            return back();
        }

        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;
        if ($swapWith < 0 || $swapWith >= $siblings->count()) {
            return back();
        }

        $other = $siblings[$swapWith];
        $tmp = $field->sort_order;
        $field->update(['sort_order' => $other->sort_order]);
        $other->update(['sort_order' => $tmp]);

        return back()->with('success', 'تم تغيير الترتيب.');
    }

    private function validateField(Request $request, bool $updating = false): array
    {
        $types = array_keys(HiringFormField::typeLabels());
        $keys = array_keys(HiringFormField::systemKeys());

        return $request->validate([
            'type' => ['required', 'in:'.implode(',', $types)],
            'label' => ['required', 'string', 'max:190'],
            'help_text' => ['nullable', 'string', 'max:1000'],
            'placeholder' => ['nullable', 'string', 'max:190'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'system_key' => ['nullable', 'in:'.implode(',', $keys)],
            'options_text' => ['nullable', 'string', 'max:5000'],
            'file_kind' => ['nullable', 'in:any,image,pdf,video,image_pdf'],
        ]);
    }

    private function parseOptions(?string $text, string $type): ?array
    {
        if (! in_array($type, [
            HiringFormField::TYPE_SELECT,
            HiringFormField::TYPE_RADIO,
            HiringFormField::TYPE_CHECKBOX,
        ], true)) {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $text) ?: [];
        $options = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (str_contains($line, '|')) {
                [$value, $label] = array_map('trim', explode('|', $line, 2));
                $options[] = ['value' => $value !== '' ? $value : $label, 'label' => $label !== '' ? $label : $value];
            } else {
                $options[] = ['value' => $line, 'label' => $line];
            }
        }

        return $options;
    }
}
