<?php
/**
 * Import learnway_web.sql into SQLite database
 */
require 'vendor/autoload.php';

$dbPath = __DIR__ . '/var/data.db';
$sqlFile = __DIR__ . '/learnway_web.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

echo "Creating SQLite database at: $dbPath\n";

try {
    // Create/connect to SQLite database
    $pdo = new PDO("sqlite:$dbPath", '', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo "[✓] SQLite database created/opened\n";

    // Read SQL file
    $sql = file_get_contents($sqlFile);

    // Convert MySQL to SQLite (basic conversion)
    $sql = convertMySQLToSQLite($sql);

    // Split by semicolon and execute
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    $count = 0;
    $errors = [];

    foreach ($statements as $statement) {
        if (empty($statement)) {
            continue;
        }

        try {
            $pdo->exec($statement);
            $count++;
        } catch (PDOException $e) {
            // Skip errors for now, log them
            $errors[] = substr($statement, 0, 80) . "... : " . $e->getMessage();
        }
    }

    echo "[✓] $count SQL statements processed\n";

    if (!empty($errors)) {
        echo "[!] " . count($errors) . " errors occurred (this may be normal for type conversions):\n";
        foreach (array_slice($errors, 0, 5) as $error) {
            echo "    - $error\n";
        }
    }

    // Verify tables
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "[✓] Tables created: " . count($tables) . "\n";
    foreach ($tables as $table) {
        echo "    - $table\n";
    }

     echo "\n[✓] Database import complete!\n";

     // Setup roles and admin account
     setupRolesAndAdmin($pdo);

} catch (Exception $e) {
    echo "[✗] Error: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Setup default roles and admin account
 */
function setupRolesAndAdmin($pdo) {
    echo "\n[Setting up roles and admin account...]\n";

    try {
        // Clear existing roles
        $pdo->exec("DELETE FROM roles");

        // Insert default roles
        $pdo->exec("
            INSERT INTO roles (id, name, role_category, description) VALUES 
            (1, 'ADMIN', 'Administration', 'System Administrator'),
            (2, 'TEACHER', 'Academic', 'Teacher'),
            (3, 'STUDENT', 'Academic', 'Student')
        ");
        echo "[✓] Roles created\n";

        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@learnway.com']);

        if (!$stmt->fetch()) {
            // Hash password: Admin@123
            $hashedPassword = password_hash('Admin@123', PASSWORD_BCRYPT, ['cost' => 12]);

            $stmt = $pdo->prepare("
                INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active, created_at)
                VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
            ");
            $stmt->execute([1, 'admin@learnway.com', $hashedPassword, 'Admin', 'User', 1]);
            echo "[✓] Admin user created\n";
        } else {
            echo "[!] Admin user already exists\n";
        }

        // Display admin credentials
        echo "\n╔════════════════════════════════════════╗\n";
        echo "║  Admin Credentials                     ║\n";
        echo "╚════════════════════════════════════════╝\n";
        echo "Email:    admin@learnway.com\n";
        echo "Password: Admin@123\n";
        echo "\n";

    } catch (PDOException $e) {
        echo "[!] Could not setup roles/admin: " . $e->getMessage() . "\n";
    }
}

/**
 * Basic MySQL to SQLite conversion
 */
function convertMySQLToSQLite($sql) {
    // Remove MySQL-specific directives
    $sql = preg_replace('/ENGINE=InnoDB\s+DEFAULT CHARSET=.*?;/i', ';', $sql);
    $sql = preg_replace('/DEFAULT CHARSET=utf8mb4.*?COLLATE=utf8mb4_unicode_ci/i', '', $sql);
    $sql = preg_replace('/AUTO_INCREMENT/i', '', $sql);

    // Convert TINYINT to INTEGER
    $sql = preg_replace('/TINYINT(\s|,|\))/i', 'INTEGER$1', $sql);

    // Remove GENERATED ALWAYS AS (stored expressions) - SQLite handles differently
    $sql = preg_replace('/,\s*\w+\s+INT\s+GENERATED\s+ALWAYS\s+AS\s+\(.*?\)\s+STORED/is', '', $sql);

    // Remove UNIQUE KEY constraints on generated columns
    $sql = preg_replace('/,?\s*UNIQUE\s+KEY\s+uniq_\w+\s+\([^)]*_flag\s*\)/i', '', $sql);

    // Convert CONSTRAINT syntax
    $sql = preg_replace('/,\s*CONSTRAINT\s+FK_\w+\s+FOREIGN\s+KEY.*?$/im', '', $sql);

    // Convert LONGTEXT to TEXT
    $sql = preg_replace('/LONGTEXT/i', 'TEXT', $sql);

    // Add IF NOT EXISTS to CREATE TABLE if not present
    $sql = preg_replace('/CREATE TABLE\s+(?!IF NOT EXISTS)/i', 'CREATE TABLE IF NOT EXISTS ', $sql);

    // Remove backticks (SQLite doesn't need them usually)
    $sql = str_replace('`', '"', $sql);

    return $sql;
}
?>

