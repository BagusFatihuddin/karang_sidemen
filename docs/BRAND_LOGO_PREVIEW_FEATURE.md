# Brand & Logo Preview Feature

## 📋 Overview
Menambahkan fitur preview logo yang sedang aktif di halaman Brand & Logo Settings untuk memudahkan user melihat logo saat ini sebelum mengganti dengan yang baru.

## ✨ Fitur yang Ditambahkan

### 1. **Custom Component: BrandLogoPreview**
- **File:** [app/Filament/Admin/Components/BrandLogoPreview.php](../app/Filament/Admin/Components/BrandLogoPreview.php)
- **Extends:** `Filament\Schemas\Components\Component`
- **Fungsi:** Menampilkan preview logo dengan cara yang fleksibel

**Methods:**
- `logoUrl(?string $url)` - Set URL logo untuk ditampilkan
- `logoAlt(?string $alt)` - Set text deskripsi logo

### 2. **Blade View: brand-logo-preview.blade.php**
- **File:** [resources/views/filament/admin/components/brand-logo-preview.blade.php](../resources/views/filament/admin/components/brand-logo-preview.blade.php)
- **Fitur:**
  - ✅ Menampilkan preview logo dengan styling modern (rounded border, gradient background)
  - ✅ Responsive design - logo scaled appropriately
  - ✅ Dark mode support dengan proper contrast
  - ✅ Warning message dengan icon jika belum ada logo
  - ✅ Menampilkan deskripsi logo jika ada

**Styling:**
- Max height: 192px (max-h-48)
- Max width: 320px (max-w-xs)
- Object contain untuk maintain aspect ratio
- Background putih untuk kontras dengan logo

### 3. **Integration di BrandSettingsPage**
- **File:** [app/Filament/Admin/Pages/Settings/BrandSettingsPage.php](../app/Filament/Admin/Pages/Settings/BrandSettingsPage.php)
- **Perubahan:**
  - Import `BrandLogoPreview` component
  - Tambah preview component di schema section "📌 Logo Situs"
  - Preview ditampilkan **sebelum** field deskripsi logo

**Flow:**
```
[Logo Section Header]
    ↓
[Preview Component] ← NEW! Shows current logo or warning message
    ↓
[Deskripsi Logo Input]
    ↓
[Upload Logo Baru]
```

## 📊 Struktur Project Analysis

### Architecture Overview
```
Laravel Project (Filament Admin Panel)
├── Backend (Laravel)
│   ├── Models/
│   │   ├── Setting.php (key-value storage)
│   │   └── ... (other models)
│   ├── Filament/
│   │   ├── Admin/
│   │   │   ├── Pages/
│   │   │   │   └── Settings/
│   │   │   │       ├── BaseSettingsPage.php (abstract base)
│   │   │   │       ├── BrandSettingsPage.php
│   │   │   │       └── ... (other settings pages)
│   │   │   ├── Components/
│   │   │   │   └── BrandLogoPreview.php (NEW)
│   │   │   ├── Resources/
│   │   │   └── Widgets/
│   │   └── Providers/
│   │       └── AdminPanelProvider.php
│   ├── Services/
│   │   └── CloudinaryService.php (image upload to CDN)
│   └── Support/
│       └── AppSettings.php (settings helper)
│
├── Frontend (Vue.js - pokdarwis-public/)
│   └── ... (separate SPA application)
│
└── Views (Blade Templates)
    └── filament/admin/components/
        └── brand-logo-preview.blade.php (NEW)
```

### Key Technologies
- **Framework:** Laravel 13.12.0 + Filament v5.6
- **PHP Version:** 8.4.20
- **Database:** Settings model untuk key-value storage
- **Image Storage:** Cloudinary CDN (via CloudinaryService)
- **Frontend Component:** Vue.js (separate pokdarwis-public folder)

## 🔄 How It Works

### Data Flow
1. **Load:** BaseSettingsPage::mount() → AppSettings::all() → Fill form dengan logo URL
2. **Display:** BrandLogoPreview component menerima logoUrl & logoAlt
3. **Render:** Blade view menampilkan preview atau warning message
4. **Save:** Filament auto-save → handleSingleImageUpload() → Upload ke Cloudinary

### Component Lifecycle
```
Schema Definition
  ↓
BrandLogoPreview::make()
  .logoUrl($this->data['brand_logo_url'] ?? null)
  .logoAlt($this->data['brand_logo_alt'] ?? null)
  ↓
Blade Rendering
  @if($logoUrl)
    [Show logo preview]
  @else
    [Show warning message]
  @endif
```

## 📝 Settings Keys Used
- `brand_logo_url` - URL logo (stored in Cloudinary)
- `brand_logo_alt` - Deskripsi/alt text untuk accessibility
- `brand_mark_text` - Fallback text jika logo tidak ada (contoh: "BS" = Brand Sidemen)

## 🚀 Installation & Usage

### Already Implemented
✅ File created & imported
✅ Component registered in schema
✅ View file created

### Testing
1. Navigate ke: http://127.0.0.1:8000/admin/settings/brand
2. Lihat warning message (jika logo belum ada)
3. Upload logo baru
4. Refresh page → preview seharusnya menampilkan logo

## 🔧 Customization Guide

### Mengubah Preview Size
Edit [brand-logo-preview.blade.php](../resources/views/filament/admin/components/brand-logo-preview.blade.php):
```blade
<!-- Ubah max-h-48 dan max-w-xs -->
<img class="h-auto max-h-64 max-w-md object-contain" />
```

### Mengubah Warning Message
Edit blade view, ubah text dalam `dark:text-amber-200`

### Menambah Info Tambahan
Extend BrandLogoPreview component dengan method baru:
```php
public function showSize(bool $show = true): static
{
    $this->viewData(['showSize' => $show]);
    return $this;
}
```

## ✅ Checklist Fitur
- [x] Preview component dibuat
- [x] Blade view dengan responsive design
- [x] Integration ke BrandSettingsPage
- [x] Dark mode support
- [x] Warning message untuk kondisi no logo
- [x] Accessibility (alt text, proper semantic HTML)
- [x] Auto-clear cache after save

## 📚 Related Files
- [BaseSettingsPage.php](../app/Filament/Admin/Pages/Settings/BaseSettingsPage.php) - Base class untuk semua settings pages
- [AppSettings.php](../app/Support/AppSettings.php) - Helper untuk manage settings
- [CloudinaryService.php](../app/Services/CloudinaryService.php) - Image upload service
- [Setting.php](../app/Models/Setting.php) - Database model

## 🎯 Future Improvements
- [ ] Crop/resize preview di form sebelum upload
- [ ] Show file size & dimensions
- [ ] Drag-drop multiple images untuk different placements (navbar, footer, etc)
- [ ] Preview how logo looks in navbar & footer
- [ ] Logo history/version management

---
**Last Updated:** 2026-06-16
**Component Version:** 1.0
