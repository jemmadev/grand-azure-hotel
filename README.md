# Grand Azure Hotel — Setup Instructions

## Requirements
- PHP 7.4+ (tested on 8.3) with the `mysqli` extension
- MySQL 5.7+ or MariaDB 10.3+
- Apache/XAMPP, or PHP's built-in server for local testing

## Setup Steps

1. **Copy the project** into your web server's document root
   (e.g. `htdocs/hotel` in XAMPP, or anywhere if using the built-in server).

2. **Create the database.** Import `database/hotel.sql` using phpMyAdmin
   or the command line:
   ```
   mysql -u root -p < database/hotel.sql
   ```
   This creates the `grand_azure_hotel` database and fills it with sample
   rooms, services, gallery photos, FAQs, and demo accounts.

3. **Check `includes/config.php`.** The defaults (`DB_HOST=localhost`,
   `DB_USER=root`, `DB_PASS=''`) match a typical XAMPP install. Update
   them if your MySQL username/password are different.

4. **Run it.**
   - XAMPP/Apache: visit `http://localhost/hotel/index.php`
   - PHP built-in server (from the project folder):
     ```
     php -S localhost:8000
     ```
     then visit `http://localhost:8000/index.php`

## Demo Login Credentials
All seeded accounts use the password: **password**

| Role  | Email                     | Password |
|-------|---------------------------|----------|
| Admin | admin@grandazure.com      | password |
| User  | alice@example.com         | password |
| User  | bob@example.com           | password |

**Change these before deploying anywhere public.**

## Folder Structure
```
HotelWebsite/
├── admin/              Admin-only PHP pages (dashboard, rooms, bookings, users, reports)
│   └── includes/       Shared admin header/footer
├── user/                User account PHP pages (dashboard, booking history, profile)
│   └── includes/        Shared user header/footer
├── assets/
│   ├── css/style.css    All site styling
│   ├── js/main.js       All site JavaScript
│   └── images/          Local image uploads land here (rooms/, gallery/)
├── includes/            Shared backend code (config, db connection, helper functions)
├── database/hotel.sql   Full schema + seed data
├── index.php            Homepage (dynamic: pulls rooms/services/gallery from DB)
├── about.html            Static About page (no DB dependency)
├── rooms.php             Room listing (dynamic, DB-driven)
├── services.php          Services listing (dynamic, DB-driven)
├── restaurant.html        Static Restaurant/menu page (no DB dependency)
├── location.html          Static Location/directions page (no DB dependency)
├── gallery.php           Photo gallery (dynamic, DB-driven)
├── faq.php               FAQ page (dynamic, DB-driven)
├── contact.php           Contact form (dynamic: saves messages to DB)
├── room-detail.php       Single room detail page (dynamic, DB-driven)
├── login.php / register.php / logout.php   User authentication
└── booking.php           The booking form + submission logic
```

See `REPORT.md` for the full list of changes made, bugs fixed, and the
reasoning behind which pages stayed PHP vs became static HTML.
