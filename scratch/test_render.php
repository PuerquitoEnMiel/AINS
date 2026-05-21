<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    Illuminate\Http\Request::capture()
);

// We want to simulate a logged-in user
$user = \App\Models\User::first();
Auth::login($user);

try {
    $html = View::make('lesson_plans.create')->render();
    file_put_contents(__DIR__ . '/rendered.html', $html);
    echo "RENDER_SUCCESS\n";
} catch (\Exception $e) {
    echo "RENDER_ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
