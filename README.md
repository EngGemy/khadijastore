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

## Facebook Pixel + Conversions API (متعدد البراندات)

كل **براند (= متجر)** له إعدادات بكسل مستقلة: Pixel ID، Access Token مشفّر، Test Event Code، وتفعيل/تعطيل لكل حدث.

### التثبيت

```bash
composer require facebook/php-business-sdk
php artisan migrate
php artisan queue:work   # مطلوب لإرسال أحداث CAPI دون حظر الطلبات
```

متغيرات `.env` الاختيارية:

```env
FACEBOOK_PIXEL_ENABLED=true
FACEBOOK_GRAPH_API_VERSION=v21.0
FACEBOOK_PIXEL_REQUIRE_CONSENT=false
FACEBOOK_PIXEL_CONSENT_COOKIE=marketing_consent
FACEBOOK_PIXEL_DEFAULT_CURRENCY=EGP
```

### الإعداد لكل براند

1. ادخل لوحة التحكم `/admin` → **الإعدادات → فيسبوك بكسل**
2. أدخل **Pixel ID** و **Access Token** (يُتحقق منهما عبر Graph API قبل الحفظ)
3. فعّل البكسل و CAPI واختر الأحداث المطلوبة
4. للاختبار: أضف **Test Event Code** من Events Manager → Test Events

أو عبر API (للمستخدمين المسجلين):

```
GET  /admin/api/facebook-pixel
PUT  /admin/api/facebook-pixel
POST /admin/api/facebook-pixel/test-token
```

### آلية إزالة التكرار (Deduplication)

Meta تدمج حدث المتصفح وحدث CAPI عندما يتطابق **`event_id`**:

1. الخادم يولّد UUID واحدًا (`Str::uuid()`)
2. **المتصفح**: `fbq('track', 'Purchase', params, { eventID: '<uuid>' })`
3. **CAPI**: نفس `event_id` في حقل `event_id` (عبر Job غير متزامن)

مثال من `OrderService` بعد إتمام الطلب — CAPI يُرسَل من الخادم، والمتصفح يستلم نفس `event_id` في استجابة JSON:

```php
// app/Services/OrderService.php — يُستدعى تلقائيًا بعد place()
$this->facebookPixel->track('Purchase', [
    'content_ids' => [(string) $item->product_id],
    'content_type' => 'product',
    'value' => (float) $order->total,
    'currency' => 'EGP',
    'order_id' => $order->order_no,
], $order->brand_id, ['ph' => $order->customer_phone], queueBrowser: false);
```

```js
// بعد نجاح POST /order
if (j.fb_pixel) {
  fbq('track', j.fb_pixel.event_name, j.fb_pixel.params, { eventID: j.fb_pixel.event_id });
}
```

### حقن الواجهة

```blade
{{-- layouts أو صفحة المنتج --}}
<x-facebook-pixel
    :brand-id="$product->brand_id"
    :page-view-event-id="$fbPageView['event_id'] ?? null"
/>

@push('fb-pixel-events')
@fbPixelEvent('ViewContent', ['content_ids' => ['123'], 'value' => 99])
@endpush
```

### GDPR / الموافقة

عند `FACEBOOK_PIXEL_REQUIRE_CONSENT=true` لا تُرسَل الأحداث إلا إذا وُجدت كوكي الموافقة:

```js
document.cookie = 'marketing_consent=1; path=/; max-age=31536000; SameSite=Lax';
```

### هيكل الملفات

```
app/Models/FacebookPixelSetting.php
app/Services/FacebookPixelService.php
app/Jobs/SendFacebookCapiEvent.php
app/Http/Controllers/Admin/FacebookPixelSettingsController.php
app/Http/Requests/Admin/UpdateFacebookPixelSettingsRequest.php
app/View/Components/FacebookPixel.php
app/Filament/Pages/ManageFacebookPixelSettings.php
config/facebook-pixel.php
database/migrations/2026_06_28_100000_create_facebook_pixel_settings_table.php
resources/views/components/facebook-pixel.blade.php
```

---

## ملاحظات إنتاجية

- في `config/cors.php` ضع نطاق الواجهة الأمامية بدل `*`.
- استخدم MySQL في الإنتاج (SQLite للتطوير فقط).
- فعّل `php artisan optimize` و queue worker للـ media conversions.
