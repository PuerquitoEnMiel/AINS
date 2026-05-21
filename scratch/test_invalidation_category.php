<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

// Seed cache
Cache::forever('welcome_categories', 'dummy_categories');

echo "Before save:\n";
echo "welcome_categories: " . (Cache::get('welcome_categories') ?? 'NULL') . "\n";

// Find first category and save it
$category = Category::first();
if ($category) {
    echo "Saving category ID: {$category->id}...\n";
    $category->save();
} else {
    echo "No categories found in database!\n";
}

echo "After save:\n";
echo "welcome_categories: " . (Cache::get('welcome_categories') ?? 'NULL') . "\n";
