# Vue + Inertia + Laravel Starter Kit

Starter kit untuk membangun aplikasi full-stack modern dengan Laravel di backend dan Vue 3 + Inertia di frontend. Tanpa API — routing Laravel langsung render komponen Vue.

## Fitur

- **Inertia v3** — SPA tanpa REST API
- **Vue 3** — Composition API, ref, reactive
- **TypeScript** — type-safe dari ujung ke ujung
- **Laravel 12** — route, controller, middleware tetap server-side
- **Tailwind CSS v4** — utility-first styling

## Tech Stack

- **Backend:** Laravel 12
- **Frontend:** Vue 3 + Inertia v3 + TypeScript
- **Database:** MySQL
- **Build:** Vite
- **UI:** Tailwind CSS v4

## Quick Start

```bash
composer install
npm install
cp .env.example .env
# isi database
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

## Struktur

```
resources/
├── js/
│   ├── components/    # Shared Vue components
│   ├── layouts/       # Layout pages
│   ├── pages/         # Inertia page components
│   ├── types/         # TypeScript definitions
│   └── app.ts         # Entry point
└── views/
    └── app.blade.php  # Root template
```
