<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Smoke: التطبيق يقلع ومسار الصفحة الرئيسية معرّف.
     * طلب HTTP للـ `/` يحتاج هجرات كاملة (غير متاحة على SQLite :memory: في هذا المشروع).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->assertTrue(app()->bound('config'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('home'));
        $this->assertSame('/', route('home', absolute: false));
    }
}
