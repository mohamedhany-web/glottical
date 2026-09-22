# تشغيل LiveKit لـ Glottical على VPS 187.124.36.228

## الهدف
- `live.glottical.com` هو نطاق LiveKit لمنصة Glottical.
- كل غرف البث وClassroom تعمل عبر LiveKit فقط.
- نموذج الحصة الموصى به: **معلم (كاميرا + شير) → طلاب مشاهدين** (مش Zoom بكل الكاميرات).

## مواصفات السيرفر الحالية (مراجعة 2026-09-22)

| البند | القيمة |
|------|--------|
| Host | `live.glottical.com` / `187.124.36.228` |
| CPU | **2 cores** (AMD EPYC shared) |
| RAM | **8 GB** (+ 2 GB swap) |
| Disk | ~96 GB (خفيف الاستخدام) |
| LiveKit | Docker `mx-livekit` — `network_mode: host` |
| Redis | غير مثبت (node واحد فقط حالياً) |
| Egress/Recording | غير مثبت على هذا السيرفر (مقصود) |
| Monitoring | Prometheus metrics على المنفذ `6789` (محلي) |

### تقدير القدرة الآمنة (سيناريو أكاديمية)

| السيناريو | تقدير آمن على 2 cores |
|-----------|------------------------|
| معلم كاميرا + شاشة، طلاب مشاهدة فقط | **30–50** مشارك في غرفة واحدة |
| نفس السيناريو مع ضغط خفيف | حتى **~60** (`max_participants`) |
| 20 كاميرا مفتوحة + 100 مشترك | **غير مناسب** لهذا الـ VPS |
| عدة غرف متزامنة | مجموع المشاركين عبر كل الغرف يجب أن يبقى تحت ~80–100 |

> Bandwidth تقريباً: مدرس 720p ~2 Mbps × عدد الطلاب = outbound.  
> مثال: 50 طالب مشاهدة ≈ **~100 Mbps** outbound — مقبول على شبكة 1 Gbps بشرط عدم وجود حد شهري خانق.

عند الحاجة لـ **100–200** طالب مشاهدة في غرفة واحدة: ارفع الـ VPS إلى **8+ cores / 16 GB** أو أضف LiveKit node ثاني (كل Room على node واحد).

---

## 1) DNS (Hostinger)
| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | live | 187.124.36.228 | 300 |

```bash
nslookup live.glottical.com
```

## 2) منافذ Firewall (UFW على الـ VPS — مفعّل)

| البروتوكول | المنفذ | الغرض |
|------------|--------|--------|
| TCP | 22 | SSH |
| TCP | 80, 443 | ACME + WSS عبر nginx |
| TCP | 7880 | LiveKit API (داخلي/تشخيص) |
| TCP | 7881 | ICE/TCP |
| TCP | 5351 | TURN/TLS |
| UDP | 34789 | TURN/UDP |
| UDP | 50000–60000 | WebRTC media |
| UDP | 30000–40000 | TURN relay |

افتح نفس المنافذ في **Hostinger VPS Firewall** إن وُجدت طبقة خارجية.

## 3) إعدادات LiveKit الحرجة (`/opt/livekit/livekit.yaml`)

- `rtc.use_external_ip: true` + `node_ip: 187.124.36.228`
- `turn.enabled: true` مع `udp_port: 34789` و `tls_port: 5351`
- شهادات TURN من `/opt/livekit/certs/` (تُحدَّث تلقائياً عند تجديد Let's Encrypt)
- `room.max_participants: 60` — حماية من overload على 2 cores
- `prometheus_port: 6789` — مقاييس محلية

تجديد الشهادة:
```bash
# hook موجود: /etc/letsencrypt/renewal-hooks/deploy/livekit-certs.sh
certbot renew --dry-run
```

## 4) منصة Glottical (`.env`)
```
LIVEKIT_URL=wss://live.glottical.com
LIVEKIT_PUBLIC_HOST=live.glottical.com
LIVEKIT_HTTP_URL=http://187.124.36.228:7880
LIVEKIT_API_KEY=...
LIVEKIT_API_SECRET=...
```

```bash
php artisan config:clear
php artisan livekit:provision-glottical --set-default
```

## 5) تحقق سريع
```bash
curl -I https://live.glottical.com/          # 200
curl http://127.0.0.1:7880/                  # OK
curl -s http://127.0.0.1:6789/metrics | head # Prometheus
docker ps --filter name=mx-livekit
ufw status
```

## 6) مخاطر الإنتاج وخطة التوسع

| الخطر | الحالة الآن | الخطوة التالية |
|-------|-------------|----------------|
| VPS واحد = نقطة فشل | 🔴 موجود | LiveKit node ثاني + DNS/LB لاحقاً |
| Bandwidth / CPU محدود | 🟠 2 cores | ترقية لـ 8 cores قبل حصص 100+ |
| TURN | 🟢 مفعّل UDP+TLS | راقب شكاوى الشركات/الجامعات |
| Recording على نفس السيرفر | 🟢 غير مثبت (مقصود) | التسجيل من المتصفح → Cloudflare R2 عبر Hostinger |
| Monitoring | 🟠 metrics محلية فقط | Prometheus+Grafana أو Uptime Kuma |
| Redis | 🟠 غير موجود | لازم عند multi-node |

### التسجيل (Recording) — ليس على LiveKit VPS
- **لا يوجد LiveKit Egress** على `187.124.36.228` (لا container ولا binary).
- المسار الحالي: **Browser MediaRecorder → Presign من Laravel (Hostinger) → رفع مباشر إلى Cloudflare R2** (`live_recordings_r2`).
- الـ VPS يخدم فقط WebRTC/WSS/TURN؛ ملفات التسجيل لا تُكتب على قرصه.
- Egress منفصل على VPS آخر يبقى خيار توسع لاحقاً إن احتجنا تسجيل سيرفري مركّب — حالياً غير مطلوب لأن الرفع على R2 يعمل من العميل.

### مسار توسع مقترح (Mindlytics-style)
1. الإبقاء على نموذج: مدرس ينشر، طلاب يشاهدون (أقل كاميرات = أقل bandwidth).
2. ترقية هذا الـ VPS أو فصل LiveKit عن أي خدمات أخرى.
3. عند نمو الغرف المتزامنة: node 02 + Redis.
4. عند الحاجة لتسجيل سيرفري مركّب: Egress على VPS منفصل → نفس R2 (ليس على سيرفر LiveKit).
5. Load test قبل الإطلاق الكبير: `lk load-test` بنفس سيناريو الحصة.

## ملاحظات تشغيل
- Container: `/opt/livekit` + `docker compose`
- Nginx: `/etc/nginx/sites-enabled/live.glottical.com.conf` → `127.0.0.1:7880`
- لا تشغّل Jitsi/muallimx STUN على نفس منافذ LiveKit TURN.
- بعد أي تعديل yaml: `cd /opt/livekit && docker compose up -d`
