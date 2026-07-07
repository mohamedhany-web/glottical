@extends('layouts.admin')

@section('title', 'صلاحيات '.$employee->name)
@section('header', 'صلاحيات موظف المبيعات')

@section('content')
<div class="space-y-4 max-w-4xl">
  @include('partials.crm-admin-nav')

  <div class="rounded-xl border bg-white p-4 text-sm">
    <p><strong>{{ $employee->name }}</strong> — {{ $employee->employeeJob?->name }}</p>
    <p class="text-gray-500">{{ $employee->email }}</p>
  </div>

  <form method="POST" action="{{ route('admin.crm.sales-permissions.update', $employee) }}" class="rounded-2xl border bg-white p-6 space-y-6">
    @csrf
    @method('PUT')

    <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-violet-200 bg-violet-50 cursor-pointer">
      <input type="checkbox" name="employee_permissions_custom" value="1"
             @checked(old('employee_permissions_custom', $employee->usesCustomEmployeePermissions()))
             class="mt-1 w-4 h-4 text-violet-600 rounded">
      <span>
        <span class="font-bold text-violet-900 block">صلاحيات مخصصة لهذا الموظف</span>
        <span class="text-sm text-violet-800">عند التفعيل تُستخدم القائمة أدناه فقط (وليس صلاحيات الوظيفة العامة). أزل أي خانة ليختفي القسم من نظام الموظف بالكامل.</span>
      </span>
    </label>

    @foreach($groups as $groupTitle => $items)
      <div>
        <h3 class="font-bold text-gray-900 mb-2">{{ $groupTitle }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 border rounded-xl p-4">
          @foreach($items as $key => $label)
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="employee_permissions[]" value="{{ $key }}"
                     @checked(in_array($key, $selected, true))
                     class="w-4 h-4 text-indigo-600 rounded border-gray-300">
              <span>{{ $label }}</span>
            </label>
          @endforeach
        </div>
      </div>
    @endforeach

    <div class="flex gap-3 pt-4 border-t">
      <a href="{{ route('admin.crm.sales-permissions.index') }}" class="px-5 py-2.5 rounded-lg bg-gray-500 text-white font-bold text-sm">إلغاء</a>
      <button class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-bold text-sm">حفظ الصلاحيات</button>
    </div>
  </form>
</div>
@endsection
