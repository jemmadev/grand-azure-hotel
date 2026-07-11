# Grand Azure Hotel — Hotel Management System

A hotel booking and management system built with HTML, CSS, JavaScript, PHP, and MySQL. Includes a public-facing site (rooms, gallery, services, booking) and an admin dashboard for managing rooms, bookings, users, and reports.

## Requirements
- PHP 7.4+ (tested on 8.3) with the `mysqli` extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache/XAMPP, or PHP's built-in server for local testing

## Setup

1. **Copy the project** into your web server's document root (e.g. `htdocs/hotel` in XAMPP).

2. **Create the database.** Import `database/hotel.sql`:
   ```
   mysql -u root -p < database/hotel.sql
   ```
   This creates the `grand_azure_hotel` database with sample rooms, services, gallery photos, FAQs, and demo accounts.

3. **Check `includes/config.php`.** Defaults (`DB_HOST=localhost`, `DB_USER=root`, `DB_PASS=''`) match a typical XAMPP install — update if yours differs.

4. **Run it.**
   - XAMPP/Apache: visit `http://localhost/hotel/index.php`
   - PHP built-in server: `php -S localhost:8000`, then visit `http://localhost:8000/index.php`

## Demo Login Credentials
All seeded accounts use the password: **password**

| Role  | Email                 | Password |
|-------|-----------------------|----------|
| Admin | admin@grandazure.com  | password |
| User  | alice@example.com     | password |
| User  | bob@example.com        | password |

**Change these before deploying anywhere public.**

## Folder Structure
```
├── admin/              Admin PHP pages (dashboard, rooms, bookings, users, reports)
├── user/               User account pages (dashboard, booking history, profile)
├── assets/             CSS, JS, and uploaded images
├── includes/           Shared backend code (config, db connection, helpers)
├── database/hotel.sql  Full schema + seed data
├── index.php           Homepage
├── rooms.php           Room listing
├── services.php        Services listing
├── gallery.php         Photo gallery
├── login.php / register.php / logout.php   Authentication
└── booking.php         Booking form + logic
```

See `REPORT.md` for the full list of changes made and bugs fixed during development.
