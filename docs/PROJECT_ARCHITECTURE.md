# 📊 POKDARWIS Project Architecture Analysis

## 🏗️ High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    POKDARWIS PROJECT                            │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────────┬──────────────────────────┬──────────────┐
│   Laravel Backend        │   Filament Admin Panel   │ Vue Frontend │
│   (Main Application)     │   (CMS/Management)       │ (Separate)   │
└──────────────────────────┴──────────────────────────┴──────────────┘
         ↓                          ↓                        ↓
    API Routes           Admin Routes (/admin)      pokdarwis-public/
  api_v1.php             Pages, Resources            (Separate SPA)
```

## 📁 Project Structure

### **Backend Core** (`app/`)
```
app/
├── Console/            - Command handling
├── Exports/            - Excel/PDF exports (VisitReport, etc)
├── Filament/
│   ├── Admin/
│   │   ├── Pages/
│   │   │   ├── Settings/     ⭐ Admin settings pages
│   │   │   │   ├── BaseSettingsPage.php
│   │   │   │   ├── BrandSettingsPage.php (LOGO SETTINGS)
│   │   │   │   └── ...
│   │   │   └── ...
│   │   ├── Components/       ⭐ Custom Filament components
│   │   │   └── BrandLogoPreview.php (NEW)
│   │   ├── Resources/        - CRUD resources (Destinations, Bookings, etc)
│   │   │   ├── Bookings/
│   │   │   ├── Visitors/
│   │   │   ├── Guides/
│   │   │   └── ...
│   │   └── Widgets/         - Dashboard widgets
│   └── Providers/
│       └── AdminPanelProvider.php
├── Http/
│   ├── Controllers/     - API controllers
│   ├── Middleware/      - Auth, CORS, etc
│   ├── Resources/       - Filament resources (separate from CRUD)
│   └── Responses/
├── Models/
│   ├── Setting.php         - Global settings (key-value)
│   ├── User.php
│   ├── Visitor.php
│   ├── Booking.php
│   ├── Destination.php
│   ├── TripPackage.php
│   ├── Guide.php
│   ├── Review.php
│   ├── DailyVisit.php
│   ├── Promo.php           - Events/Promotions
│   └── ... (others)
├── Observers/          - Model event listeners
├── Services/
│   └── CloudinaryService.php  - Image upload to CDN
├── Support/
│   └── AppSettings.php  - Settings management helper
└── helpers.php          - Shared helper functions
```

### **Frontend Separate** (`pokdarwis-public/`)
- Standalone Vue.js SPA
- Separate package.json, vite.config.js
- Communicates via API (api_v1.php)
- Independent deployment

### **Database** (`database/`)
```
database/
├── migrations/   - Schema definitions
├── factories/    - Test data generators
└── seeders/      - Initial data population
```

### **Public Assets** (`public/`)
- Static files, CSS, JS
- Fonts and images
- Storage symlink

### **Views** (`resources/`)
```
resources/
├── views/
│   └── filament/admin/
│       ├── components/
│       │   └── brand-logo-preview.blade.php (NEW)
│       ├── pages/
│       └── resources/
├── css/
└── js/
```

## 🎯 Core Components & Their Roles

### **1. Data Models** (Database Layer)
```
Setting          → Global app settings (key-value pairs)
User             → Admin users
Visitor          → Tourist/guest records
Booking          → Tour bookings
Destination      → Tour locations
TripPackage      → Tour packages
Guide            → Local guides
Review           → Guest reviews
DailyVisit       → Daily visitor statistics
Promo (Event)    → Promotions/events
```

### **2. Admin Panel** (Filament)
**Purpose:** Content management & operations for admins
**Key Features:**
- Dashboard (widgets, stats)
- Content Management (Destinations, Packages, Guides)
- Visitor Management (Tourists, Reviews, Bookings)
- Operational Data (Daily visits, WA Blast)
- Reports & Analytics
- Settings (Brand, Logo, Website config)

**Key Files:**
- `AdminPanelProvider.php` - Panel configuration
- `BaseSettingsPage.php` - Abstract settings page
- `BrandSettingsPage.php` - Logo & brand settings

### **3. Settings Management**
**Architecture:**
```
Database (Setting model)  ← key-value storage
      ↓
AppSettings::all()      ← Load all settings
      ↓
BaseSettingsPage        ← Display form
      ↓
User Edit Form          ← Update values
      ↓
AppSettings::set()      ← Save to DB
      ↓
AppSettings::clearCache() ← Invalidate cache
```

**Used Settings:**
- `brand_logo_url` - Logo URL (Cloudinary)
- `brand_logo_alt` - Logo alt text
- `brand_mark_text` - Fallback text

### **4. Image Management**
**Flow:**
```
User Upload File
      ↓
Handle In BaseSettingsPage::handleSingleImageUpload()
      ↓
CloudinaryService::upload()  ← Upload to Cloudinary CDN
      ↓
Get CDN URL
      ↓
Save URL to Setting model
```

**Service:** CloudinaryService.php

## 🔌 API Structure

### **Routes**
- `routes/web.php` - Admin & public routes
- `routes/api_v1.php` - API endpoints (for Vue frontend)
- `routes/console.php` - Artisan commands

### **API Endpoints** (Typical)
```
GET    /api/v1/destinations
POST   /api/v1/bookings
GET    /api/v1/guides
GET    /api/v1/settings (public)
```

## 🔐 User & Roles

**Role System:**
```
AppSupport/UserRole:
├── SUPER_ADMIN       - Full access
├── ADMIN_KONTEN      - Content management
└── ... (others)
```

**Permissions:** Managed via Filament authorization

## 📦 Key Dependencies

From `composer.json`:
- **Laravel** 13.12.0
- **Filament** v5.6+ (Admin panel)
- **Livewire** - Real-time component updates
- **Maatwebsite/Laravel-Excel** - Export functionality
- **Cloudinary** - Image CDN
- **DOMPDF** - PDF generation
- **Spatie packages** - Various utilities

From `package.json` (pokdarwis-public):
- **Vue.js** 3
- **Vite** - Bundler
- **Tailwind CSS** - Styling

## 🚀 Development Workflow

### **Admin Side** (Filament)
1. Define Model (e.g., Destination)
2. Create Resource (DestinationResource)
3. Create Form & Table schemas
4. Register in AdminPanelProvider
5. Admin accesses via `/admin`

### **Frontend Side** (Vue)
1. Fetch data from API
2. Display in Vue components
3. Send updates back to API
4. Separate deployment process

## 🛠️ Common Tasks

### Add New Content Type
```
Model → Migration → Observer → Resource → Register
```

### Add New Settings
```
1. Schema in SettingsPage
2. Add key to settingKeys()
3. Define beforeSave() if needed
4. Access via AppSettings::get('key_name')
```

### Upload Images
```
Use CloudinaryService::upload($file, $folder)
Store URL in database
```

## 📊 Database Insights

**Key Tables:**
- `users` - Admin users
- `settings` - App config (key-value)
- `visitors` - Tourist profiles
- `bookings` - Tour reservations
- `destinations` - Locations
- `trip_packages` - Tour packages
- `reviews` - Guest reviews
- `daily_visits` - Statistics

## 🔄 Data Flow Example: Brand Logo Update

```
1. User navigates to /admin/settings/brand
   ↓
2. BrandSettingsPage::mount() loads data
   - AppSettings::all()
   - Fill form with current brand_logo_url
   ↓
3. Schema renders:
   - BrandLogoPreview component shows current logo
   - Upload field for new logo
   ↓
4. User uploads new logo
   ↓
5. beforeSave() hook:
   - CloudinaryService::upload() → uploads to CDN
   - Returns URL
   ↓
6. save() method:
   - AppSettings::set('brand_logo_url', $url)
   - AppSettings::clearCache()
   ↓
7. Next page load:
   - Preview shows new logo
```

## 🎨 UI/UX Stack

**Admin Panel:**
- Filament components (Tables, Forms)
- Tailwind CSS styling
- Dark mode support
- Custom blade components

**Frontend:**
- Vue.js with custom components
- Tailwind CSS
- Responsive design

## 🌐 Deployment Structure

**Single Instance Deployment:**
```
Server:
├── Laravel API Backend    (localhost:8000)
├── Filament Admin Panel   (/admin route)
└── Vue Frontend           (localhost:3000 - dev, or separate build)
```

**Can be deployed as:**
- Monolith (one server for all)
- Separate (backend API + frontend SPA)
- Microservices (future scaling)

## 📋 Project Statistics

- **Models:** 12+ 
- **Resources:** 10+
- **Settings Pages:** Multiple
- **Widgets:** 3+
- **Routes:** 100+ (estimated)
- **Frontend Components:** Depends on pokdarwis-public

## ✨ Notable Features

✅ Real-time admin updates (Livewire)
✅ Media management (Cloudinary CDN)
✅ Report generation (Excel, PDF)
✅ Role-based access control
✅ Modern admin UI (Filament)
✅ API-first architecture
✅ Separate frontend (Vue SPA)
✅ Automatic dark mode
✅ Responsive design

## 🚨 Key Considerations

1. **Media Storage:** Use Cloudinary, not local storage for prod
2. **API Security:** Implement proper auth for API endpoints
3. **Database:** Indexed queries for large datasets
4. **Cache:** Use Redis for settings cache
5. **Frontend:** Separate deployment pipeline for Vue app
6. **Scaling:** Already structured for horizontal scaling

---

**Generated:** 2026-06-16
**For:** POKDARWIS Project Analysis
