# متجر العلامات — Backend

backend متعدد البراندات بـ **Laravel 12 + Filament 4**، يخدم الواجهة الأمامية (Tailwind) الموجودة عبر API دون أي تعديل فيها.

---

## التشغيل من الصفر

```bash
# 1) أنشئ مشروع Laravel 12 وانسخ هذه الملفات فوقه
composer create-project laravel/laravel:^12 alamat-shop
cd alamat-shop
# انسخ محتويات هذا المجلد (app/, database/, routes/, config/, bootstrap/, composer.json)

# 2) ثبّت الحزم
composer require filament/filament:"^4.0" \
  spatie/laravel-permission:"^6.0" \
  owen-it/laravel-auditing:"^14.0" \
  spatie/laravel-medialibrary:"^11.0"

# 3) انشر إعدادات الحزم
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="OwenIt\Auditing\AuditingServiceProvider" --tag="config"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"

# 4) الإعداد
cp .env.example .env
php artisan key:generate
php artisan storage:link

# 5) قاعدة البيانات + البيانات التجريبية
php artisan migrate
php artisan db:seed

# 6) التشغيل
php artisan serve
```

اللوحة على: `http://localhost:8000/admin`

---

## حسابات الدخول التجريبية

| الدور | البريد | كلمة المرور | النطاق |
|------|--------|------------|--------|
| سوبر أدمن | `super@alamat.test` | `password` | كل البراندات + الإجماليات + الـ audit |
| أدمن براند | `admin-1@alamat.test` | `password` | براند العناية فقط |
| موظف براند | `staff-1@alamat.test` | `password` | طلبات براند العناية فقط |

(الأرقام 1/2/3 لكل براند)

---

## الصلاحيات

- **super_admin**: يرى الإجمالي وكل الفلوس وكل الحجوزات عبر البراندات، يدير المستخدمين/البراندات/الثيمات، ويطّلع على الـ audit log المركزي.
- **brand_admin**: يدير منتجات/طلبات/مستخدمي **برانده فقط** (عزل تلقائي بالـ `BrandScope`).
- **brand_staff**: يتعامل مع طلبات برانده فقط (تحديث الحالة)، دون إدارة.

كل براند له بياناته المنفصلة: واتساب، فودافون كاش، إنستاباي، ومواعيد عمل خاصة.

---

## الثيمات والمناسبات

من اللوحة (السوبر أدمن): **الثيمات والمناسبات** → ثيم العيد/رمضان/الجمعة البيضاء.
- كل ثيم له ألوان (تُحقن كـ CSS variables)، أولوية، ونافذة زمنية (`starts_at`/`ends_at`).
- ثيم العيد يُفعّل ويُطفأ **تلقائيًا** في تواريخه — أضف للـ scheduler:

```php
// routes/console.php أو app/Console/Kernel (Laravel 12: bootstrap/app)
Schedule::command('themes:refresh')->hourly();
```

الواجهة الأمامية لا تتغير: تستدعي `GET /api/v1/theme` وتحقن `data.css` في `<style>`.

---

## الـ Audit Log

تلقائي عبر `owen-it/laravel-auditing` على: Brand, Product, Order, User, Theme, Category, Variant.
يسجّل: **من** غيّر، **ماذا** تغيّر (قديم/جديد)، **متى**، الـ IP والرابط.
يُعرض في اللوحة (السوبر أدمن فقط) تحت **سجل التغييرات**.

---

## تكامل الواجهة الأمامية (دون تعديلها جوهريًا)

الواجهة الحالية تستهلك هذه الـ endpoints:

```
GET  /api/v1/brands                      قائمة البراندات
GET  /api/v1/products                    المنتجات المميّزة (الرئيسية)
GET  /api/v1/brands/{slug}/products      منتجات براند
GET  /api/v1/products/{slug}             تفاصيل منتج
GET  /api/v1/governorates                المحافظات + الشحن
GET  /api/v1/theme                       الثيم الفعّال (CSS vars)
POST /api/v1/orders                      إنشاء طلب (+ رفع إيصال التحويل)
```

مثال إرسال طلب من صفحة المنتج (يستبدل دوال الـ COD/التحويل الحالية):

```js
async function placeOrder(payload, receiptFile) {
  const fd = new FormData();
  Object.entries(payload).forEach(([k, v]) => fd.append(k, v));
  if (receiptFile) fd.append('receipt', receiptFile);

  const res = await fetch('https://api.example.com/api/v1/orders', {
    method: 'POST', body: fd, headers: { Accept: 'application/json' },
  });
  const json = await res.json();
  if (json.success && json.data.whatsapp_url) {
    window.open(json.data.whatsapp_url, '_blank'); // لإرسال الإيصال
  }
  return json;
}
```

حقن الثيم عند تحميل الصفحة:

```js
fetch('/api/v1/theme').then(r => r.json()).then(({ data }) => {
  const s = document.createElement('style');
  s.textContent = data.css;       // :root{--accent:...;--ink:...}
  document.head.appendChild(s);
});
```

---

## ملاحظات إنتاجية

- في `config/cors.php` ضع نطاق الواجهة الأمامية بدل `*`.
- استخدم MySQL في الإنتاج (SQLite للتطوير فقط).
- فعّل `php artisan optimize` و queue worker للـ media conversions.
