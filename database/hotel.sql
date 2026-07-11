-- ============================================================
--  Grand Azure Hotel — Database Schema
--  Compatible with MySQL 5.7+ / MariaDB 10.3+
--  Run this in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS grand_azure_hotel
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE grand_azure_hotel;

-- ============================================================
--  Table: admins
-- ============================================================
CREATE TABLE IF NOT EXISTS admins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,
    avatar      VARCHAR(255)        DEFAULT NULL,
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL UNIQUE,
    password    VARCHAR(255)        NOT NULL,
    phone       VARCHAR(20)         DEFAULT NULL,
    address     TEXT                DEFAULT NULL,
    avatar      VARCHAR(255)        DEFAULT NULL,
    status      ENUM('active','suspended') DEFAULT 'active',
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: services
-- ============================================================
CREATE TABLE IF NOT EXISTS services (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    description TEXT                DEFAULT NULL,
    icon        VARCHAR(100)        DEFAULT NULL,    -- Font-Awesome class
    image       VARCHAR(255)        DEFAULT NULL,
    status      ENUM('active','inactive') DEFAULT 'active',
    sort_order  INT                 DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: rooms
-- ============================================================
CREATE TABLE IF NOT EXISTS rooms (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150)        NOT NULL,
    category        ENUM('Standard','Deluxe','Executive','Family','Presidential Suite') NOT NULL,
    description     TEXT                DEFAULT NULL,
    amenities       TEXT                DEFAULT NULL,   -- JSON or comma-separated
    capacity        INT                 DEFAULT 2,
    price_per_night DECIMAL(10,2)       NOT NULL,
    image           VARCHAR(255)        DEFAULT NULL,
    gallery         TEXT                DEFAULT NULL,   -- JSON array of image paths
    floor           INT                 DEFAULT 1,
    room_number     VARCHAR(10)         DEFAULT NULL,
    status          ENUM('available','booked','maintenance') DEFAULT 'available',
    created_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: bookings
-- ============================================================
CREATE TABLE IF NOT EXISTS bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT                 NOT NULL,
    room_id         INT                 NOT NULL,
    check_in        DATE                NOT NULL,
    check_out       DATE                NOT NULL,
    guests          INT                 DEFAULT 1,
    special_request TEXT                DEFAULT NULL,
    total_price     DECIMAL(10,2)       NOT NULL DEFAULT 0.00,
    status          ENUM('pending','approved','cancelled','completed') DEFAULT 'pending',
    created_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_booking_user FOREIGN KEY (user_id) REFERENCES users(id)   ON DELETE CASCADE,
    CONSTRAINT fk_booking_room FOREIGN KEY (room_id) REFERENCES rooms(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: payments
-- ============================================================
CREATE TABLE IF NOT EXISTS payments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    booking_id      INT                 NOT NULL,
    amount          DECIMAL(10,2)       NOT NULL,
    method          ENUM('cash','card','bank_transfer','online') DEFAULT 'cash',
    status          ENUM('pending','paid','refunded','failed') DEFAULT 'pending',
    transaction_ref VARCHAR(100)        DEFAULT NULL,
    paid_at         TIMESTAMP           NULL DEFAULT NULL,
    created_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_payment_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: contacts
-- ============================================================
CREATE TABLE IF NOT EXISTS contacts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150)        NOT NULL,
    phone       VARCHAR(20)         DEFAULT NULL,
    subject     VARCHAR(200)        DEFAULT NULL,
    message     TEXT                NOT NULL,
    is_read     TINYINT(1)          DEFAULT 0,
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: gallery
-- ============================================================
CREATE TABLE IF NOT EXISTS gallery (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    image       VARCHAR(255)        NOT NULL,
    caption     VARCHAR(200)        DEFAULT NULL,
    category    ENUM('rooms','restaurant','pool','lobby','exterior','events','spa') DEFAULT 'lobby',
    sort_order  INT                 DEFAULT 0,
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: settings
--  Simple key/value store for site-wide settings editable from
--  the Admin > Settings page (hotel name, logo, contact info,
--  social links, footer text, hero text). Seed values below match
--  the previous hardcoded constants in includes/config.php so the
--  site looks identical until an admin changes something.
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
    setting_key   VARCHAR(64)  PRIMARY KEY,
    setting_value TEXT         DEFAULT NULL,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO settings (setting_key, setting_value) VALUES
('hotel_name',         'Grand Azure Hotel'),
('logo',               ''),
('phone',              '+1 (555) 123-4567'),
('email',              'info@grandazure.com'),
('address',            '123 Azure Boulevard, Luxury District, NY 10001, USA'),
('footer_text',        'Experience the pinnacle of luxury at Grand Azure Hotel. Nestled in the heart of the city, we offer world-class amenities, impeccable service, and unforgettable memories.'),
('copyright_text',     'All rights reserved.'),
('hero_title',         'Where Luxury Meets Timeless Elegance'),
('hero_subtitle',      'Discover a world of refined sophistication at Grand Azure Hotel. Award-winning service, breathtaking views, and an unparalleled commitment to your comfort.'),
('social_facebook',    '#'),
('social_instagram',   '#'),
('social_twitter',     '#'),
('social_tripadvisor', '#'),
('social_youtube',     '#')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- ============================================================
--  Table: testimonials
-- ============================================================
CREATE TABLE IF NOT EXISTS testimonials (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    author_name VARCHAR(100)        NOT NULL,
    author_role VARCHAR(100)        DEFAULT 'Guest',
    avatar      VARCHAR(255)        DEFAULT NULL,
    content     TEXT                NOT NULL,
    rating      TINYINT             DEFAULT 5,
    is_visible  TINYINT(1)          DEFAULT 1,
    created_at  TIMESTAMP           DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  Table: faqs
-- ============================================================
CREATE TABLE IF NOT EXISTS faqs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    question    VARCHAR(300)        NOT NULL,
    answer      TEXT                NOT NULL,
    category    VARCHAR(100)        DEFAULT 'General',
    sort_order  INT                 DEFAULT 0,
    is_visible  TINYINT(1)          DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  SEED DATA
-- ============================================================

-- ============================================================
--  DEMO LOGIN CREDENTIALS (for local testing only)
--  All seeded accounts below share the password:  password
--  Admin login:  admin@grandazure.com   / password
--  User logins:  alice@example.com      / password
--                bob@example.com        / password
--  CHANGE OR REMOVE these accounts before any real deployment.
-- ============================================================

-- Admin account (password: Admin@1234)
INSERT INTO admins (name, email, password) VALUES
('Hotel Administrator', 'admin@grandazure.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample users (password: User@1234)
INSERT INTO users (name, email, password, phone) VALUES
('Alice Johnson', 'alice@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0101'),
('Bob Smith',    'bob@example.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1-555-0102');

-- Rooms
-- NOTE: image URLs point to Unsplash so the site displays correctly out of
-- the box. If you upload your own photos via the admin panel (Manage Rooms),
-- they are stored in assets/images/rooms/ and will replace these automatically.
INSERT INTO rooms (name, category, description, amenities, capacity, price_per_night, image, floor, room_number) VALUES
('Comfort Standard Room',   'Standard',          'A cozy room with all essential comforts. Perfect for solo travelers or couples seeking a relaxing stay.', 'Free WiFi,Flat-screen TV,Air Conditioning,Mini-fridge,Safe,Hair Dryer,24h Room Service', 2,  89.00, 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=800&q=80',  1, '101'),
('Superior Deluxe Room',    'Deluxe',            'Spacious and elegantly furnished with premium bedding and stunning city views.', 'Free WiFi,Flat-screen TV,Air Conditioning,Mini-bar,Safe,Hair Dryer,Bathtub,City View,Balcony', 2, 149.00, 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',    2, '201'),
('Executive Business Room', 'Executive',         'Designed for the discerning business traveler. Features a dedicated work desk, ergonomic chair, and express checkout.', 'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Work Desk,Safe,Espresso Machine,Bathtub,Shower,Lounge Access', 2, 229.00, 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80', 3, '301'),
('Spacious Family Room',    'Family',            'Thoughtfully designed for families, offering ample space, two bathrooms, and fun amenities for children.', 'Free WiFi,Smart TV,Air Conditioning,Mini-bar,Safe,Two Bathrooms,Children Amenities,Connecting Rooms', 5, 199.00, 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80',    2, '205'),
('Presidential Suite',      'Presidential Suite','The pinnacle of luxury. A full two-bedroom suite with a private terrace, butler service, and panoramic views.', 'Free WiFi,Smart TV,Air Conditioning,Full Kitchen,Private Terrace,Butler Service,Jacuzzi,Panoramic View,Meeting Room,Private Gym', 4, 599.00, 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80', 10, '1001');

-- Services
INSERT INTO services (name, description, icon, sort_order) VALUES
('Free WiFi',       'High-speed wireless internet throughout the hotel.',                       'fas fa-wifi',            1),
('Swimming Pool',   'Temperature-controlled outdoor pool with poolside bar.',                   'fas fa-swimming-pool',   2),
('Restaurant',      'Fine-dining restaurant serving international and local cuisine.',          'fas fa-utensils',        3),
('Breakfast',       'Daily buffet breakfast with over 50 fresh items.',                         'fas fa-coffee',          4),
('Airport Pickup',  'Complimentary airport transfer for Presidential Suite guests.',            'fas fa-car',             5),
('Conference Hall', 'State-of-the-art conference facilities for up to 500 guests.',            'fas fa-chalkboard',      6),
('Laundry',         'Same-day laundry and dry-cleaning service.',                               'fas fa-tshirt',          7),
('Spa & Wellness',  'Rejuvenating spa treatments, massages, and beauty services.',             'fas fa-spa',             8),
('Fitness Center',  'Fully-equipped gym open 24 hours with personal trainers available.',      'fas fa-dumbbell',        9),
('Parking',         'Secure underground parking with 24-hour surveillance.',                   'fas fa-parking',         10),
('Room Service',    '24-hour in-room dining with an extensive menu.',                          'fas fa-concierge-bell',  11),
('Bar & Lounge',    'Signature cocktail bar with live music every Friday and Saturday night.', 'fas fa-glass-martini-alt', 12);

-- Gallery entries
-- Same fix as rooms above: use working Unsplash URLs instead of local
-- paths that were never actually included in the project's assets folder.
INSERT INTO gallery (image, caption, category, sort_order) VALUES
('https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=800&q=80', 'Grand Lobby',              'lobby',    1),
('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=800&q=80',     'Infinity Pool',            'pool',     2),
('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?auto=format&fit=crop&w=800&q=80',  'Fine Dining Restaurant',   'restaurant', 3),
('https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=800&q=80',  'Presidential Suite',       'rooms',    4),
('https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',  'Luxury Spa',               'spa',      5),
('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',  'Hotel Exterior',           'exterior', 6),
('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80',  'Fitness Center',           'events',   7),
('https://images.unsplash.com/photo-1587827073598-f8d7fc16c8c4?auto=format&fit=crop&w=800&q=80',  'Sky Bar',                  'lobby',    8);

-- Testimonials
INSERT INTO testimonials (author_name, author_role, content, rating) VALUES
('Emily Richardson', 'Business Traveler', 'Absolutely stunning hotel! The Executive Room exceeded all expectations. The service was impeccable and the staff went above and beyond. Will definitely return!', 5),
('James Whitmore',   'Honeymoon Couple',  'We spent our honeymoon here and it was magical. The Presidential Suite was breathtaking. The staff made every moment special. Highly recommend!', 5),
('Sarah Chen',       'Family Vacation',   'Perfect hotel for families. The kids loved the pool and the family room had everything we needed. Breakfast was exceptional. 10/10!', 5),
('Michael Torres',   'Conference Guest',  'The conference facilities are world-class. Our corporate event went flawlessly. The team was professional and accommodating throughout.', 4);

-- FAQs
INSERT INTO faqs (question, answer, category, sort_order) VALUES
('What are the check-in and check-out times?',          'Check-in is from 3:00 PM and check-out is at 12:00 PM (noon). Early check-in and late check-out are available upon request, subject to availability.',                     'General',  1),
('Is breakfast included in the room rate?',             'Breakfast is included in our Deluxe, Executive, and Presidential Suite packages. Standard and Family rooms can add breakfast for $25 per person per day.',                  'Dining',   2),
('Do you offer airport transfers?',                     'Yes, we provide complimentary airport transfers for Presidential Suite guests. Other room categories can book airport transfers at an additional fee of $45 per trip.',     'Transport', 3),
('Is there free parking at the hotel?',                 'Yes, we offer complimentary secure underground parking for all guests.',                                                                                                   'General',  4),
('What is your cancellation policy?',                   'Cancellations made 48 hours or more before check-in receive a full refund. Cancellations within 48 hours are charged one night\'s room rate.',                           'Bookings', 5),
('Are pets allowed?',                                   'We are a pet-friendly hotel! Pets are welcome in designated rooms for a $30 per night fee. Please inform us at the time of booking.',                                    'General',  6),
('Do you have a swimming pool?',                        'Yes! Our temperature-controlled outdoor infinity pool is open daily from 6:00 AM to 10:00 PM. The poolside bar serves refreshments throughout the day.',                 'Amenities', 7),
('Is there a gym/fitness center?',                      'Our fully-equipped fitness center is open 24 hours a day, 7 days a week. Personal trainers are available by appointment.',                                               'Amenities', 8),
('Can I request a specific room or floor?',             'Yes, we do our best to accommodate special requests. Please note that specific room/floor requests are subject to availability and cannot be guaranteed.',                 'Bookings', 9),
('Do you offer wedding or event packages?',             'Absolutely! Our dedicated events team offers customized packages for weddings, corporate events, and private celebrations. Please contact us for a personalized quote.',   'Events',   10);
