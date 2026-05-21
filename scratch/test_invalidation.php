<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tool;
use Illuminate\Support\Facades\Cache;

// Seed cache
Cache::forever('welcome_tools', 'dummy_tools');
Cache::forever('welcome_categories', 'dummy_categories');

echo "Before save:\n";
echo "welcome_tools: " . (Cache::get('welcome_tools') ?? 'NULL') . "\n";
echo "welcome_categories: " . (Cache::get('welcome_categories') ?? 'NULL') . "\n";

// Find first tool and save it (without changing anything)
$tool = Tool::first();
if ($tool) {
    echo "Saving tool ID: {$tool->id}...\n";
    $tool->save();
} else {
    echo "No tools found in database!\n";
}

echo "After save:\n";
echo "welcome_tools: " . (Cache::get('welcome_tools') ?? 'NULL') . "\n";
echo "welcome_categories: " . (Cache::get('welcome_categories') ?? 'NULL') . "\n";
