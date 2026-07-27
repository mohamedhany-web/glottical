<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesLead;
use Illuminate\Http\Request;

class SalesLeadController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesLead::query()
            ->with(['assignedTo:id,name', 'creator:id,name', 'interestedCourse:id,title']);

        if ($request->filled('status')) {
            $s = (string) $request->input('status');
            if (array_key_exists($s, SalesLead::statusLabels())) {
                $query->where('status', $s);
            }
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            if ($search !== '') {
                $like = '%'.$search.'%';
                $query->where(function ($q) use ($like, $search) {
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('phone', 'like', $like);
                    if (ctype_digit($search)) {
                        $q->orWhere('id', (int) $search);
                    }
                });
            }
        }

        $leads = $query->latest()->paginate(25)->withQueryString();

        return view('admin.sales.leads.index', compact('leads'));
    }

    public function show(SalesLead $salesLead)
    {
        $salesLead->load([
            'assignedTo:id,name,email',
            'creator:id,name',
            'linkedUser:id,name,email',
            'convertedOrder:id,user_id,amount,status,advanced_course_id',
            'convertedOrder.course:id,title',
            'convertedOrder.user:id,name,email',
            'interestedCourse:id,title',
        ]);

        return view('admin.sales.leads.show', compact('salesLead'));
    }
}
