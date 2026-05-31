# دمج التصميم داخل Laravel (Blade)

تم تحويل التصميم (Tailwind) إلى Blade views داخل المشروع — **بدون تغيير الشكل أو الحركة**.
الواجهة الآن تقرأ البيانات الحقيقية من قاعدة البيانات مباشرة، ولوحة التحكم منفصلة على `/admin`.

## ما تم عمله
- `resources/views/layouts/app.blade.php` — الـ head المشترك + Tailwind config + حقن الثيم (المناسبات) server-side.
- `resources/views/shop/index.blade.php` — الرئيسية (براندات + منتجات featured من DB).
- `resources/views/shop/brand.blade.php` — صفحة البراند (منتجاتها + واتساب الخاص بها).
- `resources/views/shop/product.blade.php` — صفحة المنتج (variants حقيقية + فورم يبعت للـ backend).
- `resources/views/partials/` — strip (نص المناسبة) + footer.
- `app/Http/Controllers/ShopController.php` — يخدم الصفحات + يحقن الثيم.
- `routes/web.php` — `/`, `/brand/{slug}`, `/product/{slug}`, `POST /order`.

## الفورم والطلبات
فورم صفحة المنتج يبعت لـ `POST /order` (الـ OrderController الموجود):
- **COD**: يسجّل الطلب ويعرض رقمه.
- **واتساب**: يسجّل الطلب + يفتح واتساب البراند برسالة جاهزة.
- **تحويل**: يرفع صورة الإيصال فعليًا + يسجّل الطلب + يفتح واتساب.
كله يظهر في لوحة Filament تحت "الطلبات"، ويُسجّل في الـ Audit Log.

## التشغيل
```powershell
php artisan migrate:fresh --seed
php artisan serve
```
- الواجهة: `http://127.0.0.1:8000/`
- اللوحة: `http://127.0.0.1:8000/admin` (super@alamat.test / password)

## الثيمات (العيد/المناسبات)
من اللوحة: فعّل ثيم "العيد" → الواجهة تتغيّر ألوانها ونص الشريط تلقائيًا server-side،
دون لمس أي ملف. الجدولة (starts_at/ends_at) تشتغل تلقائيًا.

## ملاحظة مهمة
امسح الكاش بعد النسخ:
```powershell
php artisan optimize:clear
php artisan view:clear
```
