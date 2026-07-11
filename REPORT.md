# Code Review & Cleanup Report — Grand Azure Hotel

## Overall assessment

This was a better-than-average AI-generated PHP project. Prepared statements
were used consistently, passwords were hashed with `password_hash`/`password_verify`,
ownership checks existed before letting a user cancel someone else's booking,
and the database schema had real foreign keys with sensible `ON DELETE` rules.
It was not "AI slop" that needed a rewrite — it needed a review, which is
what you asked for.

That said, it had real bugs (not just style issues), and one part of your
brief doesn't match the actual code. I'm flagging that up front rather than
quietly working around it.

---

## Important: the requested PHP→HTML conversion list didn't match the code

You listed `index.php`, `about.php`, `rooms.php`, `services.php`, `gallery.php`,
and `contact.php` as candidates to convert to static HTML. I checked each one
individually. Here's what's actually true:

| Page | What it actually does | Can it be static HTML? |
|---|---|---|
| `index.php` | Pulls featured rooms, services, gallery preview, and testimonials from the database | **No** — real dynamic content |
| `rooms.php` | Pulls the full room list and prices from the database | **No** — this is the core of "Hotel Booking Management System"; converting it would delete working functionality |
| `services.php` | Pulls services from the database | **No** |
| `gallery.php` | Pulls gallery photos from the database | **No** |
| `contact.php` | Saves submitted messages into the `contacts` table | **No** — it's a form processor, not a page |
| `about.php` | Static text only, no DB queries, no forms | **Yes** |
| `location.php`* | Static text only | **Yes** |
| `restaurant.php`* | Static menu/hours, no DB queries | **Yes** |

*(location.php and restaurant.php weren't on your original list but fit the same "convert if static" rule you described, so I applied it consistently.)*

I did **not** gut `rooms.php`/`services.php`/`gallery.php`/`contact.php`/`index.php`
into static HTML, because doing so would mean deleting real, working,
database-backed features — the opposite of "improve the project." A hotel
booking system whose room list isn't pulled from the database isn't really
a booking system anymore.

**One trade-off to be aware of:** `about.html`, `location.html`, and
`restaurant.html` are now genuinely static files. That means the navigation
bar on those three pages always shows "Login / Book Now" — it can't check
whether someone is logged in, because static HTML has no access to PHP
sessions. Every dynamic `.php` page still correctly shows "My Account /
Logout" when logged in. This is an inherent limitation of static HTML, not
a bug — just something to know before a grader notices the nav looks
slightly different on 3 pages out of 16.

---

## Files converted PHP → HTML
- `about.php` → `about.html`
- `location.php` → `location.html`
- `restaurant.php` → `restaurant.html`

All internal links (`includes/header.php`, `includes/footer.php`, `index.php`,
`contact.php`) were updated to point at the new `.html` filenames. I checked
every file in the project for stale links — none remain.

## Files kept as PHP (with justification)
Everything that touches the database or processes a form: `index.php`,
`rooms.php`, `services.php`, `gallery.php`, `faq.php`, `room-detail.php`,
`contact.php`, `login.php`, `register.php`, `logout.php`, `booking.php`,
all of `admin/`, all of `user/`, `includes/*`.

---

## Real bugs fixed

1. **`booking.php`** — a leftover line referenced the undefined variable
   `$POST` (typo for `$_POST`) before being immediately overwritten by the
   correct line. Harmless in effect, but it threw a PHP warning on every
   booking submission. Removed the dead line.

2. **Broken image paths (this one would have bitten you immediately).**
   The seed data in `hotel.sql` pointed room and gallery images at local
   files like `assets/images/rooms/standard.jpg` — but no such files
   existed anywhere in the project, and the folders didn't even exist.
   The moment you imported the database, every room photo and every
   gallery photo would have shown as a broken image icon. Fixed by
   pointing the seed data at working Unsplash URLs (matching the
   fallback images the front-end already used), and creating the
   `assets/images/rooms/` and `assets/images/gallery/` folders so admin
   uploads have somewhere to land.

3. **Broken image paths, take two.** Once images could be either a full
   URL (the new seed data) or a relative uploaded path, two places that
   manually prepended `../` to the image path would have produced
   `../https://images.unsplash.com/...` — a broken URL. Added an
   `image_url()` helper in `functions.php` that handles both cases
   correctly, and used it in `admin/rooms.php` and `user/history.php`.

4. **`admin/rooms.php`, `admin/users.php`, `admin/bookings.php` — a
   "headers already sent" bug waiting to happen.** All three pages
   included the shared header (which immediately prints HTML) *before*
   processing form submissions that call `redirect()` (which sends a
   `Location:` header). On a server with output buffering off, this
   fails outright; with it on, it's fragile. I reordered all three so
   form processing (and any resulting redirect) happens before any HTML
   is printed — the standard PHP rule of "no output before headers."

5. **`admin/rooms.php` — deleting a room was a GET request.** A room
   could be permanently deleted just by visiting a URL
   (`rooms.php?action=delete&id=5`) — no confirmation server-side, just
   a client-side JS `confirm()` that does nothing to stop a forged
   request, a crawler, or a link shared by accident. Converted this to a
   POST-only form with CSRF protection (see below).

6. **`admin/rooms.php` — image upload directory didn't exist and wasn't
   validated.** `move_uploaded_file()` would silently fail because
   `assets/images/rooms/` wasn't present, and the file type check only
   looked at the file extension, so a renamed non-image file would pass.
   Fixed by creating the directory on demand and validating with
   `getimagesize()`.

7. **`user/history.php`** — invalid CSS `justify-content: between`
   (not a real value; correct is `space-between`). Cosmetic but wrong.

8. **`logout.php`** — always redirected to the guest login page, even
   for an admin logging out, sending them to the wrong login form.
   Fixed to redirect based on which session existed.

9. **Undocumented demo password.** The seed data in `hotel.sql` created
   an admin account and two user accounts with bcrypt hashes and no
   comment anywhere saying what the actual password was. I had to
   brute-force a handful of common guesses to find it was `password`
   (documented now in `hotel.sql` and `README.md`).

---

## Security improvements

- **CSRF protection.** Added `csrf_token()` / `csrf_field()` / `csrf_verify()`
  helpers in `functions.php` and applied them to every state-changing form:
  add/edit/delete room, booking status update, user suspend/reactivate,
  booking submission, booking cancellation, profile update, password change.
  I did *not* add it to login/register/contact, since those don't act on
  another user's existing data — login only reads credentials, register
  only creates the submitter's own account, and contact only inserts a new
  message. Adding CSRF there would be extra defense-in-depth but wasn't
  the priority given the scope of this review.
- **File upload validation** — real image-content check via `getimagesize()`,
  not just trusting the file extension.
- **Consistent output escaping** — a couple of spots printed error messages
  with `<?= $e ?>` instead of `<?= htmlspecialchars($e) ?>`. These
  specific messages were always static strings (not user input), so there
  was no actual exploit path, but I fixed them anyway since relying on
  "this particular string happens to be safe" is not a habit worth
  reinforcing.

## Things I deliberately left alone
- The `payments` table in the schema is never referenced anywhere in the
  PHP code. It's a stub for a feature that doesn't exist yet (no payment
  gateway integration). I didn't remove it or fake something around it —
  flagging it so you know it's there and unused, likely a good candidate
  for a future-work section in your final-year project write-up if you
  ever extend this.
- No rate-limiting on login attempts. Worth adding if you take this
  further, not in scope for this pass.

---

## Folder structure changes
- Added `assets/images/rooms/` and `assets/images/gallery/` (previously
  missing entirely — see bug #2 above).
- Added `user/includes/` — see DRY section below.
- No other structural moves were necessary; the original layout already
  matched the admin/user/assets/includes/database pattern you described.

## DRY cleanup: user dashboard pages
`user/dashboard.php`, `user/history.php`, and `user/profile.php` each had
their own full copy of the sidebar HTML and ~15 lines of CSS — three
copies of the same thing. (`admin/` already did this correctly, with a
shared `admin/includes/header.php`/`footer.php` — the user pages just
never got the same treatment.) Created `user/includes/header.php` and
`user/includes/footer.php` on the same pattern, and rewired all three
pages to use them. One place to change the sidebar now, not three.

## Database changes
- Fixed the broken image seed data (bug #2/#3 above).
- Documented the demo login credentials directly in the SQL comments.
- No schema changes — the table structure, keys, and constraints were
  already sound (proper `AUTO_INCREMENT` primary keys, `FOREIGN KEY`
  constraints with `ON DELETE CASCADE` where it made sense, `ENUM` columns
  for constrained values like booking/room status, `InnoDB` engine,
  `utf8mb4` throughout). I did not find a reason to change it.

---

## How I verified this actually works

I didn't just read the code — I installed MySQL/MariaDB and PHP in this
environment, imported `hotel.sql`, ran the site with PHP's built-in server,
and drove it with `curl` end to end:

- Homepage and every content page (`rooms.php`, `gallery.php`, `faq.php`,
  `services.php`, `about.html`, `location.html`, `restaurant.html`,
  `room-detail.php`, `contact.php`) returned HTTP 200 with no PHP warnings,
  notices, or errors in the server log.
- Registered a new account, logged in as a seeded user, loaded the
  dashboard.
- Submitted a booking **without** a CSRF token — correctly rejected, no
  row inserted.
- Submitted the same booking **with** a valid CSRF token — correctly
  inserted, correct price calculated, showed up in booking history.
- Logged in as admin, loaded the dashboard, added a room via POST with a
  valid CSRF token, then deleted it via POST with a valid CSRF token —
  both worked.
- Tried the *old* vulnerable pattern (`GET /admin/rooms.php?action=delete&id=1`)
  against the *new* code — correctly did nothing. The room was still
  there afterward.

This is the level of testing I'd want before calling something "working,"
so that's what I did rather than assuming the code was correct because it
looked correct.
