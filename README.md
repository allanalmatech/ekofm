# Eko FM - Core PHP Radio Website + Admin CMS

Production-style modular Core PHP application for radio broadcasting and CMS management.

## Stack
- PHP 7.x compatible
- MySQL / MariaDB
- PDO prepared statements
- Bootstrap + custom CSS/JS
- No Laravel, no WordPress, no heavy framework

## Project Structure
```
ekofm/
├── .htaccess
├── index.php
├── admin/
│   ├── _init.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── news.php
│   ├── programs.php
│   ├── dramas.php
│   ├── services.php
│   ├── ratecard.php
│   ├── pages.php
│   ├── radio.php
│   ├── settings.php
│   ├── media.php
│   ├── contacts.php
│   ├── users.php
│   ├── roles.php
│   └── activity.php
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── functions.php
│   ├── auth.php
│   └── rbac.php
├── modules/
│   ├── pages.php
│   ├── news.php
│   ├── programs.php
│   ├── dramas.php
│   ├── services.php
│   └── ratecard.php
├── pages/
│   ├── home.php
│   ├── about.php
│   ├── news.php
│   ├── news-single.php
│   ├── programs.php
│   ├── dramas.php
│   ├── services.php
│   ├── rate-card.php
│   ├── contact.php
│   ├── search.php
│   ├── dynamic-page.php
│   └── 404.php
├── templates/
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   ├── player.php
│   ├── admin_header.php
│   ├── admin_sidebar.php
│   └── admin_footer.php
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── admin.css
│   └── js/
│       ├── app.js
│       ├── player.js
│       └── admin.js
├── uploads/
└── database/
    └── schema.sql
```

## Setup (XAMPP / Shared Hosting)
1. Put project in web root (`htdocs/ekofm`).
2. Create DB `ekofm` in phpMyAdmin.
3. Import `database/schema.sql`.
4. Update DB credentials in `includes/config.php`.
5. Ensure Apache rewrite module is enabled.
6. Ensure `uploads/` is writable.
7. Open: `http://localhost/ekofm`.

## Default Admin Login
- URL: `http://localhost/ekofm/admin/login.php`
- Super Admin: `admin@ekofm.com`
- Editor: `editor@ekofm.com`
- Password (both): `Admin@123`

## Persistent Shoutcast Player Notes
- Global player lives outside page content in `templates/player.php`.
- Audio state is persisted in localStorage (`assets/js/player.js`) with:
  - `playing` state
  - `currentTime`
  - `volume`
- Lightweight PJAX (`assets/js/app.js`) updates content region `#pjax-container` for internal links marked `data-pjax`, so audio instance survives navigation and does not restart.
- Stream URL/title are editable in `admin/radio.php` and stored in settings.

## Security Implemented
- Password hashing using `password_hash` / `password_verify`
- CSRF token on admin and contact forms
- PDO prepared statements
- Escaped output helper `e()`
- Login rate limiting table (`login_attempts`)
- RBAC permissions (role + user overrides)
- Activity logs for key admin actions
