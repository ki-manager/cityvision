<?php

declare(strict_types=1);

/**
 * Optionales Cron-Skript (z. B. 1x täglich über goneo-Kundencenter):
 * - löscht abgelaufene Cache-Dateien und alte Temp-Dateien
 * - rotiert Logs (behält 90 Tage)
 * Kernfunktionen der Website hängen NICHT von diesem Skript ab.
 */

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/app/bootstrap.php';

$cleanup = static function (string $dir, int $maxAgeSeconds): int {
    $removed = 0;
    foreach (glob($dir . '/*') ?: [] as $file) {
        if (is_file($file) && (time() - filemtime($file)) > $maxAgeSeconds) {
            @unlink($file);
            $removed++;
        }
    }
    return $removed;
};

$cacheRemoved = $cleanup(ROOT_PATH . '/storage/cache', 3600);
$tempRemoved = $cleanup(ROOT_PATH . '/storage/temp', 86400);
$logsRemoved = $cleanup(ROOT_PATH . '/storage/logs', 90 * 86400);

echo json_encode([
    'ok' => true,
    'cache_removed' => $cacheRemoved,
    'temp_removed' => $tempRemoved,
    'logs_removed' => $logsRemoved,
]), PHP_EOL;
