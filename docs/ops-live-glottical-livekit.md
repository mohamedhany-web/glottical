# تشغيل LiveKit لـ Glottical على VPS 187.124.36.228

## الهدف
- `live.muallimx.com` يبقى لـ Jitsi / Muallimx دون تغيير.
- `live.glottical.com` يصبح نطاق LiveKit لمنصة Glottical على **نفس** الـ VPS.

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
- يضيف مفتاح Glottical إلى `livekit.yaml` **بدون حذف** مفاتيح Muallimx
- لا يلمس إعدادات `live.muallimx.com`

## 3) منصة Glottical
في `.env` (محلياً وعلى الإنتاج):
```
LIVE_PROVIDER=livekit
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
- غرفة بث معلم/طالب تحمّل LiveKit (وليس `external_api.js` من Jitsi)

## ملاحظات
- LiveKit يعمل حالياً على المنفذ `7880` على الـ VPS (تم التحقق).
- بدون سجل DNS + شهادة SSL لن يعمل `wss://live.glottical.com` من المتصفح على HTTPS.
- إن احتجت تنفيذ السكربت من هنا، أرسل مستخدم/كلمة مرور SSH أو مفتاحاً خاصاً للـ VPS.
