<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Measure DB Query (uncached)
$startDb = microtime(true);
$toolsDb = \App\Models\Tool::approved()->with('categoryRelation')->get();
$categoriesDb = \App\Models\Category::withCount('approvedTools')->orderBy('sort_order')->get();
$timeDb = microtime(true) - $startDb;

// Measure Cache Get (cached)
// Let's populate first
\Illuminate\Support\Facades\Cache::rememberForever('welcome_tools', function() use ($toolsDb) { return $toolsDb; });
\Illuminate\Support\Facades\Cache::rememberForever('welcome_categories', function() use ($categoriesDb) { return $categoriesDb; });

$startCache = microtime(true);
$toolsCache = \Illuminate\Support\Facades\Cache::get('welcome_tools');
$categoriesCache = \Illuminate\Support\Facades\Cache::get('welcome_categories');
$timeCache = microtime(true) - $startCache;

echo "DB Query Time: " . number_format($timeDb * 1000, 2) . " ms\n";
echo "Cache Get Time: " . number_format($timeCache * 1000, 2) . " ms\n";
echo "Cache is " . number_format($timeDb / $timeCache, 1) . "x faster!\n";
