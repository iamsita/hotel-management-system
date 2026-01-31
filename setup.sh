#!/bin/bash

# HMS Quick Setup Script
echo "=========================================="
echo "Hotel Management System - Quick Setup"
echo "=========================================="
echo ""

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "Error: artisan file not found. Please run this script from the HMS root directory."
    exit 1
fi

echo "✓ In correct directory"
echo ""

# Step 1: Install composer dependencies
echo "Step 1: Installing Composer dependencies..."
if [ ! -d "vendor" ]; then
    composer install
    echo "✓ Composer dependencies installed"
else
    echo "✓ Composer dependencies already installed"
fi
echo ""

# Step 2: Install npm dependencies
echo "Step 2: Installing NPM dependencies..."
if [ ! -d "node_modules" ]; then
    npm install
    echo "✓ NPM dependencies installed"
else
    echo "✓ NPM dependencies already installed"
fi
echo ""

# Step 3: Environment setup
echo "Step 3: Setting up environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✓ .env file created"
else
    echo "✓ .env file already exists"
fi
echo ""

# Step 4: Generate app key
echo "Step 4: Generating application key..."
php artisan key:generate
echo "✓ Application key generated"
echo ""

# Step 5: Create database
echo "Step 5: Setting up database..."
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    echo "✓ SQLite database created"
else
    echo "✓ Database file already exists"
fi
echo ""

# Step 6: Run migrations
echo "Step 6: Running migrations..."
php artisan migrate --force
echo "✓ Migrations completed"
echo ""

# Step 7: Seed database
echo "Step 7: Seeding sample data..."
php artisan db:seed
echo "✓ Sample data seeded"
echo ""

# Step 8: Build frontend assets
echo "Step 8: Building frontend assets..."
npm run build
echo "✓ Assets built"
echo ""

echo "=========================================="
echo "✓ Setup Complete!"
echo "=========================================="
echo ""
echo "To start the development server, run:"
echo "  php artisan serve"
echo ""
echo "Then visit: http://localhost:8000"
echo ""
echo "Dashboard will load with sample data:"
echo "  - 20 Rooms (various types)"
echo "  - 8 Guest Accounts"
echo "  - 7 Hotel Services"
echo ""
