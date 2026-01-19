# Trn-Knowledge-System — Development Setup Guide

## 1. Prepare Your Environment

Install::

- Docker Desktop
- Git

---

# 2. Clone the Project

```bash
git clone https://github.com/xxx/Trn-Knowledge-System.git
cd Trn-Knowledge-System
```

---

# 3. Create the .env Environment File

Create the .env file:

```bash
cp src/.env.example src/.env
```

Open src/.env and update the database configuration:

> 2025/01/19: update

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=trd_knowledge_system_db
DB_USERNAME=user
DB_PASSWORD=password


FILESYSTEM_DISK=gcs
GOOGLE_CLOUD_PROJECT_ID=hybrid-text-477900-i6
GOOGLE_CLOUD_STORAGE_BUCKET=ai_knowledge_system
GOOGLE_CLOUD_KEY_FILE=/var/www/storage/app/google-cloud/google-cloud-key.json
VERTEX_AI_DATA_STORE_ID=ai-knowledge-system-data-store_1768793275314
VERTEX_AI_LOCATION=global

```

> 2025/01/19: add

# 3.1 Download File Google Cloud

Download file "google-cloud-key.json" from path "https://drive.google.com/drive/folders/1j-jvHL6zXcezWhgZ4e12JixE7Wm57Mf6?role=writer" to the directory src\storage\app\google-cloud

---

# 4. Start Docker

```bash
docker compose up -d --build
```

Check running containers:

```bash
docker compose ps
```

You should see the following containers:
`app`, `nginx`, `db`, `node`.

---

# 5. Install PHP Dependencies (vendor)

Vendor files are stored in Docker volumes, so installation must be done inside the container:

```bash
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

---

# 6. Recreate Directories Ignored by .gitignore

Enter the container:

```bash
docker compose exec app sh
```

Recreate the standard Laravel directory structure:

```bash
cd /var/www

mkdir -p storage/app/public
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs

touch storage/logs/laravel.log
```

Set permissions for storage and bootstrap/cache:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Clear all Laravel caches:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Exit the container:

```bash
exit
```

# 7. Email Configuration for Forgot Password Feature

Laravel Fortify Forgot Password feature will send a password reset email.
Configure the email settings in the .env file.:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

If you DO NOT configure email
Laravel will log the email content here:

```bash
storage/logs/laravel.log
```

---

> 2025/01/19:add

# 8. Queue Worker Setup for GCS to VerterAI Synchronization

```bash
   docker compose exec app php artisan queue:work --verbose
```

# 9. Run the Application

Backend (Laravel + Nginx):

```bash
Access the system : [Trn-Knowledge-System].(http://localhost:8000).
```

Login credentials:

```bash
username: matsumoto@example.com
password: password
```

> note: the password for all login accounts is `password`

# 10. Commands

Stop all Docker containers

```bash
docker compose down
```

Remove all data (DB, vendor, node_modules)

```bash
docker compose down -v
```

> Warning: This will remove all Docker volumes, including your database.

Build Docker images

```bash
docker compose build
```

Build and start immediately

```bash
docker compose up -d --build
```

Run ESLint (check only)

```bash
docker compose exec node sh -lc "cd /var/www && npm run lint"
```

Run ESLint and auto-fix issues

```bash
docker compose exec node sh -lc "cd /var/www && npm run lint:fix"
```

Check code formatting with Prettier

```bash
   docker compose exec node sh -lc "cd /var/www && npm run format:check"
```

Auto-format frontend code with Prettier

```bash
docker compose exec node sh -lc "cd /var/www && npm run format"
```
