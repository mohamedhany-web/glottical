# أثير — تصميمات HTML / CSS / Tailwind (بدون Node)

منصة تصميمات ثابتة بالكامل. لا تحتاج `npm` أو `node_modules`.

## التشغيل (XAMPP)

1. شغّل Apache في XAMPP
2. افتح المتصفح على:

**القاعدة:** `http://localhost/e-commerce%20designs/site/`

| الصفحة | الرابط |
|---|---|
| الرئيسية | http://localhost/e-commerce%20designs/site/index.html |
| المنتج | http://localhost/e-commerce%20designs/site/product.html |
| السلة | http://localhost/e-commerce%20designs/site/cart.html |
| الدفع | http://localhost/e-commerce%20designs/site/checkout.html |
| نجاح الدفع | http://localhost/e-commerce%20designs/site/checkout-success.html |
| فشل الدفع | http://localhost/e-commerce%20designs/site/checkout-failure.html |
| الحساب | http://localhost/e-commerce%20designs/site/account.html |
| التصنيفات | http://localhost/e-commerce%20designs/site/categories.html |
| المجموعات | http://localhost/e-commerce%20designs/site/collections.html |
| العلامات | http://localhost/e-commerce%20designs/site/brands.html |
| العروض | http://localhost/e-commerce%20designs/site/offers.html |
| وصل حديثاً | http://localhost/e-commerce%20designs/site/new-arrivals.html |
| مساعد أثير | http://localhost/e-commerce%20designs/site/assistant.html |
| المقارنة | http://localhost/e-commerce%20designs/site/compare.html |
| المساعدة | http://localhost/e-commerce%20designs/site/help.html |
| من نحن | http://localhost/e-commerce%20designs/site/about.html |
| لوحة الإدارة | http://localhost/e-commerce%20designs/site/admin/index.html |

## صفحات الإدارة

| الصفحة | الرابط |
|---|---|
| لوحة التحكم | http://localhost/e-commerce%20designs/site/admin/index.html |
| الطلبات | http://localhost/e-commerce%20designs/site/admin/orders.html |
| المنتجات | http://localhost/e-commerce%20designs/site/admin/products.html |
| التصنيفات | http://localhost/e-commerce%20designs/site/admin/categories.html |
| المخزون | http://localhost/e-commerce%20designs/site/admin/inventory.html |
| القسائم | http://localhost/e-commerce%20designs/site/admin/coupons.html |
| العملاء | http://localhost/e-commerce%20designs/site/admin/customers.html |
| التقارير | http://localhost/e-commerce%20designs/site/admin/reports.html |
| الإعدادات | http://localhost/e-commerce%20designs/site/admin/settings.html |

## الحركة والانتقالات

- دخول الصفحات: `page-enter`
- مغادرة الصفحات: `page-leave` (160ms)
- رفع البطاقات: `card-lift`
- ضغط الأزرار: `btn-press`
- ظهور متدرج: `fade-up`
- سكرول السايدبار مخفي: `scrollbar-none`

## السلة (localStorage)

- المفتاح: `atheer-cart-v1`
- الواجهة: `window.AtheerCart` (read / write / add / count / clear)
- أزرار الإضافة: `[data-add-to-cart]`

## الملفات التقنية

| الملف | الوظيفة |
|---|---|
| `assets/css/atheer.css` | الخط، الخلفيات، الحركات، أنماط الإدارة |
| `assets/js/tailwind-config.js` | ألوان وظلال أثير في Tailwind CDN |
| `assets/js/storefront.js` | الهيدر/الفوتر، السلة، التنقل، الانتقالات |
| `assets/js/admin-shell.js` | قشرة الإدارة، السايدبار، التوببار، الدرج |
| `assets/js/admin-charts.js` | مخطط المبيعات 14 يوماً + أعمدة الطلبات بالساعة |

## الهوية البصرية

- الخلفية: `#f3f5f7` · السطح: `#fff` · الحبر: `#0b1220`
- اللون الأساسي: `#0f5c57` · المعدني: `#b08d57`
- الخط: IBM Plex Sans Arabic · الاتجاه: RTL عربي أولاً
