# تشغيل LiveKit لـ Glottical على VPS 187.124.36.228

## الهدف
- `live.glottical.com` هو نطاق LiveKit لمنصة Glottical.
- كل غرف البث وClassroom تعمل عبر LiveKit فقط.

## 1) DNS (Hostinger — dns.hostinger.com)
في لوحة DNS لنطاق `glottical.com` أضف:

| Type | Name | Value | TTL |
|------|------|-------|-----|
| A | live | 187.124.36.228 | 300 |

تحقق:
```bash
nslookup live.glottical.com
```
يجب أن يظهر `187.124.36.228`.

## 2) VPS (SSH إلى 187.124.36.228)
ارفع/انسخ ثم نفّذ:
```bash
sudo bash scripts/setup-live-glottical-livekit.sh
```
السكربت:
- يضيف nginx لـ `live.glottical.com` → `127.0.0.1:7880`
- يصدر شهادة Let's Encrypt
- يضبط مفاتيح LiveKit في `livekit.yaml`

## 3) منصة Glottical
في `.env` (محلياً وعلى الإنتاج):
```
LIVEKIT_URL=wss://live.glottical.com
LIVEKIT_PUBLIC_HOST=live.glottical.com
LIVEKIT_HTTP_URL=http://187.124.36.228:7880
LIVEKIT_API_KEY=your_livekit_api_key
LIVEKIT_API_SECRET=your_livekit_api_secret
```

ثم:
```bash
php artisan config:clear
php artisan livekit:provision-glottical --set-default
```

## 4) تحقق
- `curl -I https://live.glottical.com/` → 200
- `curl http://187.124.36.228:7880/` → `OK`
- غرفة بث معلم/طالب وClassroom تحمّل عميل LiveKit من jsDelivr

## ملاحظات
- LiveKit يعمل حالياً على المنفذ `7880` على الـ VPS.
- بدون سجل DNS + شهادة SSL لن يعمل `wss://live.glottical.com` من المتصفح على HTTPS.
- من لوحة الإدارة → سيرفرات البث: أضف سيرفر LiveKit واضغط «استخدام كنطاق افتراضي».
