<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\PublicLearnCatalogService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CoursesController extends Controller
{
    public function index(Request $request, PublicLearnCatalogService $catalog): View
    {
        $tab = (string) $request->query('tab', '');
        if (! in_array($tab, ['private', 'groups'], true)) {
            $delivery = (string) $request->query('delivery', 'one_to_one');
            $tab = $delivery === 'group' ? 'groups' : 'private';
        }

        $payload = $catalog->catalog($tab, [
            'q' => $request->query('q'),
            'subject_id' => $request->query('subject_id'),
            'year_id' => $request->query('year_id', $request->query('path')),
            'type' => $request->query('type'),
            'subject' => $request->query('subject'),
            'age' => $request->query('age'),
            'gender' => $request->query('gender'),
            'language' => $request->query('language'),
            'specialty' => $request->query('specialty'),
            'availability' => $request->query('availability'),
        ]);

        return view('courses', $payload);
    }
}
