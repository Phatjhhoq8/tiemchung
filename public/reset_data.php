<?php
header('Content-Type: text/plain; charset=utf-8');
header('X-Accel-Buffering: no');

@ob_end_clean();
ob_implicit_flush(true);

$autoloadPath = __DIR__.'/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    $autoloadPath = __DIR__.'/vendor/autoload.php';
}
if (file_exists($autoloadPath)) {
    require $autoloadPath;
}

$bootstrapPath = __DIR__.'/../bootstrap/app.php';
if (!file_exists($bootstrapPath)) {
    $bootstrapPath = __DIR__.'/bootstrap/app.php';
}
$app = require $bootstrapPath;
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Starting data delete reset...\n";
    flush();
    
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    
    $tables = [
        'registration_vaccines',
        'registrations',
        'patients',
        'vaccination_workflows',
        'point_allocations',
        'point_transactions',
        'customers',
        'consultation_leads',
        'audit_logs'
    ];
    
    foreach ($tables as $table) {
        try {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
                try {
                    DB::statement("ALTER TABLE `$table` AUTO_INCREMENT = 1");
                } catch (\Exception $e) {}
                echo "Successfully deleted table: $table\n";
            } else {
                echo "Table not found: $table\n";
            }
        } catch (\Throwable $ex) {
            echo "Error deleting table $table: " . $ex->getMessage() . "\n";
        }
        flush();
    }
    
    // Reset stock_quantity về 100 trong bảng center_vaccines
    try {
        if (Schema::hasTable('center_vaccines')) {
            DB::table('center_vaccines')->update([
                'stock_quantity' => 100,
                'stock_status' => 'available'
            ]);
            echo "Reset center_vaccines stock_quantity to 100\n";
        }
    } catch (\Throwable $ex) {
        echo "Error resetting center_vaccines: " . $ex->getMessage() . "\n";
    }
    flush();
    
    // Reset reserved_count của slots về 0
    try {
        if (Schema::hasTable('slots')) {
            DB::table('slots')->update([
                'reserved_count' => 0
            ]);
            echo "Reset slots reserved_count to 0\n";
        }
    } catch (\Throwable $ex) {
        echo "Error resetting slots: " . $ex->getMessage() . "\n";
    }
    flush();
    
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "Data reset completed successfully!\n";
    flush();
} catch (\Throwable $e) {
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    echo "Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    flush();
}
