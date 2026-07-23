# Medicare Vaccine Registration System (Hệ Thống Tiêm Chủng Medicare)

> **Commercial Production Application**: Modern, high-performance Laravel Single-Page Application (SPA) for vaccine catalog browsing, online appointment booking, and multi-branch management.

## Project Technology Stack

- **Backend**: Laravel 11.x (PHP >= 8.2), MySQL.
- **Frontend**: Vite 6.x, Tailwind CSS 3.x, **Flowbite UI Kit**, Axios, Vanilla JS (SPA).
- **Brand Theme**: **Medicare Red (`#c8102e`)** as primary dominant brand color.

## Quick Installation & Setup Guide

### 1. Backend Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php database/create_db.php
php artisan migrate --seed
```

### 2. Frontend & Flowbite Setup
```bash
npm install
npm run build    # Or `npm run dev` for hot reload
```

### 3. Start Development Server
```bash
php artisan serve
```
Access the application at `http-[#127.0.0.1:8000](http://127.0.0.1:8000)`.

## Project Documentation & Rules

- **Release Notes & Versions**: See [CHANGELOG.md](file:///home/hongphuoc/Desktop/thue/CHANGELOG.md).
- **AI Agent & Coding Standards**: See [.agents/AGENTS.md](file:///home/hongphuoc/Desktop/thue/.agents/AGENTS.md).
