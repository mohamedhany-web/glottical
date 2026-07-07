@extends('layouts.admin')

@section('title', 'صلاحيات قسم المبيعات')
@section('header', 'صلاحيات موظفي المبيعات')

@section('content')
<div class="space-y-4">
  @include('partials.crm-admin-nav')

  @if(session('success'))
    <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
  @endif

  <div class="rounded-2xl bg-white border p-5">
    <h1 class="text-xl font-black text-gray-900">تحكم فردي بصلاحيات قسم المبيعات</h1>
    <p class="text-sm text-gray-600 mt-1">أضف أو أزل أي صلاحية لكل موظف — ما تُلغيه يختفي من السايدبار وصفحات CRM فوراً.</p>
  </div>

  <form method="GET" class="flex flex-wrap gap-2 items-end bg-white border rounded-xl p-4">
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">بحث</label>
      <input name="search" value="{{ request('search') }}" class="rounded-lg border px-3 py-2 text-sm" placeholder="اسم / بريد / رمز">
    </div>
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">الوظيفة</label>
      <select name="job_code" class="rounded-lg border px-3 py-2 text-sm">
        <option value="">الكل</option>
        @foreach($jobs as $job)
          <option value="{{ $job->code }}" @selected(request('job_code')===$job->code)>{{ $job->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-xs font-bold text-gray-600 mb-1">نوع الصلاحيات</label>
      <select name="custom" class="rounded-lg border px-3 py-2 text-sm">
        <option value="">الكل</option>
        <option value="1" @selected(request('custom')==='1')>مخصصة للموظف</option>
        <option value="0" @selected(request('custom')==='0')>من الوظيفة فقط</option>
      </select>
    </div>
    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-bold">تصفية</button>
  </form>

  <div class="rounded-2xl border bg-white overflow-hidden">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-xs uppercase">
        <tr>
          <th class="px-4 py-3 text-right">الموظف</th>
          <th class="px-4 py-3 text-right">الوظيفة</th>
          <th class="px-4 py-3 text-right">الصلاحيات</th>
          <th class="px-4 py-3 text-right"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($employees as $employee)
          <tr>
            <td class="px-4 py-3">
              <div class="font-semibold">{{ $employee->name }}</div>
              <div class="text-xs text-gray-500">{{ $employee->email }}</div>
            </td>
            <td class="px-4 py-3">{{ $employee->employeeJob?->name ?? '—' }}</td>
            <td class="px-4 py-3">
              @if($employee->usesCustomEmployeePermissions())
                <span class="text-xs font-bold bg-violet-100 text-violet-800 px-2 py-1 rounded">مخصصة</span>
                <span class="text-gray-600 text-xs mr-1">{{ count($employee->effectiveEmployeePermissions()) }} صلاحية</span>
              @else
                <span class="text-xs font-bold bg-gray-100 text-gray-700 px-2 py-1 rounded">من الوظيفة</span>
              @endif
            </td>
            <td class="px-4 py-3">
              <a href="{{ route('admin.crm.sales-permissions.edit', $employee) }}" class="text-indigo-600 font-bold text-xs">تعديل الصلاحيات</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-4 py-10 text-center text-gray-500">لا يوجد موظفون في قسم المبيعات</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">{{ $employees->links() }}</div>
  </div>
</div>
@endsection
