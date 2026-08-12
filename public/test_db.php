<?php
header('Content-Type: text/plain; charset=utf-8');

$autoloadPath = __DIR__.'/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    $autoloadPath = __DIR__.'/vendor/autoload.php';
}
if (file_exists($autoloadPath)) {
    require $autoloadPath;
}

// Bootstrap Laravel
$bootstrapPath = __DIR__.'/../bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    $bootstrapPath = __DIR__.'/bootstrap/app.php';
}
$app = require $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Modules\VaccineRegistration\Models\Schedule;
use Carbon\Carbon;

$nowVn = Carbon::now('Asia/Ho_Chi_Minh');
$today = $nowVn->toDateString();
echo "Today VN: " . $today . "\n";
echo "DB Default Connection: " . DB::getDefaultConnection() . "\n";

$schedules = Schedule::query()
    ->where('is_active', true)
    ->whereDate('date', '>=', $today)
    ->get();

echo "Schedules count: " . $schedules->count() . "\n";
foreach ($schedules as $s) {
    echo "ID: {$s->id}, Date: " . $s->date->toDateString() . "\n";
}
