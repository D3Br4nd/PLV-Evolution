# Pro Loco Venticanese Evolution

PWA per la gestione di eventi e tesseramenti della Pro Loco Venticanese.

## 🚀 Stack Tecnologico

### Backend
- **PHP**: 8.4
- **Framework**: Laravel 12.44
- **Server**: FrankenPHP (Caddy + PHP)
- **Database**: PostgreSQL 18.1
- **Cache/Queue**: Redis 8.4.0
- **WebSockets**: Laravel Reverb

### Frontend
- **Framework**: Svelte 5 (Runes)
- **Router**: Inertia.js 2.0
- **Styling**: Tailwind CSS 4.1 (CSS-first)
- **Theme**: Shadcn Zinc (Monochrome)
- **Build**: Vite 5
- **PWA**: vite-plugin-pwa

## 📋 Prerequisiti

- Docker & Docker Compose
- Rete Docker `plv_network` (per reverse proxy)

## 🛠️ Setup Locale

### 1. Clone e Dipendenze

```bash
# Installa dipendenze PHP
docker run --rm -v $(pwd):/app -w /app composer:2 install --ignore-platform-reqs

# Installa dipendenze Node.js
docker run --rm -v $(pwd):/app -w /app node:22-alpine npm install

# Build assets
docker run --rm -v $(pwd):/app -w /app node:22-alpine npm run build
```

### 2. Avvio Stack

```bash
# Avvia i container
docker compose up -d

# Verifica lo stato
docker compose ps
```

### 3. Database Setup

```bash
# Esegui migrazioni e seeder
docker compose exec app php artisan migrate:fresh --seed --force
```

## 🌐 Deployment

L'applicazione è configurata per funzionare dietro **Nginx Proxy Manager** sulla rete `plv_network`.

**URL Produzione**: https://evo.prolocoventicano.com

### Configurazione Nginx Proxy Manager

- **Scheme**: `http`
- **Forward Hostname/IP**: `plv_saas-app-1` (o `app`)
- **Forward Port**: `8000`
- **Websockets Support**: ✅ Enabled (per Reverb)

## 📁 Struttura Progetto

```
plv_saas/
├── app/
│   ├── Http/
│   │   └── Middleware/
│   │       ├── HandleInertiaRequests.php
│   │       └── TrustProxies.php
│   └── Models/
│       ├── User.php (UUIDv7)
│       ├── Event.php (UUIDv7)
│       └── Membership.php (UUIDv7)
├── database/
│   ├── migrations/
│   │   └── 2025_01_01_000000_create_plv_schema.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css (Tailwind 4 + Shadcn Theme)
│   ├── js/
│   │   ├── app.js
│   │   └── Pages/
│   │       └── Welcome.svelte
│   └── views/
│       └── app.blade.php
├── docker/
│   ├── pgsql/ (bind mount)
│   └── redis/ (bind mount)
├── Dockerfile
├── docker-compose.yml
└── .env
```

## 🗄️ Database

### Schema

- **users**: Utenti e membri (UUIDv7)
- **events**: Eventi e manifestazioni (UUIDv7)
- **memberships**: Tessere associative con QR code (UUIDv7)

### Seeder Iniziale

- Admin: `admin@prolocoventicanese.it` / `password`
- Membro: `mario.rossi@example.com` / `password`
- Eventi: Fiera Campionaria 2025, Sagra del Prosciutto 2025

## 🎨 Styling

Il progetto usa **Tailwind CSS 4** con configurazione CSS-first (no `tailwind.config.js`).

Tema: **Shadcn Zinc** (Black Primary, White Background)

Variabili CSS definite in `resources/css/app.css` usando la direttiva `@theme`.

## 🔧 Comandi Utili

```bash
# Logs in tempo reale
docker compose logs -f

# Accesso shell container app
docker compose exec app sh

# Artisan commands
docker compose exec app php artisan [command]

# Clear cache
docker compose exec app php artisan optimize:clear

# Rebuild container
docker compose down
docker compose build --no-cache app
docker compose up -d
```

## 👥 Team

- **Tech Lead**: Massimiliano
- **Stack**: Bleeding Edge PHP Ecosystem

## 📝 Note

- Tutte le chiavi primarie usano **UUIDv7** (trait `HasUuids`)
- Le migrazioni sono **consolidate** in un singolo file
- Il tema è **strettamente monocromatico** (Zinc palette)
- PWA configurato con manifest e service worker

---

**Pro Loco Venticanese** - L'evoluzione della tradizione 🇮🇹
