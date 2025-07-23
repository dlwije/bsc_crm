# Laravel + Flutter Project

## Project Structure
```
backend-laravel/         # Laravel REST API
mobile-flutter/          # Flutter Mobile App
```

## Tech Stack
- **Backend:** Laravel 12+, PHP 8.2+
- **Frontend:** Flutter 3+
- **Database:** MySQL
- **Auth:** JWT (tymon/jwt-auth)

## Prerequisites
- PHP 8.2+ with Composer
- MySQL (via XAMPP or another server)
- Node.js & npm (if using Laravel Mix or Vite)
- Flutter SDK 3+
- Enable Apache and MySQL via XAMPP (if applicable)

## Backend Installation (Existing Laravel Project)

### 1. Navigate to the backend project:
```bash
cd backend-laravel
```

### 2. Install PHP dependencies:
```bash
composer install
```

### 3. Create the environment file:
```bash
cp .env.example .env
```

### 4. Update `.env` with your database configuration:
```
APP_NAME=Laravel
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_db_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Generate the application key:
```bash
php artisan key:generate
```

### 6. Run migrations and seed the database:
```bash
php artisan migrate
php artisan db:seed
```

### 7. Generate JWT secret key:
```bash
php artisan jwt:secret
```

### 8. Start the Laravel development server:
```bash
php artisan serve
```
Then visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Mobile App Setup (Flutter)

### 1. Navigate to the mobile app directory:
```bash
cd mobile-flutter
```

### 2. Install Flutter dependencies:
```bash
flutter pub get
```

### 3. Run the Flutter app:
```bash
flutter run
```

---

## Notes
- Make sure Apache and MySQL are running.
- Ensure the Laravel backend is running before testing the Flutter app.
- Ensure that the database is correctly set up and seeded.

---
