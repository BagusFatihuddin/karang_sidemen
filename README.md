# POKDARWIS — Tourism Management Platform

**Pulau Lombok tourism destination and booking management system.**

- **Backend:** Laravel 11 (PHP)
- **Frontend:** React 19 + Vite (TypeScript ready)
- **Database:** MySQL
- **Stack:** React Query, Axios, React Router v7

---

## 1. Local Setup

### Backend (Laravel)

```bash
# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Create storage link for uploads
php artisan storage:link

# Run migrations (first time only)
php artisan migrate

# Start Laravel dev server (runs on http://localhost:8000)
php artisan serve
```

### Frontend (React + Vite)

```bash
cd pokdarwis-public

# Install dependencies
npm install

# Start dev server (runs on http://localhost:5173)
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview
```

### Environment Configuration

**Frontend** (`pokdarwis-public/.env`):

```
VITE_API_URL=http://127.0.0.1:8000/api/v1
```

The API URL connects to your local Laravel backend. Change port if needed.

---

## 2. Project Structure

### Backend

```
app/
  ├── Http/Controllers/Api/V1/          # Public API endpoints
  ├── Models/                             # Database models
  ├── Services/                           # Business logic (Cloudinary, etc)
  └── Providers/                          # Service registration
routes/
  └── api_v1.php                          # API route definitions
database/
  ├── migrations/                         # Database schema
  └── seeders/                            # Test data
```

### Frontend

```
pokdarwis-public/src/
  ├── pages/                              # Page components (routed)
  │   ├── HomePage.jsx
  │   ├── DestinationsPage.jsx
  │   ├── DestinationDetailPage.jsx
  │   ├── PackagesPage.jsx
  │   ├── ReviewsPage.jsx
  │   ├── ReviewTokenPage.jsx             # Review submission form
  │   └── AboutPage.jsx
  ├── components/                         # Reusable UI components
  ├── layouts/                            # Layout components (wrappers)
  ├── router/                             # React Router configuration
  ├── services/api/                       # API client + request functions
  │   ├── client.js                       # Axios instance
  │   ├── destinations.js                 # GET destinations
  │   ├── reviews.js                      # GET reviews
  │   └── promos.js                       # GET promos
  ├── hooks/                              # React custom hooks
  └── main.jsx                            # Entry point
```

---

## 3. UI/UX Handover Rules

### ✅ YOU CAN CHANGE

- **Styling & Theme** — colors, fonts, spacing
- **Layout & Spacing** — responsive breakpoints, padding, margins
- **Typography** — font sizes, weights, line heights
- **Animations & Transitions** — hover effects, page transitions
- **Component Visual Design** — buttons, cards, forms, dialogs
- **Accessibility** — ARIA labels, keyboard navigation
- **Images & Assets** — icons, illustrations (respecting Cloudinary URLs)

### 🚫 DO NOT CHANGE WITHOUT APPROVAL

- **API Endpoint paths** — `/api/v1/destinations`, `/api/v1/reviews`, etc.
- **API Payload/Response shape** — field names, data structure
- **React Query query keys** — `["destinations"]`, `["reviews"]`, etc.
- **Service functions** — `getDestinations()`, `getReviews()`, etc.
- **Route paths** — `/destinasi`, `/reviews`, `/review/:token`
- **Business logic** — form validation, submission flows
- **Backend behavior** — caching, rate limiting

**Why?** These are contracts between frontend and backend. Breaking them causes integration failure.

---

## 4. API Contract Freeze

### Public API Endpoints

All endpoints are **read-only** for the public website (except review submission).

| Method | Endpoint                    | Purpose                           | Response                                         |
| ------ | --------------------------- | --------------------------------- | ------------------------------------------------ |
| `GET`  | `/api/v1/destinations`      | List all active destinations      | Array of destination objects                     |
| `GET`  | `/api/v1/destinations/{id}` | Get single destination detail     | Single destination with images & daily visits    |
| `GET`  | `/api/v1/reviews`           | List approved reviews (paginated) | Array of reviews, pagination meta                |
| `GET`  | `/api/v1/reviews/pinned`    | Get pinned testimonials (10 max)  | Array of pinned reviews                          |
| `GET`  | `/api/v1/promos`            | List active promotions            | Array of active promos                           |
| `GET`  | `/api/v1/trip-packages`     | List tour packages                | Array of packages with destinations & guides     |
| `GET`  | `/api/v1/guides`            | List active guides                | Array of guides                                  |
| `GET`  | `/api/v1/settings/public`   | Public app settings (whitelist)   | Object: village_name, tagline, social links, etc |
| `POST` | `/api/v1/review/{token}`    | Submit review via token           | Review submission (photo upload to Cloudinary)   |
| `GET`  | `/api/v1/review/{token}`    | Validate review token             | Token data: visitor info, destination            |

**Response structure is FROZEN.** Check actual API for exact field names before using.

---

## 5. Important User Flows

### Flow 1: Browse Destinations

1. **HomePage** → Featured destinations carousel (from `/promos`)
2. **DestinationsPage** → List all with type filtering
3. **DestinationDetailPage** → View details, reviews, booking link

**Involved endpoints:**

- `GET /api/v1/destinations`
- `GET /api/v1/destinations/{id}`
- `GET /api/v1/reviews?destination_id={id}`

### Flow 2: Submit a Review

1. Admin sends unique review token via WhatsApp to visitor
2. Visitor clicks link: `/review/:token`
3. **ReviewTokenPage** validates token and shows form
4. User fills: name, city, rating, review text, photo
5. Form POSTs to `/api/v1/review/:token`
6. Success message shown

**Involved endpoints:**

- `GET /api/v1/review/:token` (validation)
- `POST /api/v1/review/:token` (submission with file upload)

### Flow 3: View Reviews

1. **ReviewsPage** shows paginated approved reviews
2. Filter by rating or destination
3. **HomePage** testimonial section shows pinned reviews

**Involved endpoints:**

- `GET /api/v1/reviews` (with filters & pagination)
- `GET /api/v1/reviews/pinned` (homepage testimonials)

### Flow 4: About Page

Static page with:

- Village name and tagline (from settings)
- Social media links (from settings)
- Maps embed URL (from settings)

**Involved endpoints:**

- `GET /api/v1/settings/public`

---

## 6. Development Rules

### API Calls

**DO:**

```javascript
// ✅ Use existing service functions
import { getDestinations } from "../services/api/destinations";
const { data } = await getDestinations();
```

**DON'T:**

```javascript
// ❌ Do NOT use axios directly
const { data } = await axios.get("/destinations");

// ❌ Do NOT hardcode URLs
fetch("http://127.0.0.1:8000/api/v1/destinations");
```

### Component Logic

**Keep concerns separate:**

- **Pages** — Route-level logic only
- **Components** — UI & presentation only
- **Services** — API communication only
- **Hooks** — State management & side effects

**Example:**

```javascript
// ✅ Good: Service handles API
export const useDestinations = () => {
    return useQuery({
        queryKey: ["destinations"],
        queryFn: getDestinations,
    });
};

// ❌ Bad: Component doing API work
function MyComponent() {
    const [data, setData] = useState();
    useEffect(() => {
        axios.get("/destinations").then(setData);
    }, []);
}
```

---

## 7. Pre-PR Checklist

Before submitting a pull request:

- [ ] **Build passes:** `npm run build` (no errors)
- [ ] **No console errors** in browser devtools
- [ ] **API still works** — test in Network tab
- [ ] **Responsive design** — tested on mobile (DevTools)
- [ ] **No hardcoded URLs** — all API calls use services
- [ ] **Accessibility** — buttons/links are keyboard accessible
- [ ] **No API changes** — endpoint paths unchanged
- [ ] **Query keys unchanged** — React Query still works
- [ ] **Service functions unchanged** — no signature changes

---

## 8. Useful Commands

```bash
# Frontend dev
cd pokdarwis-public
npm run dev          # Start dev server
npm run build        # Production build
npm run lint         # ESLint check

# Backend dev
php artisan serve    # Start Laravel
php artisan migrate  # Run migrations
php artisan tinker   # PHP REPL

# Git workflow
git branch           # List branches
git checkout feature/ui-redesign   # Switch to UI branch
git pull origin      # Get latest
git diff             # See changes
```

---

## 9. File Structure at a Glance

For UI changes, you'll mostly work in:

```
pokdarwis-public/src/
  ├── pages/           ← Page styling/layout
  ├── components/      ← New/modify UI components
  ├── layouts/         ← Header/footer/wrapper styling
  └── assets/          ← Images, fonts, etc
```

**Don't touch these unless told:**

```
pokdarwis-public/src/
  ├── services/        ← API layer (frozen)
  ├── hooks/           ← Data fetching (frozen)
  ├── router/          ← Routes (frozen)
  └── main.jsx         ← App setup (frozen)
```

---

## 10. Questions?

- **API not responding?** Check that backend is running: `php artisan serve`
- **Build fails?** Clear node_modules: `rm -rf node_modules && npm install`
- **Need to modify an endpoint?** Ask the backend team (don't change without sync)
- **Styling not applying?** Check browser cache and hard refresh (Ctrl+Shift+R)

---

**Branch:** `feature/ui-redesign`  
**Last updated:** 2026-06-09
