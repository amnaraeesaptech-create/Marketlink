<?php
/*
 * MarketLink self-check.
 * If the app shows an error, run this and read the [FAIL] lines:
 *     php diagnose.php
 */
$failures = 0;

function check($name, $pass, $hint = '')
{
    global $failures;
    if (!$pass) {
        $failures++;
    }
    echo ($pass ? '[OK]   ' : '[FAIL] ') . $name . PHP_EOL;
    if (!$pass && $hint !== '') {
        echo '         FIX: ' . $hint . PHP_EOL;
    }
}

echo 'MarketLink diagnose' . PHP_EOL;
echo '====================' . PHP_EOL;

// 1. PHP version
check('PHP 8.2+ (yours: ' . PHP_VERSION . ')', version_compare(PHP_VERSION, '8.2.0', '>='), 'Install PHP 8.2 or newer (XAMPP with PHP 8.2+).');

// 2. Extensions
$need = ['pdo_sqlite' => 'SQLite database driver', 'mbstring' => null, 'openssl' => null, 'fileinfo' => null, 'tokenizer' => null, 'xml' => null];
foreach ($need as $ext => $label) {
    check('PHP extension: ' . $ext, extension_loaded($ext), 'Open php.ini, uncomment the line  extension=' . $ext . '  (remove the ; ) and restart Apache/terminal.');
}

// 3. .env
$envPath = __DIR__ . '/.env';
check('.env file exists', file_exists($envPath), 'Re-extract marketlink-final.zip into an EMPTY folder (the zip already contains .env).');
$key = '';
$dbConn = '';
if (file_exists($envPath)) {
    foreach (file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
        $line = trim($line);
        if (str_starts_with($line, 'APP_KEY=')) {
            $key = trim(substr($line, 8));
        }
        if (str_starts_with($line, 'DB_CONNECTION=')) {
            $dbConn = trim(substr($line, 14));
        }
    }
    check('APP_KEY is set', strlen($key) > 10, 'Run:  php artisan key:generate');
    check('DB_CONNECTION=sqlite (simple mode)', $dbConn === 'sqlite', 'Your .env says DB_CONNECTION=' . ($dbConn === '' ? '(empty)' : $dbConn) . ' — delete .env and re-extract the zip.');
}

// 4. Database file
$dbPath = __DIR__ . '/database/database.sqlite';
check('database/database.sqlite exists', file_exists($dbPath), 'Re-extract marketlink-final.zip into an EMPTY folder.');
check('database/ folder writable', is_writable(__DIR__ . '/database'), 'Right-click database folder > Properties > uncheck Read-only (Windows) or chmod 775.');
try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $users = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    check('Database readable (users found: ' . $users . ')', $users >= 3, 'Run:  php artisan migrate --seed');
    $pdo = null;
} catch (Throwable $e) {
    check('Database readable', false, substr($e->getMessage(), 0, 160));
}

// 5. Writable storage
check('storage/ writable', is_writable(__DIR__ . '/storage'), 'Fix folder permissions (uncheck Read-only on Windows).');
check('storage/framework/sessions writable', is_writable(__DIR__ . '/storage/framework/sessions'), 'Fix folder permissions.');

// 6. Vendor
check('vendor/ installed', file_exists(__DIR__ . '/vendor/autoload.php'), 'Run:  composer install');

echo '====================' . PHP_EOL;
if ($failures === 0) {
    echo "ALL OK. Now run:\n  php artisan serve\nThen open http://127.0.0.1:8000\n";
} else {
    echo $failures . " problem(s) found. Fix the [FAIL] lines above, then run:\n  php artisan serve\n";
}
exit($failures === 0 ? 0 : 1);
