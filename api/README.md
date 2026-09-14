# Deco&Mat Stationers — REST API

This `api/` folder is a thin JSON REST layer that sits **next to** your
existing PHP website (same `stationery` database, same `assets/images`
folder). It's what the Flutter app talks to — the app never touches MySQL
directly.

## Install

1. Copy this whole `api/` folder into your website's root, so you end up
   with e.g. `.../stationery/api/...` alongside the existing `client/`,
   `admin/`, `assets/` folders.
2. Import the security migration once:
   ```
   mysql -u root -p stationery < api/security_additions.sql
   ```
3. Edit `api/bootstrap.php`:
   - DB credentials (`$host`, `$dbname`, `$username`, `$password`) — match
     whatever the rest of the site already uses.
   - `API_SECRET` — change this to a long random string. This signs every
     login token; if it leaks, an attacker can forge tokens.
   - `SITE_BASE_URL` — the public base URL of the website (used to build
     image links for products/categories/payment proofs).

## Endpoints

| Method | Path | Auth | Purpose |
|---|---|---|---|
| POST | `register.php` | – | Create account, returns token |
| POST | `login.php` | – | Log in, returns token |
| GET | `categories.php` | – | List categories |
| GET | `products.php?q=&category=` | – | List/search/filter products |
| GET | `product.php?id=` | – | Product detail + related products |
| GET/POST | `cart.php` | ✅ | View cart / add / update / remove |
| POST | `checkout.php` (multipart) | ✅ | Place an order |
| GET | `orders.php` | ✅ | Order history |
| GET/POST | `profile.php` | ✅ | View/update profile |
| POST | `delete_account.php` | ✅ | Permanently delete account |
| GET | `payment_settings.php` | – | Mobile money / bank instructions |

Auth = send header `Authorization: Bearer <token>` from login/register.

## Security notes

- Passwords are hashed with `password_hash()` (bcrypt) — unchanged from
  the original site.
- All queries use PDO prepared statements — no SQL injection surface was
  added.
- Login is rate-limited (5 failed attempts per email+IP per 15 minutes)
  via `security_additions.sql`.
- Checkout's payment-proof upload validates the **real** file content
  (not just the extension), caps it at 5MB, and saves it under a random
  filename.
- **Serve this API over HTTPS in production.** The Flutter app's Android
  build blocks cleartext traffic by default except for the emulator dev
  address — see `flutter_app/platform_config/`.
