# WWW.NEXAN.ID — Under Development

Maintenance/development landing page for WWW.NEXAN.ID.

## Features
- PHP server-side countdown with a 72-day duration.
- Shared countdown timestamp; page refreshes do not reset it.
- Light/dark theme follows the operating system by default.
- Manual theme preference is stored in browser localStorage.
- Bootstrap 5.3, Tailwind CSS CDN, Font Awesome 6, and Inter font.
- Asia/Jakarta timezone.

## Requirements
- PHP 8.0+
- Web server with PHP-FPM or Apache PHP
- Writable `nexan-storage` directory
- Internet access for CDN assets

## Deploy
Upload repository contents to the document root. Ensure the PHP-FPM user can write to `nexan-storage`. On first request, PHP creates `nexan-storage/countdown-start.txt`.

Example for aaPanel when PHP-FPM runs as `www`:

```bash
mkdir -p /www/wwwroot/nexan.id/nexan-storage
chown -R www:www /www/wwwroot/nexan.id/nexan-storage
chmod 755 /www/wwwroot/nexan.id/nexan-storage
```

Adjust ownership if PHP-FPM uses another user.

## Protect runtime storage
The included `nexan-storage/.htaccess` denies direct access on Apache. For Nginx, add:

```nginx
location ^~ /nexan-storage/ {
    deny all;
    return 403;
}
```

The runtime timestamp is intentionally excluded from Git.

## Reset countdown
Remove the timestamp file on the server; the next request initializes a new 72-day period:

```bash
rm /www/wwwroot/nexan.id/nexan-storage/countdown-start.txt
```
