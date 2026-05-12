<?php
/**
 * Import database SQL file
 */
require 'vendor/autoload.php';

// Load environment variables
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->loadEnv('.env');

$sqlFile = __DIR__ . '/learnway_web.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

// Read DATABASE_URL from environment
$databaseUrl = $_ENV['DATABASE_URL'] ?? '';
if (!$databaseUrl) {
    die("DATABASE_URL not set in .env\n");
}

// Parse the database URL
$parsed = parse_url($databaseUrl);
$host = $parsed['host'] ?? 'localhost';
$port = $parsed['port'] ?? 3306;
$user = $parsed['user'] ?? 'root';
$pass = $parsed['pass'] ?? '';
$database = ltrim($parsed['path'] ?? '', '/');
$database = explode('?', $database)[0]; // Remove query string

echo "Importing database from: $sqlFile\n";
echo "Target database: $database @ $host:$port\n";
echo "User: $user\n";

try {
    // Try to connect and create database
    // Try with provided password first
    $pdo = null;
    $errors = [];

    // Try different connection methods
    $connectionMethods = [
        [
            'dsn' => "mysql:host=$host;port=$port;charset=utf8mb4",
            'user' => $user,
            'pass' => $pass,
            'desc' => "TCP: host=$host, password=$pass"
        ],
        [
            'dsn' => "mysql:host=$host;charset=utf8mb4",
            'user' => $user,
            'pass' => $pass,
            'desc' => "TCP (no port): host=$host, password=$pass"
        ],
        [
            'dsn' => "mysql:host=localhost;charset=utf8mb4",
            'user' => $user,
            'pass' => $pass,
            'desc' => "localhost: password=$pass"
        ],
        [
            'dsn' => "mysql:host=localhost;charset=utf8mb4",
            'user' => $user,
            'pass' => '',
            'desc' => "localhost: no password"
        ],
        [
            'dsn' => "mysql:host=127.0.0.1;charset=utf8mb4",
            'user' => $user,
            'pass' => '',
            'desc' => "127.0.0.1: no password"
        ],
    ];

    foreach ($connectionMethods as $method) {
        try {
            $pdo = new PDO(
                $method['dsn'],
                $method['user'],
                $method['pass'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            echo "[✓] Connected: " . $method['desc'] . "\n";
            break;
        } catch (PDOException $e) {
            $errors[] = $method['desc'] . ": " . $e->getMessage();
        }
    }

    if (!$pdo) {
        throw new Exception("Could not connect to MySQL with any method:\n" . implode("\n", $errors));
    }

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "[✓] Database created/verified\n";

    // Switch to the database
    $pdo->exec("USE `$database`");

    // Read and execute the SQL file
    $sql = file_get_contents($sqlFile);

    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', preg_split('/;[\r\n]+/', $sql)));
    $count = 0;

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $pdo->exec($statement);
            $count++;
        }
    }

    echo "[✓] Database imported successfully!\n";
    echo "[✓] Total statements executed: $count\n";

    // Verify tables were created
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "[✓] Tables in database: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "    - $table\n";
    }

} catch (PDOException $e) {
    echo "[✗] Database error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "[✗] Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n[✓] Database import complete!\n";

