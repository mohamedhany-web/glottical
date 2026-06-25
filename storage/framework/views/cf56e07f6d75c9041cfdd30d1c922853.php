<?php $__env->startSection('title', 'هيكل المنهج: ' . $item->title); ?>
<?php $__env->startSection('header', 'هيكل المنهج'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $countSections = $flatSections->count();
    $countMaterials = $flatSections->sum('materials_count');
?>
<div class="w-full max-w-none font-body space-y-6">
    <?php if($errors->any()): ?>
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800">
            <p class="font-black text-sm mb-2">تعذر تنفيذ العملية، راجع الأخطاء:</p>
            <ul class="list-disc pr-5 text-xs space-y-1">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e(is_array($err) ? implode('، ', $err) : $err); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-gradient-to-l from-indigo-50/90 via-white to-slate-50/90 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800/95 shadow-sm">
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-l from-indigo-500 via-violet-500 to-cyan-500 opacity-90"></div>
        <div class="p-5 sm:p-6 lg:p-8">
            <nav class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-4">
                <a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-semibold transition-colors">مكتبة المناهج</a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <a href="<?php echo e(route('admin.curriculum-library.items.edit', $item)); ?>" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-medium truncate max-w-[10rem] sm:max-w-none transition-colors"><?php echo e($item->title); ?></a>
                <i class="fas fa-chevron-left text-[10px] opacity-50"></i>
                <span class="text-slate-800 dark:text-slate-100 font-bold">هيكل المنهج</span>
            </nav>
            <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-6">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white/80 dark:bg-slate-800/80 border border-slate-200/80 dark:border-slate-600 text-[11px] font-mono text-slate-600 dark:text-slate-300">
                            <i class="fas fa-link text-indigo-500 text-[10px]"></i> <?php echo e($item->slug); ?>

                        </span>
                        <?php if($item->category): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-[11px] font-semibold text-slate-700 dark:text-slate-200"><?php echo e($item->category->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-heading font-black text-slate-900 dark:text-white tracking-tight break-words"><?php echo e($item->title); ?></h1>
                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-400 mt-2 max-w-4xl leading-relaxed">
                        نظّم الأقسام والفروع، ثم ارفع المواد لكل قسم واضبط لكل مادة: عرض داخل المنصة أو تحميل (حسب نوع الملف).
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row xl:flex-col gap-3 shrink-0">
                    <div class="flex gap-3">
                        <a href="<?php echo e(route('admin.curriculum-library.items.edit', $item)); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 text-sm font-bold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                            <i class="fas fa-pen text-indigo-500 text-xs"></i> تعديل بيانات المنهج
                        </a>
                        <a href="<?php echo e(route('admin.curriculum-library.index')); ?>" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-700 text-white text-sm font-bold hover:bg-slate-900 dark:hover:bg-slate-600 transition-colors">
                            <i class="fas fa-list text-xs opacity-80"></i> كل المناهج
                        </a>
                    </div>
                    <div class="flex gap-3 sm:justify-end xl:justify-stretch">
                        <div class="flex-1 sm:flex-none min-w-[7rem] rounded-xl bg-white/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-600 px-4 py-3 text-center shadow-sm">
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">أقسام</p>
                            <p class="text-2xl font-black text-indigo-600 dark:text-indigo-400 font-heading tabular-nums"><?php echo e($countSections); ?></p>
                        </div>
                        <div class="flex-1 sm:flex-none min-w-[7rem] rounded-xl bg-white/90 dark:bg-slate-800/90 border border-slate-200/80 dark:border-slate-600 px-4 py-3 text-center shadow-sm">
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">مواد</p>
                            <p class="text-2xl font-black text-violet-600 dark:text-violet-400 font-heading tabular-nums"><?php echo e($countMaterials); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 shadow-md overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-700 bg-gradient-to-l from-indigo-50/50 to-transparent dark:from-indigo-950/30 dark:to-transparent flex items-center gap-3">
            <span class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/25"><i class="fas fa-plus text-sm"></i></span>
            <div>
                <h2 class="text-base font-heading font-black text-slate-900 dark:text-white">إضافة قسم جذر</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">مثال: المستوى المبتدئ، الدراسات الإسلامية، القرآن والتجويد…</p>
            </div>
        </div>
        <div class="p-5 sm:p-6">
            <form action="<?php echo e(route('admin.curriculum-library.items.sections.store', $item)); ?>" method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <?php echo csrf_field(); ?>
                <div class="md:col-span-7 lg:col-span-8">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">عنوان القسم</label>
                    <input type="text" name="title" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                           placeholder="مثال: المستوى الأول — لغة عربية">
                </div>
                <div class="md:col-span-3 lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5">ترتيب العرض</label>
                    <input type="number" name="order" value="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="md:col-span-2 lg:col-span-2">
                    <button type="submit" class="w-full px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-black shadow-lg shadow-indigo-500/20 transition-colors">
                        إضافة القسم
                    </button>
                </div>
            </form>
        </div>
    </div>

    
    <div class="space-y-5">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h2 class="text-lg font-heading font-black text-slate-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-sitemap text-indigo-500"></i> هيكل الأقسام والمواد
            </h2>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $tree; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $root): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php echo $__env->make('admin.curriculum-library._structure-section', ['section' => $root, 'item' => $item, 'depth' => 0, 'materialDirectUpload' => $materialDirectUpload ?? false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-900/40 px-8 py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 flex items-center justify-center mx-auto mb-4 text-slate-400">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <p class="text-slate-700 dark:text-slate-300 font-bold text-base mb-1">لا توجد أقسام بعد</p>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">استخدم النموذج أعلاه لإضافة أول قسم جذر، ثم افتح كل قسم لإضافة فروع أو رفع المواد.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php if(!empty($materialDirectUpload)): ?>
<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    function jsonHeaders(csrf) {
        return {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }
    function applyXhrHeaders(xhr, hdrs) {
        if (!hdrs || typeof hdrs !== 'object') return;
        Object.keys(hdrs).forEach(function (k) {
            var v = hdrs[k];
            if (v === undefined || v === null) return;
            if (Array.isArray(v)) v = v[0];
            try { xhr.setRequestHeader(k, v); } catch (e) {}
        });
    }
    function sleep(ms) {
        return new Promise(function (r) { setTimeout(r, ms); });
    }
    /** تأخير تصاعدي لإعادة محاولة رفع جزء طويل (انقطاع TCP / موبايل / بروكسي) */
    function backoffUploadAttempt(attempt) {
        var base = 900;
        var ms = Math.min(22000, base * Math.pow(2, attempt - 1));
        return sleep(ms);
    }
    function fetchJson(url, options) {
        return fetch(url, options).then(
            function (res) {
                return res.json().catch(function () { return {}; }).then(function (j) {
                    return { res: res, j: j };
                });
            },
            function () {
                return Promise.reject(new Error('network'));
            }
        );
    }
    function fetchJsonRetry(url, options, max) {
        max = max || 3;
        function attempt(n) {
            return fetchJson(url, options).then(
                function (o) {
                    var code = o.res.status;
                    var retriable = (code >= 502 && code <= 504) || code === 429 || code === 408;
                    if (retriable && n < max) {
                        return sleep(450 * n).then(function () { return attempt(n + 1); });
                    }
                    return o;
                },
                function (err) {
                    if (n < max && err && err.message === 'network') {
                        return sleep(450 * n).then(function () { return attempt(n + 1); });
                    }
                    throw err;
                }
            );
        }
        return attempt(1);
    }
    function putWithProgressRetry(url, body, contentType, extraHeaders, onProgress) {
        var max = 6;
        function tryLoad(n) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('PUT', url, true);
                xhr.timeout = 0;
                if (contentType) xhr.setRequestHeader('Content-Type', contentType);
                applyXhrHeaders(xhr, extraHeaders);
                xhr.upload.onprogress = function (ev) {
                    if (ev.lengthComputable && onProgress) onProgress(ev.loaded / ev.total);
                };
                function retryOrReject(httpErr) {
                    if (n < max) {
                        backoffUploadAttempt(n).then(function () { tryLoad(n + 1).then(resolve, reject); });
                    } else if (httpErr) reject(httpErr);
                    else reject(new Error('network'));
                }
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) resolve();
                    else if ((xhr.status === 0 || (xhr.status >= 502 && xhr.status <= 504) || xhr.status === 408) && n < max) {
                        retryOrReject(null);
                    } else reject(new Error('HTTP ' + xhr.status));
                };
                xhr.onerror = function () {
                    retryOrReject(null);
                };
                xhr.onabort = function () {
                    retryOrReject(null);
                };
                xhr.send(body);
            });
        }
        return tryLoad(1);
    }
    function putPartWithProgressRetry(url, blob, extraHeaders, onProgress) {
        var max = 8;
        function tryPart(n) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('PUT', url, true);
                xhr.timeout = 0;
                applyXhrHeaders(xhr, extraHeaders);
                xhr.upload.onprogress = function (ev) {
                    if (ev.lengthComputable && onProgress) onProgress(ev.loaded / ev.total);
                };
                function retryOrReject(httpErr) {
                    if (n < max) {
                        backoffUploadAttempt(n).then(function () { tryPart(n + 1).then(resolve, reject); });
                    } else if (httpErr) reject(httpErr);
                    else reject(new Error('network'));
                }
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        var etag = xhr.getResponseHeader('ETag') || xhr.getResponseHeader('etag');
                        if (!etag) {
                            reject(new Error('part_verify'));
                            return;
                        }
                        resolve(etag);
                        return;
                    } else if ((xhr.status === 0 || (xhr.status >= 502 && xhr.status <= 504) || xhr.status === 408) && n < max) {
                        retryOrReject(null);
                    } else reject(new Error('HTTP ' + xhr.status));
                };
                xhr.onerror = function () {
                    retryOrReject(null);
                };
                xhr.onabort = function () {
                    retryOrReject(null);
                };
                xhr.send(blob);
            });
        }
        return tryPart(1);
    }
    function postPartProxyWithProgressRetry(proxyUrl, csrf, sessionToken, partNum, chunk, onProgress) {
        var max = 6;
        function tryProxy(n) {
            return new Promise(function (resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', proxyUrl, true);
                xhr.timeout = 0;
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('Content-Type', 'application/octet-stream');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-Upload-Session-Token', sessionToken);
                xhr.setRequestHeader('X-Part-Number', String(partNum));
                xhr.upload.onprogress = function (ev) {
                    if (ev.lengthComputable && onProgress) onProgress(ev.loaded / ev.total);
                };
                function retryOrReject(httpErr) {
                    if (n < max) {
                        backoffUploadAttempt(n).then(function () { tryProxy(n + 1).then(resolve, reject); });
                    } else if (httpErr) reject(httpErr);
                    else reject(new Error('network'));
                }
                xhr.onload = function () {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            var j = JSON.parse(xhr.responseText || '{}');
                            if (j.etag) {
                                resolve(j.etag);
                                return;
                            }
                        } catch (eParse) {}
                        reject(new Error('تعذّر قراءة استجابة الرفع عبر الخادم.'));
                        return;
                    }
                    if ((xhr.status === 0 || (xhr.status >= 502 && xhr.status <= 504) || xhr.status === 408) && n < max) {
                        retryOrReject(null);
                    } else reject(new Error('HTTP ' + xhr.status));
                };
                xhr.onerror = function () { retryOrReject(null); };
                xhr.onabort = function () { retryOrReject(null); };
                xhr.send(chunk);
            });
        }
        return tryProxy(1);
    }
    function humanUploadError(err) {
        if (!err || !err.message) return 'تعذّر إكمال الرفع. تحقق من الاتصال ثم أعد المحاولة.';
        var m = String(err.message);
        if (m === 'network' || m.indexOf('network') >= 0) {
            return 'تعذّر إكمال الرفع. إن تكرّر ذلك استخدم «الرفع عبر الخادم» أو راجع CORS على bucket الـ R2 (PUT، ExposeHeaders: ETag).';
        }
        if (m === 'part_verify') {
            return 'تعذّر التحقق من جزء من الملف. أعد المحاولة؛ إن استمر ذلك استخدم «الرفع عبر الخادم» أسفل النموذج.';
        }
        if (m.indexOf('HTTP ') === 0) {
            var code = parseInt(m.replace(/^HTTP\s+/, ''), 10);
            if (code === 413) return 'الملف أكبر من الحد المسموح.';
            if (code === 403 || code === 401) return 'انتهت صلاحية الجلسة أو لا يُسمح بهذا الإجراء. حدّث الصفحة ثم أعد المحاولة.';
            if (code >= 500) return 'الخدمة مشغولة مؤقتاً. أعد المحاولة بعد قليل.';
        }
        if (/تعذر|تعذّر|فشل|الرفع|اختر/.test(m)) return m;
        return 'تعذّر إكمال الرفع. أعد المحاولة أو استخدم الرفع عبر الخادم.';
    }
    function setErr(wrap, msg) {
        var el = wrap.querySelector('[data-cl-mat-err]');
        if (!el) return;
        if (!msg) {
            el.classList.add('hidden');
            el.textContent = '';
            return;
        }
        el.textContent = msg;
        el.classList.remove('hidden');
    }
    function setPhase(wrap, text) {
        var el = wrap.querySelector('[data-cl-mat-phase]');
        if (el) el.textContent = text || '';
    }
    function setProgress(wrap, visible, pct, phaseText) {
        var outer = wrap.querySelector('[data-cl-mat-progress-wrap]');
        var bar = wrap.querySelector('[data-cl-mat-bar]');
        var lab = wrap.querySelector('[data-cl-mat-pct]');
        if (phaseText) setPhase(wrap, phaseText);
        if (!outer || !bar || !lab) return;
        if (!visible) {
            outer.classList.add('hidden');
            bar.style.width = '0%';
            lab.textContent = '0%';
            setPhase(wrap, '');
            return;
        }
        outer.classList.remove('hidden');
        var p = Math.max(0, Math.min(100, Math.round((pct || 0) * 100)));
        bar.style.width = p + '%';
        lab.textContent = p + '%';
    }
    function setBusy(wrap, busy) {
        var form = wrap.querySelector('form');
        if (!form) return;
        var subs = form.querySelectorAll('button[type="submit"], [data-cl-mat-classic-link]');
        for (var i = 0; i < subs.length; i++) subs[i].disabled = !!busy;
        var fin = form.querySelector('input[type="file"]');
        if (fin) fin.disabled = !!busy;
    }
    function runClMatSinglePutUpload(wrap, cfg, file, titleInp, viewChk, dlChk) {
        setPhase(wrap, 'جاري التحضير…');
        return fetchJsonRetry(cfg.presign, {
            method: 'POST',
            credentials: 'same-origin',
            headers: jsonHeaders(cfg.csrf),
            body: JSON.stringify({
                content_type: file.type || 'application/octet-stream',
                original_name: file.name,
                file_size: file.size
            })
        })
        .then(function (_ref) {
            var res = _ref.res;
            var presign = _ref.j;
            if (!res.ok || !presign.direct_upload || !presign.upload_url || !presign.upload_token) {
                throw new Error((presign && presign.message) ? presign.message : 'تعذّر تجهيز الرفع.');
            }
            setPhase(wrap, 'جاري رفع الملف…');
            return putWithProgressRetry(
                presign.upload_url,
                file,
                presign.content_type || file.type || 'application/octet-stream',
                presign.headers || {},
                function (p) { setProgress(wrap, true, p, 'جاري رفع الملف…'); }
            ).then(function () { return presign; });
        })
        .then(function (presign) {
            setPhase(wrap, 'جاري الإنهاء…');
            return fetchJsonRetry(cfg.complete, {
                method: 'POST',
                credentials: 'same-origin',
                headers: jsonHeaders(cfg.csrf),
                body: JSON.stringify({
                    upload_token: presign.upload_token,
                    title: titleInp ? titleInp.value : '',
                    view_in_platform: viewChk && viewChk.checked ? 1 : 0,
                    allow_download: dlChk && dlChk.checked ? 1 : 0
                })
            });
        })
        .then(function (_ref2) {
            var res = _ref2.res;
            var body = _ref2.j;
            if (!res.ok || !body.ok || !body.redirect) {
                throw new Error((body && body.message) ? body.message : 'فشل إتمام الرفع.');
            }
            window.location.href = body.redirect;
        });
    }
    function runClMatMultipartUpload(wrap, cfg, file, titleInp, viewChk, dlChk) {
        var sessionToken = null;
        setPhase(wrap, 'جاري التحضير…');
        return fetchJsonRetry(cfg.multipartInit, {
            method: 'POST',
            credentials: 'same-origin',
            headers: jsonHeaders(cfg.csrf),
            body: JSON.stringify({
                content_type: file.type || 'application/octet-stream',
                original_name: file.name,
                file_size: file.size
            })
        })
        .then(function (o) {
            if (!o.res.ok || !o.j.multipart || !o.j.upload_session_token) {
                throw new Error((o.j && o.j.message) ? o.j.message : 'تعذّر بدء الرفع.');
            }
            sessionToken = o.j.upload_session_token;
            var totalParts = parseInt(o.j.total_parts, 10) || 1;
            var partSize = parseInt(o.j.part_size, 10) || (16 * 1024 * 1024);
            var partsArr = [];
            function uploadPart(partNum) {
                if (partNum > totalParts) {
                    return Promise.resolve(partsArr);
                }
                var start = (partNum - 1) * partSize;
                var end = Math.min(start + partSize, file.size);
                var chunk = file.slice(start, end);
                var base = (partNum - 1) / totalParts;
                var weight = 1 / totalParts;
                var ph = 'جاري الرفع… الجزء ' + partNum + ' من ' + totalParts;
                setPhase(wrap, ph);
                function signPart() {
                    return fetchJsonRetry(cfg.multipartSignPart, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: jsonHeaders(cfg.csrf),
                        body: JSON.stringify({
                            upload_session_token: sessionToken,
                            part_number: partNum
                        })
                    });
                }
                function directPutPart() {
                    return signPart()
                        .then(function (sig) {
                            if (!sig.res.ok || !sig.j.url) {
                                throw new Error((sig.j && sig.j.message) ? sig.j.message : 'تعذّر تجهيز جزء من الملف.');
                            }
                            return putPartWithProgressRetry(sig.j.url, chunk, sig.j.headers || {}, function (frac) {
                                setProgress(wrap, true, base + weight * frac, ph);
                            }).catch(function (err) {
                                if (err && err.message === 'HTTP 403') {
                                    return signPart().then(function (sig2) {
                                        if (!sig2.res.ok || !sig2.j.url) {
                                            throw new Error((sig2.j && sig2.j.message) ? sig2.j.message : 'تعذّر تجهيز جزء من الملف.');
                                        }
                                        return putPartWithProgressRetry(sig2.j.url, chunk, sig2.j.headers || {}, function (frac) {
                                            setProgress(wrap, true, base + weight * frac, ph);
                                        });
                                    });
                                }
                                throw err;
                            });
                        });
                }
                function proxyPutPart() {
                    if (!cfg.multipartProxyPart) {
                        return Promise.reject(new Error('network'));
                    }
                    var phP = ph + ' — عبر الخادم';
                    setPhase(wrap, phP);
                    return postPartProxyWithProgressRetry(cfg.multipartProxyPart, cfg.csrf, sessionToken, partNum, chunk, function (frac) {
                        setProgress(wrap, true, base + weight * frac, phP);
                    });
                }
                var browserFirst = cfg.multipartBrowserFirst !== false;
                var partChain = browserFirst ? directPutPart() : proxyPutPart();
                return partChain.catch(function (err) {
                    if (cfg.multipartProxyPart && browserFirst) {
                        var mm = err && err.message ? String(err.message) : '';
                        if (mm === 'network' || mm === 'part_verify' || mm.indexOf('HTTP ') === 0) {
                            return proxyPutPart();
                        }
                    }
                    throw err;
                }).then(function (etag) {
                    partsArr.push({ PartNumber: partNum, ETag: etag });
                    return uploadPart(partNum + 1);
                });
            }
            return uploadPart(1);
        })
        .then(function (partsArr) {
            setPhase(wrap, 'جاري الدمج والإنهاء…');
            return fetchJsonRetry(cfg.multipartComplete, {
                method: 'POST',
                credentials: 'same-origin',
                headers: jsonHeaders(cfg.csrf),
                body: JSON.stringify({
                    upload_session_token: sessionToken,
                    parts: partsArr,
                    title: titleInp ? titleInp.value : '',
                    view_in_platform: viewChk && viewChk.checked ? 1 : 0,
                    allow_download: dlChk && dlChk.checked ? 1 : 0
                })
            });
        })
        .then(function (done) {
            if (!done.res.ok || !done.j.ok || !done.j.redirect) {
                throw new Error((done.j && done.j.message) ? done.j.message : 'فشل إتمام الرفع.');
            }
            window.location.href = done.j.redirect;
        })
        .catch(function (err) {
            if (sessionToken && cfg.multipartAbort) {
                fetch(cfg.multipartAbort, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: jsonHeaders(cfg.csrf),
                    body: JSON.stringify({ upload_session_token: sessionToken })
                }).catch(function () {});
            }
            throw err;
        });
    }
    function runClMatDirectUpload(wrap) {
        var raw = '';
        var jsonEl = wrap.querySelector('script[type="application/json"][data-cl-mat-cfg-json]');
        if (jsonEl && jsonEl.textContent) raw = jsonEl.textContent.trim();
        if (!raw) raw = wrap.getAttribute('data-cl-mat-cfg') || '';
        if (!raw) {
            setErr(wrap, 'تعذّر تفعيل الرفع. حدّث الصفحة ثم أعد المحاولة.');
            return;
        }
        var cfg;
        try {
            cfg = JSON.parse(raw);
        } catch (err) {
            setErr(wrap, 'تعذّر قراءة إعدادات الرفع. حدّث الصفحة ثم أعد المحاولة.');
            return;
        }
        cfg.maxBytes = parseInt(cfg.maxBytes, 10) || (150 * 1024 * 1024);
        cfg.multipartThreshold = parseInt(cfg.multipartThreshold, 10) || 12582912;
        var form = wrap.querySelector('form[data-cl-mat-form="1"]') || wrap.querySelector('form');
        var fileInput = wrap.querySelector('input[type="file"]');
        if (!form || !fileInput || !fileInput.files || !fileInput.files[0]) {
            setErr(wrap, 'اختر ملفاً أولاً.');
            return;
        }
        var file = fileInput.files[0];
        if (file.size > cfg.maxBytes) {
            setErr(wrap, 'الملف أكبر من الحد المسموح.');
            return;
        }
        var titleInp = form.querySelector('input[name="title"]');
        var viewChk = form.querySelector('input[name="view_in_platform"][type="checkbox"]');
        var dlChk = form.querySelector('input[name="allow_download"][type="checkbox"]');
        setErr(wrap, '');
        setBusy(wrap, true);
        setProgress(wrap, true, 0, 'جاري البدء…');
        var threshold = typeof cfg.multipartThreshold === 'number' ? cfg.multipartThreshold : 12582912;
        var useMp = file.size >= threshold && cfg.multipartInit && cfg.multipartSignPart && cfg.multipartComplete;
        var chain = useMp
            ? runClMatMultipartUpload(wrap, cfg, file, titleInp, viewChk, dlChk)
            : runClMatSinglePutUpload(wrap, cfg, file, titleInp, viewChk, dlChk);
        chain.catch(function (err) {
            setErr(wrap, humanUploadError(err));
            setProgress(wrap, false, 0);
            setBusy(wrap, false);
        });
    }
    document.addEventListener('click', function (e) {
        var classic = e.target && e.target.closest ? e.target.closest('[data-cl-mat-classic-link]') : null;
        if (!classic) return;
        var wrap = classic.closest('[data-cl-mat-wrap]');
        if (!wrap) return;
        var form = wrap.querySelector('form[data-cl-mat-form="1"]');
        if (!form) return;
        e.preventDefault();
        wrap.dataset.forceClassicUpload = '1';
        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.submit();
        }
    });
    document.addEventListener('submit', function (e) {
        var form = null;
        if (e.submitter && typeof e.submitter.closest === 'function') {
            form = e.submitter.closest('form[data-cl-mat-form="1"]');
        }
        if (!form && e.target && typeof e.target.closest === 'function') {
            form = e.target.closest('form[data-cl-mat-form="1"]');
        }
        if (!form || form.getAttribute('data-cl-mat-form') !== '1') return;
        var wrap = form.closest('[data-cl-mat-wrap]');
        if (!wrap) return;
        if (wrap.dataset.forceClassicUpload === '1') {
            delete wrap.dataset.forceClassicUpload;
            return;
        }
        var fileInput = wrap.querySelector('input[type="file"]');
        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            setErr(wrap, 'اختر ملفاً أولاً.');
            return;
        }
        e.preventDefault();
        runClMatDirectUpload(wrap);
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\glottical\resources\views\admin\curriculum-library\structure.blade.php ENDPATH**/ ?>