<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع التسجيل — <?php echo e($meeting->title ?: $meeting->code); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex flex-col items-center justify-center p-4">
<?php
    $backUrl = ($rp === 'instructor.')
        ? route('instructor.classroom.show', $meeting)
        : route('student.classroom.show', $meeting);
?>
    <div class="w-full max-w-md rounded-2xl border border-slate-600 bg-slate-900 shadow-2xl p-5 sm:p-6">
        <h1 class="text-lg font-bold text-white m-0 mb-1 flex items-center gap-2">
            <i class="fas fa-cloud-arrow-up text-cyan-400"></i>
            رفع وحفظ التسجيل
        </h1>
        <p class="text-xs text-slate-500 m-0 mb-4">هذا التاب منفصل عن غرفة الاجتماع — يمكنك إغلاقه بعد اكتمال الرفع.</p>
        <div class="h-2.5 rounded-full bg-slate-700 overflow-hidden mb-2">
            <div id="mx-tab-bar" class="h-full w-0 bg-cyan-500 transition-[width] duration-150"></div>
        </div>
        <p id="mx-tab-status" class="text-sm text-slate-300 mb-4 min-h-[3rem] whitespace-pre-wrap m-0 leading-relaxed"></p>
        <div class="flex flex-wrap gap-2">
            <button type="button" id="mx-tab-retry" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-600/90 hover:bg-amber-600 text-white text-sm font-medium border border-amber-500/40">إعادة المحاولة</button>
            <a href="<?php echo e($backUrl); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-100 text-sm font-medium border border-slate-600">صفحة الاجتماع</a>
            <button type="button" id="mx-tab-close" class="hidden inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-100 text-sm font-medium border border-slate-600">إغلاق التاب</button>
        </div>
    </div>

    <script>
        (function() {
            var jobId = <?php echo json_encode($jobId); ?>;
            var mxMeetingId = <?php echo e((int) $meeting->id); ?>;
            var csrfToken = '<?php echo e(csrf_token()); ?>';
            var uploadRecordingUrl = '<?php echo e(route($rp . 'classroom.recording.upload', $meeting)); ?>';
            var presignRecordingUrl = '<?php echo e(route($rp . 'classroom.recording.presign', $meeting)); ?>';
            var completeRecordingUrl = '<?php echo e(route($rp . 'classroom.recording.complete', $meeting)); ?>';
            var presignAudioUrl = '<?php echo e(route($rp . 'classroom.recording-audio.presign', $meeting)); ?>';
            var uploadAudioUrl = '<?php echo e(route($rp . 'classroom.recording-audio.upload', $meeting)); ?>';
            var completeAudioUrl = '<?php echo e(route($rp . 'classroom.recording-audio.complete', $meeting)); ?>';

            var elBar = document.getElementById('mx-tab-bar');
            var elStatus = document.getElementById('mx-tab-status');
            var btnRetry = document.getElementById('mx-tab-retry');
            var btnClose = document.getElementById('mx-tab-close');

            function setBar(pct) {
                var p = pct == null ? 0 : Math.max(0, Math.min(100, Number(pct)));
                if (elBar) elBar.style.width = p + '%';
            }
            function setStatus(t) {
                if (elStatus) elStatus.textContent = t || '';
            }

            var mxUploadDbPromise = null;
            function mxOpenUploadDb() {
                if (mxUploadDbPromise) return mxUploadDbPromise;
                mxUploadDbPromise = new Promise(function(resolve, reject) {
                    var req = indexedDB.open('mxClassroomRecordings', 1);
                    req.onerror = function() { reject(req.error); };
                    req.onsuccess = function() { resolve(req.result); };
                    req.onupgradeneeded = function(e) {
                        var db = e.target.result;
                        if (!db.objectStoreNames.contains('pendingUploads')) {
                            db.createObjectStore('pendingUploads', { keyPath: 'id' });
                        }
                    };
                });
                return mxUploadDbPromise;
            }
            function mxIdbGetJob(id) {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var tx = db.transaction('pendingUploads', 'readonly');
                        var rq = tx.objectStore('pendingUploads').get(id);
                        rq.onerror = function() { reject(rq.error); };
                        rq.onsuccess = function() { resolve(rq.result); };
                    });
                });
            }
            function mxIdbPutJob(job) {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var tx = db.transaction('pendingUploads', 'readwrite');
                        tx.oncomplete = function() { resolve(); };
                        tx.onerror = function() { reject(tx.error); };
                        tx.objectStore('pendingUploads').put(job);
                    });
                });
            }
            function mxIdbDeleteJob(id) {
                return mxOpenUploadDb().then(function(db) {
                    return new Promise(function(resolve, reject) {
                        var tx = db.transaction('pendingUploads', 'readwrite');
                        tx.oncomplete = function() { resolve(); };
                        tx.onerror = function() { reject(tx.error); };
                        tx.objectStore('pendingUploads').delete(id);
                    });
                });
            }

            function formatBytes(n) {
                var x = Number(n) || 0;
                if (x < 1024) return x + ' B';
                if (x < 1048576) return (x / 1024).toFixed(1) + ' KB';
                if (x < 1073741824) return (x / 1048576).toFixed(1) + ' MB';
                return (x / 1073741824).toFixed(2) + ' GB';
            }

            function putBlobToPresignedUrl(url, blob, contentType, extraHeaders, onPercent) {
                return new Promise(function(resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('PUT', url, true);
                    xhr.timeout = 0;
                    if (contentType) xhr.setRequestHeader('Content-Type', contentType);
                    if (extraHeaders && typeof extraHeaders === 'object') {
                        Object.keys(extraHeaders).forEach(function(k) {
                            try { xhr.setRequestHeader(k, extraHeaders[k]); } catch (hErr) {}
                        });
                    }
                    xhr.upload.onprogress = function(e) {
                        if (typeof onPercent !== 'function') return;
                        if (e.lengthComputable && e.total > 0) {
                            onPercent(Math.min(99, Math.round((e.loaded / e.total) * 100)));
                        }
                    };
                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 300) resolve();
                        else reject(new Error('فشل رفع التسجيل (HTTP ' + xhr.status + ').'));
                    };
                    xhr.onerror = function() { reject(new Error('انقطع الاتصال أثناء الرفع.')); };
                    xhr.ontimeout = function() { reject(new Error('انتهت مهلة الرفع.')); };
                    xhr.send(blob);
                });
            }

            function onProgUi(o) {
                o = o || {};
                if (o.percent != null && !isNaN(o.percent)) setBar(o.percent);
                if (o.text) setStatus(o.text);
            }

            function uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress) {
                return new Promise(function(resolve, reject) {
                    var xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadRecordingUrl, true);
                    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.timeout = 0;
                    xhr.upload.onprogress = function(e) {
                        if (typeof onProgress === 'function' && e.lengthComputable && e.total > 0) {
                            var p = Math.min(100, Math.round((e.loaded / e.total) * 100));
                            onProgress({ text: 'جاري الرفع عبر الخادم ' + p + '%...', percent: p });
                        }
                    };
                    xhr.onerror = function() { reject(new Error('فشل الاتصال أثناء الرفع.')); };
                    xhr.ontimeout = function() { reject(new Error('انتهت مهلة الرفع.')); };
                    xhr.onload = function() {
                        var raw = xhr.responseText || '';
                        var data = {};
                        try { data = raw ? JSON.parse(raw) : {}; } catch (e) {}
                        if (xhr.status >= 200 && xhr.status < 300) {
                            resolve({ ok: true, data: data });
                            return;
                        }
                        reject(new Error((data && data.message) ? data.message : 'فشل رفع التسجيل.'));
                    };
                    var formData = new FormData();
                    formData.append('recording', blob, 'meeting-recording.webm');
                    formData.append('duration_seconds', String(durationSeconds || 0));
                    xhr.send(formData);
                });
            }

            async function uploadRecordedBlob(blob, durationSeconds, onProgress) {
                var putSucceeded = false;
                var ct = blob.type || 'audio/webm';
                try {
                    if (typeof onProgress === 'function') {
                        onProgress({ text: 'جاري تجهيز رابط الرفع...', percent: 2 });
                    }
                    var presignRes = await fetch(presignRecordingUrl, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ content_type: ct }),
                    });
                    var presignData = {};
                    try { presignData = await presignRes.json(); } catch (je) { presignData = {}; }

                    if (presignRes.ok && presignData.direct_upload === false) {
                        return uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress);
                    }

                    if (presignRes.ok && presignData.upload_url && presignData.upload_token && presignData.content_type) {
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'جاري رفع التسجيل (' + formatBytes(blob.size) + ')...', percent: 5 });
                        }
                        await putBlobToPresignedUrl(
                            presignData.upload_url,
                            blob,
                            presignData.content_type,
                            presignData.headers || {},
                            function(p) {
                                if (typeof onProgress === 'function') {
                                    onProgress({ text: 'جاري رفع التسجيل...', percent: 5 + Math.round((p / 100) * 80) });
                                }
                            }
                        );
                        putSucceeded = true;
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'جاري تأكيد الملف على الخادم...', percent: 90 });
                        }
                        var completeRes = await fetch(completeRecordingUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                upload_token: presignData.upload_token,
                                duration_seconds: durationSeconds || 0,
                            }),
                        });
                        var completeData = {};
                        try { completeData = await completeRes.json(); } catch (je2) { completeData = {}; }
                        if (!completeRes.ok) {
                            throw new Error((completeData && completeData.message) ? completeData.message : 'فشل ربط الملف.');
                        }
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'تم رفع الملف الرئيسي.', percent: 100 });
                        }
                        return { ok: true, data: completeData };
                    }
                } catch (err) {
                    if (putSucceeded) throw err;
                    console.warn('Direct upload presign skipped/failed:', err);
                }
                return uploadRecordedBlobViaFormData(blob, durationSeconds, onProgress);
            }

            async function uploadAudioBlob(blob, durationSeconds, onProgress) {
                function uploadAudioBlobViaFormData() {
                    return new Promise(function(resolve, reject) {
                        var formData = new FormData();
                        formData.append('recording_audio', blob, 'meeting-audio.webm');
                        formData.append('duration_seconds', String(durationSeconds || 0));
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', uploadAudioUrl, true);
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr.upload.onprogress = function(e) {
                            if (typeof onProgress === 'function' && e.lengthComputable && e.total > 0) {
                                var p = Math.min(100, Math.round((e.loaded / e.total) * 100));
                                onProgress({ text: 'جاري رفع الصوت عبر الخادم ' + p + '%...', percent: p });
                            }
                        };
                        xhr.onload = function() {
                            var data = {};
                            try { data = xhr.responseText ? JSON.parse(xhr.responseText) : {}; } catch (e) {}
                            if (xhr.status >= 200 && xhr.status < 300) {
                                resolve({ ok: true, data: data });
                                return;
                            }
                            reject(new Error((data && data.message) ? data.message : 'فشل رفع الصوت.'));
                        };
                        xhr.onerror = function() { reject(new Error('فشل الاتصال.')); };
                        xhr.send(formData);
                    });
                }

                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري تجهيز رابط رفع الصوت...', percent: 2 });
                }
                var presignRes = await fetch(presignAudioUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ content_type: blob.type || 'audio/webm' }),
                });
                var presignData = {};
                try { presignData = await presignRes.json(); } catch (je) { presignData = {}; }

                if (presignRes.ok && presignData.direct_upload === false) {
                    return uploadAudioBlobViaFormData();
                }
                if (!presignRes.ok || !presignData.upload_url || !presignData.upload_token || !presignData.content_type) {
                    return uploadAudioBlobViaFormData();
                }
                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري رفع ملف الصوت...', percent: 5 });
                }
                await putBlobToPresignedUrl(
                    presignData.upload_url,
                    blob,
                    presignData.content_type,
                    presignData.headers || {},
                    function(p) {
                        if (typeof onProgress === 'function') {
                            onProgress({ text: 'جاري رفع ملف الصوت...', percent: 5 + Math.round((p / 100) * 80) });
                        }
                    }
                );
                if (typeof onProgress === 'function') {
                    onProgress({ text: 'جاري تأكيد ملف الصوت...', percent: 90 });
                }
                var completeRes = await fetch(completeAudioUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        upload_token: presignData.upload_token,
                        duration_seconds: durationSeconds || 0,
                    }),
                });
                var completeData = {};
                try { completeData = await completeRes.json(); } catch (je2) { completeData = {}; }
                if (!completeRes.ok) {
                    throw new Error((completeData && completeData.message) ? completeData.message : 'فشل حفظ الصوت.');
                }
                if (typeof onProgress === 'function') {
                    onProgress({ text: 'تم رفع الصوت.', percent: 100 });
                }
                return { ok: true, data: completeData };
            }

            var currentJob = null;

            async function runJob(job) {
                if (!job || !job.blob || !job.id) {
                    setStatus('بيانات الرفع غير صالحة.');
                    return;
                }
                if (Number(job.meetingId) !== Number(mxMeetingId)) {
                    setStatus('هذا الرفع لا يخص هذا الاجتماع.');
                    return;
                }
                currentJob = job;
                if (btnRetry) btnRetry.classList.add('hidden');
                setBar(0);
                setStatus('جاري بدء رفع وحفظ التسجيل...');

                var persisted = Object.assign({}, job, { status: 'uploading', updatedAt: Date.now() });
                try {
                    await mxIdbPutJob(persisted);
                } catch (e) {}

                var onProg = onProgUi;

                try {
                    if (job.kind === 'report') {
                        await uploadAudioBlob(job.blob, job.durationSeconds, onProg);
                    } else {
                        await uploadRecordedBlob(job.blob, job.durationSeconds, onProg);
                        if (job.secondaryBlob && job.secondaryBlob.size > 0) {
                            onProg({ text: 'جاري رفع ملف الصوت المصاحب للفيديو...', percent: 92 });
                            await uploadAudioBlob(job.secondaryBlob, job.durationSeconds, onProg);
                        }
                    }
                    await mxIdbDeleteJob(job.id);
                    setBar(100);
                    setStatus('اكتمل الرفع بنجاح. يمكنك إغلاق هذا التاب والعودة للاجتماع.');
                    if (btnClose) btnClose.classList.remove('hidden');
                } catch (err) {
                    console.error(err);
                    var msg = (err && err.message) ? err.message : 'فشل الرفع.';
                    persisted.status = 'failed';
                    persisted.lastError = msg;
                    persisted.updatedAt = Date.now();
                    try {
                        await mxIdbPutJob(persisted);
                    } catch (e2) {}
                    currentJob = persisted;
                    setStatus(msg + '\n\nيمكنك الضغط على «إعادة المحاولة» بعد عودة الإنترنت.');
                    if (btnRetry) btnRetry.classList.remove('hidden');
                }
            }

            function start() {
                if (!jobId) {
                    setStatus('لم يُمرَّر مُعرّف المهمة. أغلق هذا التاب وابدأ الرفع من غرفة الاجتماع مرة أخرى.');
                    return;
                }
                setStatus('جاري قراءة الملف من التخزين المحلي...');
                mxIdbGetJob(jobId).then(function(job) {
                    if (!job || !job.blob) {
                        setStatus('لم يُعثر على الملف في هذا المتصفح (ربما مسحت البيانات أو استخدمت متصفحاً آخر). أعد التسجيل من الغرفة.');
                        return;
                    }
                    runJob(job);
                }).catch(function() {
                    setStatus('تعذر قراءة التخزين المحلي.');
                });
            }

            if (btnRetry) {
                btnRetry.addEventListener('click', function() {
                    if (currentJob && currentJob.blob) {
                        runJob(Object.assign({}, currentJob, { status: 'pending' }));
                    } else {
                        start();
                    }
                });
            }
            if (btnClose) {
                btnClose.addEventListener('click', function() {
                    window.close();
                });
            }

            window.addEventListener('online', function() {
                if (currentJob && currentJob.status === 'failed' && btnRetry && !btnRetry.classList.contains('hidden')) {
                    setStatus('عاد الاتصال — يمكنك الضغط على «إعادة المحاولة».');
                }
            });

            start();
        })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Muallimx\resources\views\student\classroom\recording-upload-tab.blade.php ENDPATH**/ ?>