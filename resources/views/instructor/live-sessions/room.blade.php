<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $liveSession->title }} — بث مباشر</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; }
        body { margin: 0; padding: 0; background: #0c1222; overflow: hidden; height: 100vh; }
        #mx-live-broadcast-root { width: 100%; flex: 1; min-height: 0; background: #0f172a; position: relative; }
        .room-body { position: relative; display: flex; flex-direction: column; height: calc(100vh - 72px); }
        #mx-live-broadcast-root iframe { width: 100% !important; height: 100% !important; border: none; }

        /* Recording pulse */
        @keyframes recPulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
        #record-icon.recording { animation: recPulse 1s infinite; }

        /* Recording status toast */
        #mx-rec-toast {
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(220,38,38,0.92);
            color: white;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(220,38,38,0.4);
            backdrop-filter: blur(6px);
        }
        #mx-rec-toast.is-visible { display: flex; }
        #mx-rec-dot { width:8px;height:8px;background:#fff;border-radius:50%;animation:recPulse 1s infinite; }
    </style>
</head>
<body class="bg-slate-950">
    {{-- شريط Glottical العلوي --}}
    <header class="h-[72px] bg-gradient-to-l from-slate-900 to-slate-800 border-b border-slate-700/50 flex items-center justify-between px-4 sm:px-6 shadow-lg">
        <div class="flex items-center gap-4">
            <a href="{{ route('instructor.live-sessions.index') }}" class="flex items-center gap-2 text-slate-300 hover:text-white transition-colors">
                <span class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center">
                    <i class="fas fa-broadcast-tower text-lg"></i>
                </span>
                <span class="font-bold text-white hidden sm:inline">Glottical</span>
            </a>
            <span class="w-px h-6 bg-slate-600 hidden sm:block"></span>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-red-600 rounded-full animate-pulse shadow-lg shadow-red-500/50"></span>
                <span class="text-white font-semibold text-sm">{{ $liveSession->title }}</span>
                <span class="text-slate-400 text-xs px-2 py-0.5 rounded-md bg-slate-700/80 font-mono hidden sm:inline">{{ $liveSession->room_name }}</span>
            </div>
            <span class="text-slate-400 text-xs font-mono hidden md:inline" id="timer">00:00:00</span>
        </div>
        <div class="flex items-center gap-2 flex-wrap justify-end">
            @if(!empty($subscriptionFeatureMenuItems))
            <div class="relative shrink-0" id="pkg-features-dd-wrap">
                <button type="button" id="pkg-features-dd-btn" class="inline-flex items-center gap-2 px-2.5 sm:px-3 py-2 rounded-xl bg-slate-700/80 hover:bg-slate-600/90 text-slate-100 text-sm font-medium transition-colors border border-slate-600 hover:border-cyan-500/35 max-w-[11rem] sm:max-w-none" aria-expanded="false" aria-haspopup="true" title="مزايا اشتراكك — تفتح في تاب جديد">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-cyan-500/15 text-cyan-400 border border-cyan-500/20">
                        <i class="fas fa-layer-group text-sm"></i>
                    </span>
                    <span class="flex min-w-0 flex-1 flex-col items-stretch text-right leading-tight">
                        <span class="truncate font-semibold text-slate-100">مزايا الباقة</span>
                        @if(!empty($subscriptionPackageLabel))
                        <span class="truncate text-[10px] font-normal text-slate-400">{{ $subscriptionPackageLabel }}</span>
                        @else
                        <span class="text-[10px] font-normal text-slate-500">اشتراكك النشط</span>
                        @endif
                    </span>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400 shrink-0 transition-transform duration-200" id="pkg-features-dd-chevron" aria-hidden="true"></i>
                </button>
                <div id="pkg-features-dd-panel" class="pkg-features-dd-panel-inner hidden absolute top-[calc(100%+0.5rem)] end-0 w-[min(100vw-2rem,19.5rem)] rounded-xl border border-slate-600 bg-slate-900/98 backdrop-blur-md overflow-hidden" role="menu">
                    <div class="px-3 py-2.5 border-b border-slate-700/90 bg-slate-800/70 flex items-start gap-2">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-cyan-500/10 text-cyan-400">
                            <i class="fas fa-arrow-up-left-from-square text-[10px]"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-200 m-0 leading-snug">روابط سريعة</p>
                            <p class="text-[11px] text-slate-500 m-0 mt-0.5 leading-relaxed">كل رابط يُفتح في نافذة جديدة دون إغلاق البث.</p>
                        </div>
                    </div>
                    <div class="max-h-[min(58vh,20rem)] overflow-y-auto py-1.5 px-1">
                        @foreach($subscriptionFeatureMenuItems as $item)
                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" role="menuitem" class="group flex items-center gap-3 px-2.5 py-2 mx-0.5 rounded-lg text-slate-200 hover:bg-slate-700/70 transition-colors border border-transparent hover:border-slate-600/80">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $item['icon_bg'] }} {{ $item['icon_text'] }} ring-1 ring-white/5 group-hover:ring-cyan-500/15 transition-[box-shadow]">
                                <i class="fas {{ $item['icon'] }} text-sm"></i>
                            </span>
                            <span class="min-w-0 flex-1 text-sm font-medium leading-snug text-right group-hover:text-white">{{ $item['label'] }}</span>
                            <i class="fas fa-arrow-up-left-from-square text-slate-500 group-hover:text-cyan-400/90 text-[11px] shrink-0 transition-colors"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            {{-- نفس سبورة Glottical Classroom (Excalidraw) --}}
            <button type="button" id="btn-wb-popup-open"
                    class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-amber-600/25 hover:bg-amber-600/35 text-amber-100 text-sm font-semibold transition-colors border border-amber-500/40"
                    title="فتح السبورة التفاعلية (Glottical Classroom)">
                <i class="fas fa-chalkboard text-amber-300"></i>
                <span class="hidden sm:inline">السبورة التفاعلية</span>
            </button>
            <label class="inline-flex items-center gap-2 px-2.5 py-2 rounded-xl bg-slate-700/50 border border-slate-600 cursor-pointer select-none text-slate-200 text-xs sm:text-sm shrink-0"
                   title="الطلاب يرسمون قلم/ممحاة فوق البث؛ يظهر عندك فوق نفس منطقة العرض">
                <input type="checkbox" id="mx-toggle-student-wb" class="rounded border-slate-500 text-amber-500 focus:ring-amber-500 shrink-0"
                       {{ $liveSession->allowsStudentWhiteboard() ? 'checked' : '' }}>
                <span class="font-medium whitespace-nowrap"><span class="hidden sm:inline">رسم الطلاب فوق البث</span><span class="sm:hidden">رسم</span></span>
            </label>

            {{-- زر إنشاء تقرير الذكاء الاصطناعي (n8n) --}}
            <form method="POST" action="{{ route('instructor.live-sessions.ai-report', $liveSession) }}" class="inline" onsubmit="return confirm('سيتم إرسال تسجيل الجلسة (إن وجد) إلى نظام التقارير الذكي لإنشاء تقرير. هل أنت متأكد؟');">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-emerald-600/70 hover:bg-emerald-600 text-emerald-50 text-xs sm:text-sm font-semibold transition-colors border border-emerald-400/60 shadow-sm shadow-emerald-500/20">
                    <i class="fas fa-robot"></i>
                    <span class="hidden sm:inline">تقرير ذكي للجلسة</span>
                    <span class="sm:hidden">تقرير AI</span>
                </button>
            </form>

            {{-- زر التسجيل --}}
            <button type="button" id="btn-record"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700/80 hover:bg-slate-600 text-slate-200 text-sm font-medium transition-colors border border-slate-600"
                title="تسجيل المحاضرة (تسجيل محلي بدون مشاركة شاشة)">
                <i class="fas fa-circle-dot text-rose-400" id="record-icon"></i>
                <span id="record-label">تسجيل</span>
            </button>

            {{-- إنهاء البث --}}
            <form method="POST" action="{{ route('instructor.live-sessions.end', $liveSession) }}" class="inline" id="end-session-form" onsubmit="return handleEndSession(event);">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold transition-colors shadow-lg shadow-rose-500/20">
                    <i class="fas fa-stop"></i> إنهاء البث
                </button>
            </form>
        </div>
    </header>

    {{-- Recording Toast --}}
    <div id="mx-rec-toast">
        <span id="mx-rec-dot"></span>
        <span id="mx-rec-label">جارٍ التسجيل...</span>
    </div>

    <div class="room-body">
        <div id="mx-video-stack" class="relative flex-1 min-h-0 flex flex-col">
            <main id="mx-live-broadcast-root" class="flex-1 min-h-0 relative" role="application" aria-label="غرفة البث — Glottical"></main>
            @include('partials.mx-share-annotation-overlay', [
                'mxAnnRole' => 'viewer_poll',
                'mxAnnPollUrl' => route('instructor.live-sessions.share-annotations', $liveSession),
            ])
        </div>
    </div>

    @include('partials.mx-muallimx-excalidraw-popup')
    @include('partials.jitsi-iframe-media-allow')
    <script src="https://{{ $jitsiDomain }}/external_api.js"></script>
    <script>
        /* ══════════════════════════════════════════════
           غرفة البث (Glottical)
        ══════════════════════════════════════════════ */
        const domain   = '{{ $jitsiDomain }}';
        const jitsiRoot = document.querySelector('#mx-live-broadcast-root');
        if (typeof muallimxEnsureJitsiIframeMediaAllow === 'function') {
            muallimxEnsureJitsiIframeMediaAllow(jitsiRoot);
        }
        const options = {
            roomName: '{{ $liveSession->room_name }}',
            parentNode: jitsiRoot,
            width: '100%',
            height: '100%',
            userInfo: {
                displayName: '{{ $user->name }} (مدرب)',
                email: '{{ $user->email }}'
            },
            configOverwrite: {
                prejoinConfig: { enabled: false },
                prejoinPageEnabled: false,
                enableLobby: false,
                requireDisplayName: false,
                enableWelcomePage: false,
                disableDeepLinking: true,
                startWithAudioMuted: true,
                startWithVideoMuted: true,
                enableNoisyMicDetection: false,
                @if(!$liveSession->allow_chat)
                disableChat: true,
                @endif
            },
            interfaceConfigOverwrite: {
                APP_NAME: 'Glottical',
                NATIVE_APP_NAME: 'Glottical',
                PROVIDER_NAME: 'Glottical',
                JITSI_WATERMARK_LINK: '',
                HIDE_DEEP_LINKING_LOGO: true,
                TOOLBAR_BUTTONS: [
                    'microphone', 'camera', 'desktop', 'chat',
                    'raisehand', 'participants-pane', 'tileview',
                    'fullscreen', 'hangup', 'settings',
                    'select-background',
                ],
                SHOW_JITSI_WATERMARK: false,
                SHOW_WATERMARK_FOR_GUESTS: false,
                SHOW_BRAND_WATERMARK: false,
                SHOW_POWERED_BY: false,
                MOBILE_APP_PROMO: false,
                DEFAULT_BACKGROUND: '#0f172a',
                DISABLE_JOIN_LEAVE_NOTIFICATIONS: false,
                FILM_STRIP_MAX_HEIGHT: 120,
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);

        /* ══════════════════════════════════════════════
           TIMER
        ══════════════════════════════════════════════ */
        const startTime = new Date('{{ $liveSession->started_at->toISOString() }}');
        function updateTimer() {
            const diff = Math.floor((Date.now() - startTime) / 1000);
            const h = Math.floor(diff / 3600), m = Math.floor((diff % 3600) / 60), s = diff % 60;
            var el = document.getElementById('timer');
            if (el) el.textContent = String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0');
        }
        setInterval(updateTimer, 1000);
        updateTimer();

        /* ══════════════════════════════════════════════
           LOCAL SCREEN + MIC RECORDING
           (لا يحتاج مشاركة شاشة في Jitsi)
        ══════════════════════════════════════════════ */
        let recordStream   = null;
        let recordRecorder = null;
        let recordChunks   = [];
        let recordingActive = false;

        const recBtn   = document.getElementById('btn-record');
        const recIcon  = document.getElementById('record-icon');
        const recLabel = document.getElementById('record-label');
        const recToast = document.getElementById('mx-rec-toast');
        const recToastLabel = document.getElementById('mx-rec-label');

        function setRecordingUI(active) {
            recordingActive = active;
            if (active) {
                recBtn.classList.replace('bg-slate-700/80', 'bg-rose-600/90');
                recBtn.classList.add('text-white');
                recIcon.className = 'fas fa-stop recording';
                recLabel.textContent = 'إيقاف التسجيل';
                recToast.classList.add('is-visible');
                recToastLabel.textContent = 'جارٍ التسجيل...';
            } else {
                recBtn.classList.replace('bg-rose-600/90', 'bg-slate-700/80');
                recBtn.classList.remove('text-white');
                recIcon.className = 'fas fa-circle-dot text-rose-400';
                recLabel.textContent = 'تسجيل';
                recToast.classList.remove('is-visible');
            }
        }

        function downloadRecording() {
            if (!recordChunks.length) return;
            const mimeType = (recordRecorder && recordRecorder.mimeType) || 'video/webm';
            const blob = new Blob(recordChunks, { type: mimeType });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement('a');
            a.href     = url;
            a.download = 'glottical-rec-' + Date.now() + '.webm';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 8000);
            recordChunks = [];
            recToastLabel.textContent = 'تم حفظ التسجيل ✓';
            setTimeout(() => recToast.classList.remove('is-visible'), 3000);
        }

        async function startLocalRecording() {
            try {
                // تسجيل الشاشة - يختار المستخدم ما يريد تسجيله من نافذة المتصفح
                const dispStream = await navigator.mediaDevices.getDisplayMedia({
                    video: { frameRate: { ideal: 15, max: 30 }, cursor: 'always' },
                    audio: true
                });

                // تسجيل الميكروفون
                let micStream = null;
                try { micStream = await navigator.mediaDevices.getUserMedia({ audio: true }); } catch(e) {}

                const tracks = [...dispStream.getTracks()];
                if (micStream) tracks.push(...micStream.getAudioTracks());
                recordStream = new MediaStream(tracks);

                const mimeType = ['video/webm;codecs=vp9,opus','video/webm;codecs=vp8,opus','video/webm']
                    .find(m => MediaRecorder.isTypeSupported(m)) || '';

                recordRecorder = mimeType ? new MediaRecorder(recordStream, { mimeType }) : new MediaRecorder(recordStream);
                recordChunks = [];

                recordRecorder.ondataavailable = e => { if (e.data && e.data.size > 0) recordChunks.push(e.data); };
                recordRecorder.onstop = () => downloadRecording();

                // لو المستخدم أوقف مشاركة الشاشة من المتصفح → إيقاف التسجيل تلقائياً
                dispStream.getVideoTracks()[0].addEventListener('ended', () => {
                    if (recordingActive) stopLocalRecording();
                });

                recordRecorder.start(1000);
                setRecordingUI(true);
                return true;
            } catch (err) {
                console.warn('Recording failed:', err);
                alert('لم يتم بدء التسجيل. تأكد من السماح بمشاركة الشاشة من المتصفح.');
                return false;
            }
        }

        function stopLocalRecording() {
            if (!recordingActive && (!recordRecorder || recordRecorder.state === 'inactive')) return;
            setRecordingUI(false);
            if (recordRecorder && recordRecorder.state !== 'inactive') {
                recordRecorder.stop();
            }
            if (recordStream) {
                recordStream.getTracks().forEach(t => t.stop());
                recordStream = null;
            }
        }

        recBtn && recBtn.addEventListener('click', async function() {
            if (recordingActive) {
                stopLocalRecording();
            } else {
                await startLocalRecording();
            }
        });

        /* ══════════════════════════════════════════════
           AUTO AUDIO RECORDING (في الخلفية دائماً)
        ══════════════════════════════════════════════ */
        const csrfToken       = '{{ csrf_token() }}';
        const studentWbUrl    = '{{ route("instructor.live-sessions.student-whiteboard", $liveSession) }}';
        const mxStudentWbToggle = document.getElementById('mx-toggle-student-wb');
        let mxStudentWbSaving = false;
        if (mxStudentWbToggle) {
            mxStudentWbToggle.addEventListener('change', async function () {
                if (mxStudentWbSaving) return;
                mxStudentWbSaving = true;
                const want = mxStudentWbToggle.checked;
                try {
                    const r = await fetch(studentWbUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ allow: want }),
                    });
                    if (!r.ok) mxStudentWbToggle.checked = !want;
                } catch (e) {
                    mxStudentWbToggle.checked = !want;
                } finally {
                    mxStudentWbSaving = false;
                }
            });
        }
        const audioPresignUrl = '{{ route("instructor.live-sessions.audio.presign", $liveSession) }}';
        const audioCompleteUrl= '{{ route("instructor.live-sessions.audio.complete", $liveSession) }}';
        let audioRecorder = null, audioStream = null, audioChunks = [];
        let audioStartedAt = null, audioUploadFinalized = false, audioUploadInFlight = false;

        function pickAudioMimeType() {
            if (!window.MediaRecorder || typeof MediaRecorder.isTypeSupported !== 'function') return '';
            return ['audio/webm;codecs=opus','audio/webm','audio/ogg;codecs=opus','audio/ogg']
                .find(m => MediaRecorder.isTypeSupported(m)) || '';
        }

        async function startAutoAudioRecording() {
            if (audioRecorder || !navigator.mediaDevices?.getUserMedia || !window.MediaRecorder) return;
            try {
                audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                const mimeType = pickAudioMimeType();
                audioRecorder = mimeType ? new MediaRecorder(audioStream, { mimeType }) : new MediaRecorder(audioStream);
                audioChunks = []; audioStartedAt = Date.now();
                audioRecorder.ondataavailable = e => { if (e.data?.size > 0) audioChunks.push(e.data); };
                audioRecorder.start(1000);
            } catch (e) { console.warn('Auto audio recording failed:', e); }
        }

        async function uploadAudioBlob(blob, durationSeconds) {
            if (!blob || blob.size <= 0) return;
            const presignRes = await fetch(audioPresignUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ content_type: blob.type || 'audio/webm' }),
            });
            if (!presignRes.ok) return;
            const presign = await presignRes.json();
            if (!presign.direct_upload || !presign.upload_url) return;
            const uploadHeaders = Object.assign({}, presign.headers || {});
            if (!uploadHeaders['Content-Type'] && !uploadHeaders['content-type']) {
                uploadHeaders['Content-Type'] = presign.content_type || blob.type || 'audio/webm';
            }
            const putRes = await fetch(presign.upload_url, { method: 'PUT', headers: uploadHeaders, body: blob });
            if (!putRes.ok) return;
            await fetch(audioCompleteUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ upload_token: presign.upload_token, duration_seconds: Math.max(1, Math.floor(durationSeconds || 0)) }),
            });
        }

        async function stopAndUploadAutoAudio() {
            if (audioUploadFinalized || audioUploadInFlight) return;
            if (!audioRecorder) return;
            audioUploadInFlight = true;
            try {
                if (audioRecorder.state !== 'inactive') {
                    await new Promise(resolve => { audioRecorder.addEventListener('stop', resolve, { once: true }); audioRecorder.stop(); });
                }
                const mimeType = audioRecorder.mimeType || 'audio/webm';
                const blob = new Blob(audioChunks, { type: mimeType });
                const duration = audioStartedAt ? ((Date.now() - audioStartedAt) / 1000) : 0;
                await uploadAudioBlob(blob, duration);
                audioUploadFinalized = true;
            } catch (e) { console.warn('Auto audio upload failed:', e); }
            finally {
                audioStream?.getTracks().forEach(t => t.stop());
                audioStream = null; audioRecorder = null; audioChunks = []; audioUploadInFlight = false;
            }
        }

        /* ══════════════════════════════════════════════
           JITSI EVENTS
        ══════════════════════════════════════════════ */
        api.addEventListener('videoConferenceJoined', function() {
            startAutoAudioRecording();
        });

        // لو انقطع الاتصال أو أُنهيت الجلسة من واجهة البث
        api.addEventListener('videoConferenceLeft', function() {
            stopLocalRecording();
            stopAndUploadAutoAudio();
            try {
                var payload = new FormData();
                payload.append('_token', '{{ csrf_token() }}');
                navigator.sendBeacon('{{ route("instructor.live-sessions.leave-presence", $liveSession) }}', payload);
            } catch (e) {}
        });

        window.addEventListener('pagehide', function() {
            try {
                var payload = new FormData();
                payload.append('_token', '{{ csrf_token() }}');
                navigator.sendBeacon('{{ route("instructor.live-sessions.leave-presence", $liveSession) }}', payload);
            } catch (e) {}
        });

        /* ══════════════════════════════════════════════
           إنهاء البث - حفظ التسجيل أولاً
        ══════════════════════════════════════════════ */
        async function handleEndSession(e) {
            if (!confirm('هل تريد إنهاء البث المباشر؟')) return false;
            e.preventDefault();
            const form = document.getElementById('end-session-form');
            const btn  = form.querySelector('button[type="submit"]');
            if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جارٍ الإنهاء...'; }

            // أوقف التسجيل المحلي وحمّله قبل الإنهاء
            if (recordingActive) stopLocalRecording();

            await stopAndUploadAutoAudio();
            form.submit();
            return false;
        }

        /* حفظ تلقائي عند إغلاق الصفحة أو انقطاع الإنترنت */
        window.addEventListener('beforeunload', function() {
            stopLocalRecording();
            if (!audioUploadFinalized && audioRecorder) {
                stopAndUploadAutoAudio();
            }
        });

        /* ══════════════════════════════════════════════
           KEYBOARD SHORTCUTS
        ══════════════════════════════════════════════ */
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'R') {
                e.preventDefault();
                recBtn && recBtn.click();
            }
        });
    </script>
    @if(!empty($subscriptionFeatureMenuItems))
    <script>
        (function () {
            var wrap = document.getElementById('pkg-features-dd-wrap');
            var btn = document.getElementById('pkg-features-dd-btn');
            var panel = document.getElementById('pkg-features-dd-panel');
            var chev = document.getElementById('pkg-features-dd-chevron');
            if (!wrap || !btn || !panel) return;
            function setOpen(open) {
                panel.classList.toggle('hidden', !open);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (chev) chev.style.transform = open ? 'rotate(180deg)' : '';
            }
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                setOpen(panel.classList.contains('hidden'));
            });
            wrap.addEventListener('click', function (e) { e.stopPropagation(); });
            document.addEventListener('click', function () { setOpen(false); });
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') setOpen(false);
            });
        })();
    </script>
    @endif
</body>
</html>
