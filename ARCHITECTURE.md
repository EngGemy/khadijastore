# متجر العلامات — Backend (Laravel 12 + Filament 4)

معمارية متعددة البراندات (multi-brand) بقاعدة بيانات واحدة وعزل بالـ tenant scoping.

## الحزم
- laravel/framework ^12
- filament/filament ^4
- spatie/laravel-permission ^6  (الأدوار والصلاحيات)
- owen-it/laravel-auditing ^14  (Audit Log التلقائي)
- spatie/laravel-medialibrary ^11 (صور المنتجات والإيصالات)

## نموذج الصلاحيات (Roles)
| الدور | النطاق | الصلاحيات |
|------|--------|-----------|
| `super_admin` | كل البراندات | يرى الإجمالي، كل الفلوس، كل الحجوزات، كل الـ audit log، يدير المستخدمين والبراندات والثيمات |
| `brand_admin` | براند واحد | يدير منتجات/طلبات/تقارير برانده فقط، يدير مستخدمي برانده |
| `brand_staff` | براند واحد | يتعامل مع الطلبات فقط (عرض/تحديث حالة) دون إدارة |

## العزل (Tenant Isolation)
- كل موديل tenant عليه `brand_id`.
- `BelongsToBrand` trait يضيف Global Scope: المستخدم غير السوبر أدمن يرى بيانات `brand_id` الخاص به فقط.
- السوبر أدمن: الـ scope معطّل، يرى الكل + فلتر اختياري بالبراند.
- عند الإنشاء، `brand_id` يُملأ تلقائيًا من المستخدم الحالي.

## الجداول الأساسية
- brands (البراند: اسم، slug، لوجو، واتساب، فودافون، إنستاباي، ساعات العمل/المواعيد، نشط)
- users (+ brand_id nullable للسوبر أدمن)
- categories (brand_id, name, slug)
- products (brand_id, category_id, name, slug, desc, price, compare_price, active, badge)
- product_variants (product_id, name, price, qty_gift)
- orders (brand_id, order_no, customer..., governorate, address, payment_method[cod|whatsapp|transfer], status, subtotal, shipping, total, receipt_path)
- order_items (order_id, product_id, variant_id, name, price, qty)
- governorates (name, shipping_fee) — مرجعي مشترك
- themes (key, name, is_active, scope[global|brand], brand_id, tokens[json], starts_at, ends_at) — ثيمات المناسبات
- settings (brand_id nullable, key, value) — إعدادات عامة وللبراند
- audits (تلقائي من laravel-auditing)

## الثيمات والمناسبات (Custom Themes)
- جدول themes يخزّن "tokens" (ألوان/خط/شعار) كـ JSON.
- ثيم يمكن جدولته بـ starts_at/ends_at (مثلاً ثيم العيد يُفعّل تلقائيًا في تواريخه).
- ThemeResolver service: يختار الثيم النشط (براند > عام > افتراضي) ويحقن CSS variables في الواجهة دون تغيير HTML الأمامي.
- الواجهة الأمامية (الـ Tailwind الحالي) تبقى كما هي؛ الثيم يُحقن عبر `<style>` متغيرات CSS فقط.

## Audit Log
- owen-it/laravel-auditing على Brand, Product, Order, User, Theme...
- يسجّل: من غيّر، ماذا تغيّر (قديم/جديد)، متى، IP، URL.
- مورد Filament للعرض (للسوبر أدمن فقط) مع فلاتر.

## لوحات Filament
- لوحة واحدة `/admin` بصلاحيات حسب الدور (أبسط وأسهل صيانة من لوحتين).
- البراند يرى موارده فقط (بالـ scope)؛ السوبر أدمن يرى الكل + Dashboard بإجماليات.
