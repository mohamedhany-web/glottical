<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Models\SiteTestimonial;
use App\Support\PlatformFaqDefaults;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Home page is handled by welcome.blade.php route

    public function about()
    {
        $stats = [
            'courses' => \App\Models\AdvancedCourse::where('is_active', true)->count(),
            'students' => \App\Models\User::where('role', 'student')->where('is_active', true)->count(),
            'instructors' => \App\Models\User::where('role', 'instructor')->where('is_active', true)->count(),
        ];
        
        return view('public.about', compact('stats'));
    }

    public function faq()
    {
        $faqs = FAQ::active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        $categories = FAQ::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        // عند وجود أسئلة في لوحة التحكم نعرضها فقط؛ الأسئلة الافتراضية تظهر عند عدم وجود أي سجل نشط
        $defaultFaqs = FAQ::active()->doesntExist()
            ? PlatformFaqDefaults::items()
            : [];

        return view('public.faq', compact('faqs', 'categories', 'defaultFaqs'));
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function pricing()
    {
        $packages = \App\Models\Package::active()
            ->with(['courses' => function ($query) {
                $query->where('is_active', true);
            }])
            ->withCount('courses')
            ->orderBy('is_popular', 'desc')
            ->orderBy('is_featured', 'desc')
            ->orderBy('order')
            ->orderBy('price', 'asc')
            ->get();

        $tutoringGroups = \App\Models\TutoringGroup::query()
            ->active()
            ->with(['instructor:id,name'])
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('public.pricing', compact('packages', 'tutoringGroups'));
    }

    public function team()
    {
        return view('public.team');
    }

    public function certificates()
    {
        return view('public.certificates');
    }

    public function help()
    {
        return view('public.help');
    }

    public function refund()
    {
        return view('public.refund');
    }

    public function testimonials()
    {
        $testimonials = SiteTestimonial::query()
            ->active()
            ->ordered()
            ->get();

        return view('public.testimonials', compact('testimonials'));
    }

    public function events()
    {
        return view('public.events');
    }

    public function partners()
    {
        return view('public.partners');
    }
}
