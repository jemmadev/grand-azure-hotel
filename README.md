# Grand Azure Hotel — Hotel Management System

A hotel booking and management system built with HTML, CSS, JavaScript, PHP, and MySQL. Includes a public-facing site (rooms, gallery, services, booking) and an admin dashboard for managing rooms, bookings, users, and reports.

## Requirements

* PHP 7.4+ (tested on 8.3) with the `mysqli` extension
* MySQL 5.7+ or MariaDB 10.3+
* Apache/XAMPP, or PHP's built-in server for local testing

## Setup

1. **Copy the project** into your web server's document root (e.g. `htdocs/hotel` in XAMPP).
2. **Create the database.** Import `database/hotel.sql`:

```
   mysql -u root -p < database/hotel.sql
   ```

   This creates the database with sample rooms, services, gallery photos, and FAQs.

3. **Check `includes/config.php`.** Update `DB\_HOST`, `DB\_USER`, `DB\_PASS`, and `DB\_NAME` to match your own environment. This file is not committed to version control — copy the example values from the project owner or set up your own local database and seed it yourself.
4. **Run it.**

   * XAMPP/Apache: visit `http://localhost/hotel/index.php`
   * PHP built-in server: `php -S localhost:8000`, then visit `http://localhost:8000/index.php`

## Login Credentials

set your own admin password after importing the database (see the project owner's notes, or generate a new password hash and update the `admins` table directly).



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



