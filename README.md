# Hotel Management System

A comprehensive Laravel-based hotel management system designed to streamline hotel operations including room reservations, guest management, billing, food ordering, and cleaning requests.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Project](#running-the-project)
- [Database Management](#database-management)
- [Testing](#testing)

---

## Requirements

Make sure your system has the following installed:

- **PHP**: 8.2 or higher
- **Composer**: Latest version ([Download](https://getcomposer.org/))
- **Node.js**: 16 or higher ([Download](https://nodejs.org/))
- **npm**: Comes with Node.js
- **Database**: MySQL 8.0+ or SQLite
- **Git**: For version control

---

## Installation

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd hotel-management-system
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Create Environment File

Copy the example environment file and create your own:

```bash
cp .env.example .env
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Configure Database

Edit the `.env` file and configure your database connection:

**For MySQL:**

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hotel_management
DB_USERNAME=root
DB_PASSWORD=your_password
```

**For SQLite:**

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Step 7: Run Database Migrations

```bash
php artisan migrate
```

### Step 8: Run Database Seeders (Optional - for demo data)

```bash
php artisan db:seed
```

This will populate the database with sample rooms, services, food items, and reservations.

---

## Configuration

### Environment Variables

Important configuration variables in `.env`:

| Variable        | Description                           |
| --------------- | ------------------------------------- |
| `APP_NAME`      | Application name                      |
| `APP_ENV`       | Environment (local, production)       |
| `APP_DEBUG`     | Debug mode (true/false)               |
| `APP_URL`       | Application URL                       |
| `DB_CONNECTION` | Database driver (mysql, sqlite, etc.) |
| `MAIL_MAILER`   | Mail driver for notifications         |

### Automatic Setup Script

Alternatively, you can run the included setup script:

```bash
chmod +x setup.sh
./setup.sh
```

This script will automate steps 2-8 mentioned above.

---

## Running the Project

### Option 1: Using Built-in Development Server

Start the development server:

```bash
php artisan serve
```

Then open your browser and navigate to:

```
http://localhost:8000
```

The application will be accessible at this URL with automatic asset compilation during development.

### Option 2: Using Vite (for Asset Compilation)

If you need to compile frontend assets, open another terminal and run:

```bash
npm run dev
```

This will:

- Start the Vite development server
- Watch for changes in CSS and JavaScript files
- Provide hot module reloading for CSS and JS

### Option 3: Production Build

For production, compile assets and run with a production server:

```bash
# Compile assets for production
npm run build

# Start the application (with a production server like Nginx)
# Configure your web server to point to the public/ directory
```

---

## Database Management

### Create New Migration

```bash
php artisan make:migration create_table_name
```

### Run Migrations

```bash
php artisan migrate
```

### Rollback Migrations

```bash
php artisan migrate:rollback
```

### Fresh Migration (Drop all tables and re-run)

```bash
php artisan migrate:fresh
```

### Rollback All and Reseed

```bash
php artisan migrate:fresh --seed
```

---

## Testing

This project uses **Pest PHP** for testing.

### Run All Tests

```bash
./vendor/bin/pest
```

### Run Tests with Coverage

```bash
./vendor/bin/pest --coverage
```

### Run Specific Test File

```bash
./vendor/bin/pest tests/Feature/SomeTest.php
```

### Run Tests in Parallel

```bash
./vendor/bin/pest --parallel
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/      # Application controllers
│   └── Middleware/       # HTTP middleware
├── Models/               # Eloquent models
│   ├── Charge.php
│   ├── CleaningRequest.php
│   ├── Food.php
│   ├── FoodOrder.php
│   ├── Invoice.php
│   ├── Payment.php
│   ├── Reservation.php
│   ├── Room.php
│   ├── Service.php
│   └── User.php
└── Providers/            # Service providers

database/
├── migrations/           # Database migrations
├── seeders/             # Database seeders
└── factories/           # Model factories for testing

resources/
├── css/                 # Stylesheets
├── js/                  # JavaScript files
└── views/               # Blade templates

routes/
├── web.php              # Web routes
└── console.php          # Console commands

tests/
├── Feature/             # Feature tests
└── Unit/                # Unit tests
```

---

## Common Issues & Solutions

### Issue: Composer dependencies not installed

**Solution:**

```bash
composer install --no-interaction
```

### Issue: Node modules not installed

**Solution:**

```bash
npm install
npm ci  # For CI environments
```

### Issue: Database migration fails

**Solution:**

- Ensure database credentials in `.env` are correct
- Verify database exists and is accessible
- Try: `php artisan migrate:fresh` (warning: this drops all tables)

### Issue: Permission denied on artisan

**Solution:**

```bash
chmod +x artisan
```

### Issue: Port 8000 already in use

**Solution:**

```bash
php artisan serve --port=8001
```

---

## Additional Commands

### Create a New Controller

```bash
php artisan make:controller ControllerName
```

### Create a New Model

```bash
php artisan make:model ModelName -m  # -m creates migration
```

### Clear Application Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Code Quality & Linting

This project uses PHPStan for static analysis:

```bash
./vendor/bin/phpstan analyse
```

---

## Support & Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [Pest PHP Documentation](https://pestphp.com)
- [Vite Documentation](https://vitejs.dev)

---

## License

This project is open source and available under the MIT License.

---

## Getting Help

If you encounter issues:

1. Check the error message carefully
2. Review the [Common Issues & Solutions](#common-issues--solutions) section
3. Check Laravel and package documentation
4. Contact the development team for support
