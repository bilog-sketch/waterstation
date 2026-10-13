<?php
// TEMPORARY troubleshooting page. DELETE THIS FILE once the app works.
header('Content-Type: text/plain; charset=utf-8');
echo "PHP " . PHP_VERSION . "\n";
echo "pdo_mysql loaded: " . (extension_loaded('pdo_mysql') ? 'yes' : 'NO') . "\n";
echo "config/.env exists: " . (is_file(__DIR__ . '/config/.env') ? 'yes' : 'no') . "\n\n";
foreach (['APP_DEBUG', 'DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_SSL'] as $k) {
    $v = getenv($k);
    if ($v === false) { echo "$k: NOT SET\n"; continue; }
    echo "$k: set, length " . strlen($v) . ($k === 'DB_PASS' ? '' : ", value [" . $v . "]") . "\n";
}
echo "\n";
try {
    $o = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_TIMEOUT => 10];
    if (getenv('DB_SSL') === '1') {
        $o[PDO::MYSQL_ATTR_SSL_CA] = '/etc/ssl/certs/ca-certificates.crt';
        $o[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }
    $pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4',
        (string)getenv('DB_USER'), (string)getenv('DB_PASS'), $o);
    echo "DATABASE CONNECTION: OK\n";
    $t = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . count($t) . " (expected 12)\n" . implode(', ', $t) . "\n";
} catch (Throwable $e) {
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
}
echo "\nsession folder writable: " . (is_writable(session_save_path() ?: sys_get_temp_dir()) ? 'yes' : 'NO') . "\n";
