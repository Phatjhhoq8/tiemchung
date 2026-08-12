<?php
header('Content-Type: text/plain; charset=utf-8');

$controllerFile = __DIR__.'/../modules/VaccineRegistration/Http/Controllers/VaccineController.php';
if (!file_exists($controllerFile)) {
    $controllerFile = __DIR__.'/modules/VaccineRegistration/Http/Controllers/VaccineController.php';
}

if (file_exists($controllerFile)) {
    echo "Controller file exists!\n";
    $content = file_get_contents($controllerFile);
    echo "File size: " . strlen($content) . " bytes\n";
    echo "MD5 hash: " . md5($content) . "\n";
    
    // Check if timezone fix exists
    if (strpos($content, 'nowVn = \\Carbon\\Carbon::now(\'Asia/Ho_Chi_Minh\')') !== false) {
        echo "Timezone fix is PRESENT in the file!\n";
    } else {
        echo "Timezone fix is MISSING in the file!\n";
    }
    
    // Print around showRegister line 390
    $lines = explode("\n", $content);
    echo "--- Code snippet around line 390 ---\n";
    for ($i = 385; $i <= 410; $i++) {
        if (isset($lines[$i])) {
            echo ($i + 1) . ": " . $lines[$i] . "\n";
        }
    }
} else {
    echo "Controller file not found at: $controllerFile\n";
}
