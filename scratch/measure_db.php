<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = microtime(true);
$count = \App\Models\Tool::count();
$time = microtime(true) - $start;

echo "Tool Count: " . $count . "\n";
echo "Database query time: " . number_format($time * 1000, 2) . " ms\n";
