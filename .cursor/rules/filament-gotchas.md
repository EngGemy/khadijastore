# Filament & Laravel Gotchas (alamat-shop)

Lessons from production errors — **read before editing Filament admin code**.

---

## 1. `SpatieMediaLibraryFileUpload::record()` does NOT exist

**Error:** `BadMethodCallException: Method Filament\Forms\Components\SpatieMediaLibraryFileUpload::record does not exist`

**Wrong:**
```php
SpatieMediaLibraryFileUpload::make('logo')
    ->record(fn () => $this->brand)  // ❌ Form fields have no record() method
```

**Correct:** Bind the Eloquent model on a **Schema container** (`Section`, `Schema`), not on the upload field:

```php
Section::make('لوجو البراند')
    ->model(fn (): ?Brand => $this->brand)  // ✅ BelongsToModel on Section/Schema
    ->schema([
        SpatieMediaLibraryFileUpload::make('logo')
            ->collection('logo')
            ->image(),
    ]),
```

**Why:** `record()` / `model()` live on `Filament\Schemas\Concerns\BelongsToModel` (Section, Schema). Form field components inherit `getRecord()` from the parent container.

**On custom Filament Pages (not EditRecord):**
- After `$this->form->fill(...)`, call `$this->form->loadStateFromRelationships()` so existing media shows.
- `$this->form->getState()` triggers `saveRelationships()` — logo saves on form submit.

**Safest alternative for complex pages:** Use `BrandResource` / `EditRecord` for logo uploads (works out of the box).

---

## 2. Super admin on `/platform/` has `brand_id = null`

**Symptom:** «الهوية والصفحة» tab hidden — no logo/name/whatsapp fields on `ManageBrandSettings`.

**Fix:** Brand selector for super admin + load/save using `$this->editingBrandId`, not `auth()->user()->brand_id`.

**Direct link:** `/platform/manage-brand-settings?brand=attar`

**Brand merchants** use `/merchant/manage-brand-settings` — brand loads automatically.

---

## 3. `brands` table has NO `sort` column

**Error:** `Unknown column 'sort' in ORDER BY` on brands query.

**Fix:** Use `orderBy('name')` for brands. Do not assume `sort` exists on every table.

---

## 4. Two different WhatsApp fields

| Field | Where | Used on storefront |
|-------|--------|-------------------|
| `brands.whatsapp` | ManageBrandSettings → الهوية | Hero «واتساب» button on `/brand/{slug}` |
| `store.support_whatsapp` | ManageBrandSettings → التواصل | Support / checkout context |

Do not conflate them in forms or docs.

---

## 5. Blade `@if` inside `<x-filament::button>`

**Error:** `ParseError: unexpected token "endif"` in compiled views.

**Fix:** Wrap the entire button component with `@if` / `@else`, never put `@if` inside the component tag body.

---

## 6. Stale compiled views on deploy

After Blade changes, run on server:
```bash
php artisan view:clear
php artisan view:cache
```

Deploy workflow should run `view:clear` before `view:cache`.

---

## 7. Homepage / nav cache keys

When brands or listings change, clear:
- `home.blocks.resolved.v3`
- `nav.directory.counts`
- `nav.brands`

Use `forget_home_blocks_cache()` helper.
