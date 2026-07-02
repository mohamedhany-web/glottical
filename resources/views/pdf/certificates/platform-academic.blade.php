@php
    $academy = $academyName ?? config('app.name');
    $primary = $primaryColor ?? '#283593';
    $secondary = $secondaryColor ?? '#FB5607';
    $cream = $creamBg ?? '#FDFBF7';
    $accent = $accentLight ?? '#FFE5F7';
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: {{ $cream }};
            color: #1a1a2e;
        }
        /* mPDF: linear/repeating gradients on small or nested boxes can yield bbox height 0 → Division by zero in Gradient.php */
        .page-wrap {
            padding: 8mm 14mm 10mm;
            border: 3px double {{ $primary }};
            outline: 1px solid rgba(40,53,147,.22);
            outline-offset: 4px;
            min-height: 178mm;
            position: relative;
            background: {{ $cream }};
        }
        .pattern {
            position: absolute;
            inset: 0;
            pointer-events: none;
            /* بدون تدرجات: mPDF قد يقسم على ارتفاع 0 في Gradient.php */
        }
        .inner { position: relative; z-index: 1; }
        .logo-row { text-align: center; margin-bottom: 5mm; }
        .logo-img { height: 42px; width: auto; display: inline-block; vertical-align: middle; }
        .brand-text {
            display: inline-block;
            vertical-align: middle;
            margin-inline-start: 10px;
            text-align: right;
        }
        .brand-name {
            font-size: 15pt;
            font-weight: bold;
            color: {{ $primary }};
            letter-spacing: .5px;
        }
        .brand-sub { font-size: 8pt; color: #5c6178; margin-top: 1px; }
        h1 {
            text-align: center;
            font-size: 28pt;
            font-weight: bold;
            color: {{ $primary }};
            margin: 2mm 0 4mm;
            letter-spacing: 1px;
        }
        .line-deco {
            width: 55mm;
            height: 2pt;
            min-height: 2pt;
            margin: 0 auto 5mm;
            background: {{ $secondary }};
            opacity: 0.95;
        }
        .witness {
            text-align: center;
            font-size: 12pt;
            color: #3d4260;
            margin-bottom: 6mm;
        }
        .witness strong { color: {{ $primary }}; font-weight: bold; }
        .student-name {
            text-align: center;
            font-size: 26pt;
            font-weight: bold;
            color: {{ $primary }};
            margin: 4mm 0 7mm;
            padding: 4mm 8mm;
            border-bottom: 2px solid {{ $accent }};
            display: inline-block;
            width: 88%;
            margin-left: auto;
            margin-right: auto;
        }
        .course-block {
            text-align: center;
            font-size: 13pt;
            line-height: 1.75;
            color: #2d3142;
            margin-bottom: 8mm;
        }
        .course-name {
            font-size: 16pt;
            font-weight: bold;
            color: {{ $secondary }};
            margin-top: 2mm;
        }
        .badge-row { text-align: center; margin: 6mm 0; }
        .badge {
            display: inline-block;
            border: 2px solid {{ $primary }};
            color: {{ $primary }};
            font-size: 10pt;
            font-weight: bold;
            padding: 3mm 8mm;
            border-radius: 2mm;
            background: {{ $accent }};
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5mm 0 10mm;
            font-size: 9pt;
            color: #4a4f66;
        }
        .meta-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 2mm 3mm;
            border-inline-start: 1px solid rgba(40,53,147,.12);
        }
        .meta-table td:first-child { border-inline-start: none; }
        .meta-label { font-size: 8pt; color: #6b7089; margin-bottom: 1mm; }
        .meta-value { font-family: 'DejaVu Sans Mono', monospace; font-size: 9pt; color: {{ $primary }}; font-weight: bold; }
        .sign-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4mm;
        }
        .sign-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding-top: 6mm;
        }
        .sign-line {
            width: 48mm;
            border-top: 1px solid {{ $primary }};
            margin: 0 auto 2mm;
        }
        .sign-name { font-size: 10pt; font-weight: bold; color: {{ $primary }}; }
        .sign-title { font-size: 8pt; color: #6b7089; margin-top: 1mm; }
        .footer-note {
            text-align: center;
            font-size: 7pt;
            color: #8b90a8;
            margin-top: 5mm;
        }
    </style>
</head>
<body>
<div class="page-wrap">
    <div class="pattern"></div>
    @if(!empty($previewWatermark))
        <div style="position:absolute; inset:0; z-index:50; pointer-events:none; display:flex; align-items:center; justify-content:center;">
            <span style="font-size:52pt; color:rgba(40,53,147,0.09); transform:rotate(-24deg); font-weight:bold; letter-spacing:2px;">معاينة</span>
        </div>
    @endif
    <div class="inner">
        <div class="logo-row">
            @if(!empty($logoDataUri))
                <img class="logo-img" src="{{ $logoDataUri }}" alt="">
            @endif
            <div class="brand-text">
                <div class="brand-name">{{ $academy }}</div>
                <div class="brand-sub">أكاديمية تعليمية رقمية</div>
            </div>
        </div>

        <h1>شهادة إتمام دورة</h1>
        <div class="line-deco"></div>

        <p class="witness">تشهد <strong>{{ $academy }}</strong> بأن</p>

        <div style="text-align:center;">
            <div class="student-name">{{ $studentName }}</div>
        </div>

        <div class="course-block">
            قد أتم بنجاح دورة:<br>
            <span class="course-name">{{ $courseDisplayName }}</span>
        </div>

        <div class="badge-row">
            <span class="badge">مُعتمد — إتمام برنامج تدريبي</span>
        </div>

        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">تاريخ الإتمام</div>
                    <div class="meta-value">{{ $issueDateFormatted }}</div>
                </td>
                <td>
                    <div class="meta-label">رقم الشهادة</div>
                    <div class="meta-value">{{ $certificateNumber }}</div>
                </td>
                <td>
                    <div class="meta-label">رمز التحقق</div>
                    <div class="meta-value" style="font-size:8pt;">{{ $verificationCode }}</div>
                </td>
            </tr>
        </table>

        <table class="sign-table">
            <tr>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-name">{{ $directorName }}</div>
                    <div class="sign-title">{{ $directorTitle }}</div>
                </td>
                <td>
                    <div class="sign-line"></div>
                    <div class="sign-name">{{ $instructorName }}</div>
                    <div class="sign-title">{{ $instructorTitle }}</div>
                </td>
            </tr>
        </table>

        @if(!empty($verificationUrl))
            <p class="footer-note">للتحقق من صحة الشهادة: {{ $verificationUrl }}</p>
        @endif
    </div>
</div>
</body>
</html>
