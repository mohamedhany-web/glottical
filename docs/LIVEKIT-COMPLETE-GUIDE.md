# دليل LiveKit الكامل — Glottical

دليل تشغيل وصيانة خدمة البث المباشر (WebRTC) لمنصة **Glottical** من الصفر حتى الإنتاج.

| | |
|---|---|
| النطاق العام | `live.glottical.com` |
| IP الـ VPS | `187.124.36.228` |
| مجلد السيرفر | `/opt/livekit` |
| Container | `mx-livekit` (`livekit/livekit-server`) |
| تطبيق Laravel | Hostinger (منفصل عن سيرفر LiveKit) |
| التسجيل | المتصفح → Cloudflare R2 — **ليس** على سيرفر LiveKit |

مرجع سريع مختصر: [`docs/ops-live-glottical-livekit.md`](ops-live-glottical-livekit.md)  
سكربتات: `scripts/setup-live-glottical-livekit.sh` · `scripts/test-livekit-meeting.sh` · `scripts/diagnose-livekit-audio-server.sh`

---

## 1) المعمارية (ماذا يعمل أين؟)

```
[طالب / معلم — المتصفح]
        │  wss://live.glottical.com  (WebSocket + WebRTC)
        ▼
[Nginx :443] ──proxy──► [LiveKit :7880] على VPS 187.124.36.228
        │                      │
        │                      ├── RTC UDP 50000–60000
        │                      ├── ICE TCP 7881
        │                      └── TURN UDP 34789 + TLS 5351
        │
[Laravel على Hostinger]
        ├── يصدر JWT (LIVEKIT_API_KEY/SECRET)
        ├── يفتح غرف Classroom / Live Sessions
        └── Presign تسجيل → رفع المتصفح مباشرة إلى Cloudflare R2
```

### قواعد مهمة
1. **سيرفر LiveKit = وسائط فقط** (WSS / WebRTC / TURN). لا تثبت عليه Laravel ولا قاعدة بيانات المنصة.
2. **لا تثبّت LiveKit Egress على نفس الـ VPS** — التسجيل الحالي من المتصفح إلى R2.
3. **مفاتيح API واحدة** في `livekit.yaml` على الـ VPS **ونفسها** في `.env` على Hostinger.
4. المعلم والطالب يجب أن يدخلا **نفس اسم الغرفة** (`ClassroomMeeting::liveRoomName()` أو `LiveSession.room_name`).
5. نموذج الحصة الموصى به: **معلم ينشر (كاميرا + شاشة) · طلاب يشاهدون** — يقلل استهلاك الـ bandwidth والـ CPU.

---

## 2) مواصفات السيرفر الحالية وتقدير القدرة

| البند | القيمة |
|------|--------|
| Host | `live.glottical.com` / `187.124.36.228` |
| CPU | 2 cores (AMD EPYC shared) |
| RAM | 8 GB + 2 GB swap |
| Disk | ~96 GB |
| Docker | `network_mode: host` |
| Redis | غير مثبت (node واحد) |
| Egress | غير مثبت (مقصود) |
| Prometheus | منفذ `6789` (محلي على الـ VPS) |

| السيناريو | تقدير آمن على 2 cores |
|-----------|------------------------|
| معلم كاميرا + شاشة، طلاب مشاهدة | **30–50** مشارك / غرفة |
| نفس السيناريو بضغط خفيف | حتى **~60** (`max_participants`) |
| 20 كاميرا + 100 مشترك | **غير مناسب** |
| عدة غرف متزامنة | مجموع المشاركين عبر كل الغرف ≈ تحت 80–100 |

> تقدير bandwidth: مدرس 720p ~2 Mbps × عدد الطلاب = outbound.  
> مثال: 50 طالب ≈ **~100 Mbps** outbound.

لحصص **100–200** مشاهد: رقِّ الـ VPS إلى **8+ cores / 16 GB** أو أضف node ثاني.

---

## 3) تجهيز الـ VPS من الصفر

نفّذ على سيرفر Ubuntu 22.04/24.04 كـ `root`.

### 3.1 تحديث النظام وحزم أساسية

```bash
apt update && apt upgrade -y
apt install -y ca-certificates curl gnupg ufw nginx certbot python3-certbot-nginx \
  wget jq openssl python3
```

### 3.2 تثبيت Docker Engine + Compose plugin

```bash
# Docker الرسمي
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
chmod a+r /etc/apt/keyrings/docker.gpg

echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] \
  https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" \
  > /etc/apt/sources.list.d/docker.list

apt update
apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

docker --version
docker compose version
systemctl enable --now docker
```

### 3.3 Swap (موصى به على 2 cores / 8 GB)

```bash
fallocate -l 2G /swapfile || dd if=/dev/zero of=/swapfile bs=1M count=2048
chmod 600 /swapfile
mkswap /swapfile
swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab
free -h
```

### 3.4 توليد مفاتيح API

```bash
# مفتاح واسم سري قويان — احفظهما في مكان آمن
API_KEY="API$(openssl rand -hex 8)"
API_SECRET="$(openssl rand -base64 32 | tr -d '/+=' | head -c 43)"
echo "LIVEKIT_API_KEY=$API_KEY"
echo "LIVEKIT_API_SECRET=$API_SECRET"
```

نفس القيم تُوضع لاحقاً في:
- `/opt/livekit/livekit.yaml` → قسم `keys:`
- `.env` على Hostinger → `LIVEKIT_API_KEY` / `LIVEKIT_API_SECRET`

---

## 4) DNS

عند Hostinger (نطاق `glottical.com`):

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | `live` | `187.124.36.228` | 300 |

```bash
dig +short live.glottical.com A
# يجب أن يظهر: 187.124.36.228
```

لا تُصدر شهادة SSL قبل أن يشير الـ DNS بشكل صحيح.

---

## 5) Firewall (UFW + لوحة الـ VPS)

### 5.1 UFW على السيرفر

```bash
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp comment 'SSH'
ufw allow 80/tcp comment 'HTTP ACME'
ufw allow 443/tcp comment 'HTTPS WSS'
ufw allow 7880/tcp comment 'LiveKit API'
ufw allow 7881/tcp comment 'ICE TCP'
ufw allow 5351/tcp comment 'TURN TLS'
ufw allow 34789/udp comment 'TURN UDP'
ufw allow 50000:60000/udp comment 'WebRTC media'
ufw allow 30000:40000/udp comment 'TURN relay'
ufw --force enable
ufw status numbered
```

| البروتوكول | المنفذ | الغرض |
|------------|--------|--------|
| TCP | 22 | SSH |
| TCP | 80, 443 | ACME + WSS عبر nginx |
| TCP | 7880 | LiveKit HTTP/API |
| TCP | 7881 | ICE / TCP fallback |
| TCP | 5351 | TURN/TLS |
| UDP | 34789 | TURN/UDP |
| UDP | 50000–60000 | WebRTC media |
| UDP | 30000–40000 | TURN relay |

### 5.2 Firewall لوحة Hostinger VPS
افتح **نفس المنافذ** في أي طبقة Firewall خارجية على الـ VPS — وإلا UDP/TURN سيفشل رغم أن UFW مفتوح.

> لا تستخدم منافذ Jitsi/coturn القديمة على نفس السيرفر لنفس الغرض إن كانت متعارضة: `3478` / `5349` غالباً لـ muallimx. LiveKit يستخدم **34789** و **5351**.

---

## 6) مجلد LiveKit + Docker Compose

### 6.1 إنشاء الهيكل

```bash
mkdir -p /opt/livekit/certs
cd /opt/livekit
```

### 6.2 ملف `docker-compose.yml` (الإنتاج الحالي)

```yaml
services:
  livekit:
    # ثبّت إصداراً صريحاً — لا تستخدم :latest في الإنتاج
    image: livekit/livekit-server:v1.13.7
    container_name: mx-livekit
    command: --config /etc/livekit.yaml
    restart: unless-stopped
    network_mode: host
    volumes:
      - ./livekit.yaml:/etc/livekit.yaml:ro
      - ./certs:/etc/livekit/certs:ro
    healthcheck:
      test: ["CMD", "wget", "-qO-", "http://127.0.0.1:7880/"]
      interval: 30s
      timeout: 5s
      retries: 3
      start_period: 20s
    deploy:
      resources:
        limits:
          cpus: "1.8"
          memory: 3072M
        reservations:
          cpus: "0.5"
          memory: 512M
```

**لماذا `network_mode: host`؟**  
WebRTC يحتاج منافذ UDP كثيرة وTURN؛ وضع host يبسّط الـ NAT ويقلل مشاكل ICE على VPS صغير.

**تحديث الصورة لاحقاً:** غيّر الرقم فقط (مثلاً `v1.13.8`) ثم `docker compose pull && docker compose up -d`.

### 6.3 ملف `livekit.yaml` (الإنتاج الحالي — انسخ وعدّل المفاتيح فقط)

مرجع المستودع: [`config/livekit-server.example.yaml`](../config/livekit-server.example.yaml)

```yaml
# Glottical LiveKit — production baseline
port: 7880
bind_addresses:
  - "0.0.0.0"
log_level: info
prometheus_port: 6789

rtc:
  tcp_port: 7881
  port_range_start: 50000
  port_range_end: 60000
  use_external_ip: true
  node_ip: 187.124.36.228
  packet_buffer_size_video: 800
  packet_buffer_size_audio: 300
  congestion_control:
    enabled: true
    allow_pause: false
  interfaces:
    includes:
      - eth0
    excludes:
      - docker0
      - br-+
      - veth+
  stun_servers:
    - stun.l.google.com:19302

turn:
  enabled: true
  domain: live.glottical.com
  cert_file: /etc/livekit/certs/fullchain.pem
  key_file: /etc/livekit/certs/privkey.pem
  # external_tls=false => LiveKit نفسه يخدم TLS على tls_port
  udp_port: 34789
  tls_port: 5351
  external_tls: false
  relay_range_start: 30000
  relay_range_end: 40000

room:
  empty_timeout: 300
  departure_timeout: 30
  enable_remote_unmute: true
  # سقف آمن على VPS بـ 2 cores
  max_participants: 60

keys:
  YOUR_API_KEY: YOUR_API_SECRET
```

#### إعدادات حرجة — لا تغيّرها بدون سبب

| المفتاح | القيمة الصحيحة | لماذا |
|---------|----------------|--------|
| `rtc.use_external_ip` | `true` | العملاء يحتاجون الـ IP العام في ICE |
| `rtc.node_ip` | `187.124.36.228` | تثبيت الـ IP وعدم الاعتماد على اكتشاف خاطئ داخل Docker |
| `turn.enabled` | `true` | شبكات مصر/الموبايل/الجامعات غالباً تحتاج TURN |
| `turn.external_tls` | `false` | LiveKit يقدّم TLS على 5351 مباشرة |
| `room.max_participants` | `60` | حماية الـ CPU |

> قبل وجود شهادة في `/opt/livekit/certs/` يمكن تشغيل LiveKit مؤقتاً بدون TURN، أو نسخ شهادات مؤقتة بعد certbot (الخطوة التالية).

### 6.4 تشغيل أولي (قبل الشهادة — بدون TURN إن لزم)

إن لم تكن الشهادات جاهزة بعد، علّق مؤقتاً قسم `turn:` أو ضع ملفات وهميّة بعد إصدار الشهادة أولاً (موصى به: أكمل Nginx+certbot ثم انسخ الشهادات ثم ارفع الـ container).

```bash
cd /opt/livekit
docker compose pull
docker compose up -d
docker ps --filter name=mx-livekit
curl -fsS http://127.0.0.1:7880/ && echo " OK"
```

---

## 7) Nginx + Let's Encrypt + ربط شهادات TURN

### 7.1 موقع Nginx

يمكن استخدام السكربت الجاهز من المستودع (على الـ VPS بعد نسخ الملفات أو لصق المحتوى):

```bash
export LIVEKIT_API_KEY='...'
export LIVEKIT_API_SECRET='...'
# من نسخة المشروع أو انسخ محتوى scripts/setup-live-glottical-livekit.sh
sudo -E bash setup-live-glottical-livekit.sh
```

أو يدوياً — ملف `/etc/nginx/sites-available/live.glottical.com.conf`:

```nginx
# Glottical LiveKit — live.glottical.com
server {
    listen 80;
    listen [::]:80;
    server_name live.glottical.com;

    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }

    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name live.glottical.com;

    ssl_certificate     /etc/letsencrypt/live/live.glottical.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/live.glottical.com/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    location / {
        proxy_pass http://127.0.0.1:7880;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 86400s;
        proxy_send_timeout 86400s;
    }
}
```

```bash
ln -sfn /etc/nginx/sites-available/live.glottical.com.conf /etc/nginx/sites-enabled/
# أولاً: نسخة HTTP-only لإصدار الشهادة إن لم تكن موجودة
certbot --nginx -d live.glottical.com --non-interactive --agree-tos -m info@glottical.com --redirect
nginx -t && systemctl reload nginx
```

### 7.2 نسخ الشهادات لـ TURN داخل Docker

LiveKit يقرأ الشهادات من `/etc/livekit/certs` داخل الـ container (= `/opt/livekit/certs` على المضيف).

```bash
install -d -m 755 /opt/livekit/certs
cp -L /etc/letsencrypt/live/live.glottical.com/fullchain.pem /opt/livekit/certs/fullchain.pem
cp -L /etc/letsencrypt/live/live.glottical.com/privkey.pem /opt/livekit/certs/privkey.pem
chmod 644 /opt/livekit/certs/fullchain.pem
chmod 600 /opt/livekit/certs/privkey.pem
cd /opt/livekit && docker compose up -d
```

### 7.3 Hook تجديد الشهادة (إلزامي)

`/etc/letsencrypt/renewal-hooks/deploy/livekit-certs.sh`:

```bash
#!/bin/bash
set -euo pipefail
DOMAIN=live.glottical.com
DEST=/opt/livekit/certs
if [ -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" ]; then
  install -d -m 755 "$DEST"
  cp -L "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" "$DEST/fullchain.pem"
  cp -L "/etc/letsencrypt/live/${DOMAIN}/privkey.pem" "$DEST/privkey.pem"
  chmod 644 "$DEST/fullchain.pem"
  chmod 600 "$DEST/privkey.pem"
  cd /opt/livekit && docker compose restart livekit || docker restart mx-livekit || true
fi
```

```bash
chmod +x /etc/letsencrypt/renewal-hooks/deploy/livekit-certs.sh
certbot renew --dry-run
```

---

## 8) ربط منصة Glottical (Hostinger / Laravel)

### 8.1 متغيرات `.env`

```env
LIVEKIT_URL=wss://live.glottical.com
LIVEKIT_PUBLIC_HOST=live.glottical.com
LIVEKIT_HTTP_URL=http://187.124.36.228:7880
LIVEKIT_API_KEY=...          # نفس مفتاح livekit.yaml
LIVEKIT_API_SECRET=...       # نفس السر
LIVEKIT_TOKEN_TTL=21600
```

الإعداد في الكود: [`config/livekit.php`](../config/livekit.php)

### 8.2 بعد تعديل `.env`

```bash
cd ~/domains/glottical.com/public_html/glottical   # أو HOSTINGER_APP_PATH
php artisan config:clear
php artisan livekit:provision-glottical --set-default
```

الأمر يسجّل السيرفر في جدول `live_servers` ويضبط مزود البث الافتراضي على LiveKit.

### 8.3 تحديث مفاتيح على الـ VPS فقط

```bash
export LIVEKIT_API_KEY='...'
export LIVEKIT_API_SECRET='...'
sudo -E bash scripts/install-livekit-keys-on-vps.sh
cd /opt/livekit && docker compose restart livekit
```

ثم حدّث نفس المفاتيح في `.env` على Hostinger + `config:clear`.

---

## 9) التسجيل (Recording) — خارج سيرفر LiveKit

| السؤال | الجواب |
|--------|--------|
| هل يوجد Egress على الـ VPS؟ | **لا** (مقصود) |
| أين يُسجَّل الفيديو؟ | المتصفح (`MediaRecorder`) |
| أين يُرفع؟ | Cloudflare R2 عبر Presign من Laravel |
| القرص في Laravel | `live_recordings_r2` في `config/filesystems.php` |

مسار التدفق:
1. المعلم يوقف الحصة / ينهي التسجيل في الواجهة.
2. المتصفح يطلب `classroom.recording.presign`.
3. Laravel يُرجع `upload_url` موقّع لـ R2.
4. المتصفح يعمل `PUT` مباشرة إلى R2.
5. `recording.complete` يحفظ المسار في `classroom_meetings`.

متغيرات R2 (على Hostinger):

```env
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_BUCKET=...
AWS_ENDPOINT=https://xxxx.r2.cloudflarestorage.com
AWS_USE_PATH_STYLE_ENDPOINT=true
R2_PUBLIC_URL=...   # اختياري للعرض
# أو مفاتيح منفصلة للتسجيلات:
# R2_LIVE_RECORDINGS_ACCESS_KEY_ID=
# R2_LIVE_RECORDINGS_SECRET_ACCESS_KEY=
# R2_LIVE_RECORDINGS_BUCKET=
# R2_LIVE_RECORDINGS_ENDPOINT=
```

في R2 CORS للـ bucket: اسمح بـ `PUT, GET, HEAD` من نطاق الموقع.

**توسع لاحق (اختياري):** LiveKit Egress على **VPS منفصل** → نفس R2. لا تضعه على سيرفر الوسائط الحالي.

---

## 10) التحقق والاختبارات

### 10.1 من الـ VPS

```bash
docker ps --filter name=mx-livekit
curl -fsS http://127.0.0.1:7880/ && echo OK
curl -fsSI https://live.glottical.com/ | head
ss -tulpn | grep -E '7880|7881|34789|5351'
curl -fsS http://127.0.0.1:6789/metrics | head
ufw status
sudo bash scripts/diagnose-livekit-audio-server.sh
```

### 10.2 TURN TLS

```bash
echo | openssl s_client -connect 187.124.36.228:5351 -servername live.glottical.com 2>&1 \
  | grep -E 'Verify return code|subject=|Protocol'
# المتوقع: Verify return code: 0 (ok) و CN=live.glottical.com
```

### 10.3 اختبار API غرفة (من جهازك أو الـ VPS)

```bash
export LIVEKIT_API_KEY='...'
export LIVEKIT_API_SECRET='...'
export LIVEKIT_HTTP_URL='http://187.124.36.228:7880'
export LIVEKIT_PUBLIC_HOST='live.glottical.com'
bash scripts/test-livekit-meeting.sh
# المتوقع: ALL API TESTS PASSED
```

### 10.4 اختبارات Laravel (مسار التسجيل)

```bash
php artisan test tests/Feature/ClassroomRecordingR2UploadTest.php
php artisan test tests/Feature/LiveKitRoomProviderTest.php
php artisan test tests/Unit/LiveKitTokenServiceTest.php
```

### 10.5 قائمة تحقق قبل الإطلاق

- [ ] DNS `live` → IP الصحيح
- [ ] `https://live.glottical.com/` يرد 200 / OK
- [ ] Container `mx-livekit` = healthy
- [ ] UFW + Firewall اللوحة مفتوحان لـ UDP media + TURN
- [ ] `use_external_ip: true` و `node_ip` مضبوطان
- [ ] شهادات TURN في `/opt/livekit/certs` ومحدَّثة عبر hook
- [ ] مفاتيح `.env` = مفاتيح `livekit.yaml`
- [ ] `php artisan livekit:provision-glottical --set-default`
- [ ] حصة تجريبية: معلم ينشر صوت/فيديو + طالب يسمع ويرى
- [ ] اختبار من شبكة موبايل (TURN)
- [ ] تسجيل تجريبي يظهر في R2 / لوحة التسجيلات

---

## 11) التشغيل اليومي والصيانة

### أوامر شائعة

```bash
cd /opt/livekit

# حالة
docker compose ps
docker logs --tail 100 mx-livekit

# بعد تعديل yaml
docker compose up -d
# أو
docker compose restart livekit

# تحديث صورة LiveKit
docker compose pull
docker compose up -d

# صحة دورية (مثال cron كل 5 دقائق)
# */5 * * * * /usr/local/bin/glottical-livekit-health.sh
```

### مثال سكربت صحة بسيط

`/usr/local/bin/glottical-livekit-health.sh`:

```bash
#!/bin/bash
set -euo pipefail
if ! curl -fsS --max-time 5 http://127.0.0.1:7880/ >/dev/null; then
  logger -t glottical-livekit "health FAIL — restarting"
  cd /opt/livekit && docker compose restart livekit
fi
```

```bash
chmod +x /usr/local/bin/glottical-livekit-health.sh
```

### نسخ احتياطي للإعداد

```bash
tar -czf "/root/livekit-backup-$(date +%Y%m%d).tgz" \
  /opt/livekit/livekit.yaml \
  /opt/livekit/docker-compose.yml \
  /etc/nginx/sites-available/live.glottical.com.conf
# لا ترفع المفاتيح إلى Git عام
```

---

## 12) استكشاف الأعطال

| العَرَض | السبب الشائع | العلاج |
|---------|--------------|--------|
| لا يدخل أحد الغرفة | مفاتيح `.env` ≠ `livekit.yaml` | وحّد المفاتيح + `config:clear` + restart container |
| صوت/فيديو ينقطع بعد دقائق | UDP محجوب / بدون TURN | فعّل TURN + افتح 34789 و 30000–40000 و 50000–60000 |
| يعمل على الواي فاي ويفشل على الموبايل | NAT صارم | تأكد TURN TLS 5351 وشهادة صالحة |
| `could not validate external IP` في اللوج | تحذير شائع مع Docker | طالما `node_ip` مضبوط يُتجاهل عادةً |
| `TLS handshake failed: EOF` على TURN | probe غير TLS (nc) | تجاهل إن `openssl s_client` ناجح |
| WSS يفشل من المتصفح | Nginx/SSL أو proxy headers | راجع `Upgrade` / `Connection` و timeout 86400 |
| التسجيل لا يُرفع | R2/CORS/مفاتيح Hostinger | افحص Presign و CORS على الـ bucket |
| غرفة فارغة رغم دخول الطرفين | اختلاف اسم الغرفة في JWT | راجع `liveRoomName()` / `room_name` |
| حمل عالي / تقطيع مع 80+ | VPS صغير | خفّض الكاميرات أو رقِّ السيرفر |

تشخيص سريع على السيرفر:

```bash
sudo bash scripts/diagnose-livekit-audio-server.sh
docker logs --tail 200 mx-livekit | grep -iE 'error|turn|ice|started'
```

---

## 13) خطة التوسع

| المرحلة | ماذا تفعل |
|---------|-----------|
| الآن | Node واحد + TURN + تسجيل متصفح→R2 |
| نمو متوسط | ترقية VPS إلى 8 cores / 16 GB قبل حصص 100+ |
| نمو كبير | Node ثاني + **Redis** لتوزيع الغرف |
| تسجيل سيرفري مركّب | **Egress على VPS منفصل** → R2 (ليس على سيرفر الوسائط) |
| مراقبة | Prometheus (`:6789`) + Grafana أو Uptime Kuma خارجياً على WSS |

Load test قبل الإطلاق الكبير:

```bash
# بعد تثبيت LiveKit CLI (lk)
lk load-test --url wss://live.glottical.com --api-key ... --api-secret ... \
  --room load-test --publishers 1 --subscribers 40
```

---

## 14) خريطة ملفات المشروع ذات الصلة

| الملف | الدور |
|-------|--------|
| `config/livekit.php` | إعدادات Laravel |
| `config/livekit-server.example.yaml` | قالب إعداد السيرفر |
| `app/Services/LiveKitTokenService.php` | إصدار JWT |
| `app/Services/LiveMeetingProvider.php` | ربط الغرف بالواجهة |
| `app/Console/Commands/ProvisionGlotticalLiveKitCommand.php` | تسجيل السيرفر في DB |
| `resources/views/partials/livekit-room.blade.php` | واجهة الغرفة + MediaRecorder |
| `scripts/setup-live-glottical-livekit.sh` | Nginx + SSL على الـ VPS |
| `scripts/install-livekit-keys-on-vps.sh` | تحديث المفاتيح |
| `scripts/test-livekit-meeting.sh` | اختبار API |
| `scripts/diagnose-livekit-audio-server.sh` | تشخيص صوت/منافذ |
| `docs/ops-live-glottical-livekit.md` | مرجع تشغيل مختصر |
| `.env.ops.local` | بيانات SSH للعمليات (gitignored) |

---

## 15) ملخص أوامر «من صفر إلى جاهز» (نسخ سريع)

```bash
# —— على الـ VPS ——
apt update && apt install -y nginx certbot python3-certbot-nginx ufw
# ثبّت Docker Compose كما في القسم 3.2
mkdir -p /opt/livekit/certs
# انسخ docker-compose.yml + livekit.yaml (ضع المفاتيح)
ufw allow 22,80,443,7880,7881,5351/tcp
ufw allow 34789,50000:60000,30000:40000/udp
ufw --force enable

# DNS: A live → 187.124.36.228 ثم:
certbot --nginx -d live.glottical.com ...
cp -L /etc/letsencrypt/live/live.glottical.com/*.pem /opt/livekit/certs/
# ثبّت renewal hook
cd /opt/livekit && docker compose up -d

# —— على Hostinger ——
# LIVEKIT_* في .env ثم:
php artisan config:clear
php artisan livekit:provision-glottical --set-default

# —— تحقق ——
curl -fsS https://live.glottical.com/
bash scripts/test-livekit-meeting.sh
```

---

*آخر مزامنة مع إعداد الإنتاج: 2026-09-26 — Container `mx-livekit` صورة مثبتة `v1.13.7`، TURN 34789/5351، تسجيل Browser→R2.*
