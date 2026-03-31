<?php

//Manually bootstrap Laravel and run tests without Artisan
$basePath = '/Users/anil/Desktop/sita/hotel-management-system';

// Set up a fresh environment
putenv('APP_NAME=Laravel');
putenv('APP_ENV=testing');
putenv('APP_DEBUG=false');
putenv('APP_URL=http://localhost');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=:memory:');
putenv('LOG_CHANNEL=stderr');

// Load composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap the application
$app = require $basePath . '/bootstrap/app.php';

// Get Laravel container
$container = $app;

// Load test configuration
$container->make('config')->set('database.default', 'sqlite');
$container->make('config')->set('database.connections.sqlite', [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
]);

// Run migrations
echo "Running migrations...\n";
$migrator = $container->make('migrator');
$migrator->run(['/Users/anil/Desktop/sita/hotel-management-system/database/migrations']);

// Now run tests using Pest
echo "\n\nRunning tests...\n";
echo "===================\n\n";

passthru('cd ' . $basePath . ' && php artisan test tests/Unit/GuestSegmentationEngineTest.php --no-ansi 2>&1 | sed -e "s/$(tput cols)//" > /tmp/realtest.log; echo "Test execution complete"; cat /tmp/realtest.log');
