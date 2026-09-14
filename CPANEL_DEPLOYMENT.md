# cPanel deployment checklist

1. Create a MariaDB database and database user in cPanel. Grant the user all required privileges.
2. Import the project SQL schema before opening the application.
3. Set the values in `includes/config.php` on the server: `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `API_SECRET`, `SITE_BASE_URL`, and `BASE_URL`.
4. Use a long random `API_SECRET` and never commit real credentials.
5. Upload the project into `public_html` and confirm `includes/.htaccess` remains present.
6. Create and protect `assets/images/products` and `assets/images/payments`; ensure PHP execution is disabled there.
7. Enable an SSL certificate and keep the root `.htaccess` HTTPS redirect enabled.
8. Use a supported PHP version with PDO MySQL, Fileinfo, and GD enabled.
9. Test registration, login, cart, pickup checkout, online proof upload, admin login, and logout.
10. Review PHP and MariaDB error logs after each test, then remove any test accounts and orders.

Do not expose `includes/config.php`, database exports, logs, or environment files through the web server.
