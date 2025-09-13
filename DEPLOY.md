Local Docker development

1. Start services:

   docker-compose up -d

2. Copy `env` file for Docker:

   copy .env.docker .env

3. Install PHP dependencies (if not already):

   composer install

4. Generate app key:

   php artisan key:generate

5. Run migrations:

   php artisan migrate --force

6. Visit the app at http://localhost:8000 and phpMyAdmin at http://localhost:8080

Notes:

Remote MySQL (cPanel) troubleshooting

1. If you're using your cPanel remote MySQL database, ensure you've added your development machine's IP to cPanel -> Remote MySQL (allow access from your IP). cPanel may require the IP of the machine that runs `php artisan migrate`.

2. From this project root run the connectivity checker:

```powershell
php scripts/check_remote_db.php
```

This will test TCP connect and attempt a PDO connection using the `.env` credentials. If it fails, follow the hints printed by the script.

3. If remote access is blocked, either:
    - Run migrations on the server (SSH into the host and run `php artisan migrate --force`).
    - Or create an SSH tunnel and forward the remote DB port to your machine, e.g.:

```powershell
# Example (replace user@host and remote_port):
ssh -L 3307:127.0.0.1:3306 user@example.com -N
# Then update your .env to use DB_HOST=127.0.0.1 and DB_PORT=3307
```

4. After connectivity is validated, run:

```powershell
composer install
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=DatabaseSeeder --force
```

5. If you still encounter permissions or SSL errors, consult your hosting provider — some shared hosts require the DB user to have specific host entries (like `%` or your IP) or require SSL CA certificates.

Image uploads and frontend guidance

- Backend (what I changed and checks you should run):
   - Ensure `FILESYSTEM_DISK=public` in `.env` for public media access.
   - Run `php artisan storage:link` to create `public/storage -> storage/app/public` (I created this link).
   - Confirm PHP has image processing support (GD or Imagick). Run `php -r "phpinfo();" | findstr /i gd` or check `php -m` for `gd`/`imagick`.
   - I added `config/medialibrary.php` which sets the medialibrary disk to `public` by default. If you use S3, update the disk accordingly and set the AWS env vars.
   - If you rely on Spatie image optimizer or conversion binaries in production, install them on the server and enable them in `config/medialibrary.php`.

- Frontend (how to consume images reliably):
   - Use the API `image_url` provided by product endpoints (e.g., `/api/v1/products`, `/api/v1/products/{slug}`) — this field is added server-side via `$product->getFirstMediaUrl('image')`.
   - The `image_url` points to either a public storage URL (e.g., `https://your-site.com/storage/<filename>`) or the S3 URL if you configure S3.
   - Implement a graceful fallback: if `image_url` is empty, show a placeholder image (recommended 800x800) or a CSS background color. That avoids broken images when media is missing.
   - For best performance, show optimized/resized images on the frontend. You can either:
      - Pre-generate conversions via Spatie (requires server binaries) and request `getFullUrl('conversions/<conversionName>-file.jpg')`, or
      - Use a client-side service (Cloudflare Image Resizing, Imgix, etc.) if using CDN.
   - Recommended sizes: thumbnails 300x300, product image 800x800. Use responsive srcset for smaller screens.

Commands to verify locally:
```powershell
# Ensure public disk and symlink exist
php artisan storage:link

# Check PHP upload limits (increase in php.ini if needed)
php -r "echo ini_get('upload_max_filesize').PHP_EOL; echo ini_get('post_max_size').PHP_EOL;"

# Quick file presence check (after uploading an image via Filament/admin)
ls public\storage
```
