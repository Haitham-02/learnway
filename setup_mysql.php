<?php
/**
 * Learnway MySQL Database Setup Script
 * Run: php setup_mysql.php
 */

echo "\n╔════════════════════════════════════════════╗\n";
echo "║  LEARNWAY MYSQL DATABASE SETUP             ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

require 'vendor/autoload.php';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=learnway_web", "root", "2005", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "[1/3] Checking MySQL connection... ✓\n";

    // Step 1: Setup roles
    echo "[2/3] Setting up roles...\n";

    try {
        $pdo->exec("DELETE FROM roles");
    } catch (Exception $e) {
        // Table might not exist, that's ok
    }

    $pdo->exec("
        INSERT IGNORE INTO roles (id, name, role_category, description) VALUES 
        (1, 'ADMIN', 'Administration', 'System Administrator'),
        (2, 'TEACHER', 'Academic', 'Teacher'),
        (3, 'STUDENT', 'Academic', 'Student')
    ");
    echo "  ✓ Roles created (Admin, Teacher, Student)\n";

    // Step 2: Create admin account
    echo "[3/3] Setting up admin account...\n";

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@learnway.com']);

    if ($stmt->fetch()) {
        echo "  ! Admin user already exists - updating password\n";
        // Update existing admin with new password
        $hashedPassword = password_hash('2005', PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, 'admin@learnway.com']);
        echo "  ✓ Admin password updated\n";
    } else {
        // Hash password: 2005
        $hashedPassword = password_hash('2005', PASSWORD_BCRYPT, ['cost' => 12]);

        // Insert admin user
        $stmt = $pdo->prepare("
            INSERT INTO users (role_id, email, password_hash, first_name, last_name, is_active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        if ($stmt->execute([1, 'admin@learnway.com', $hashedPassword, 'Admin', 'User', 1])) {
            echo "  ✓ Admin account created\n";
        } else {
            echo "  ! Could not create admin account\n";
        }
    }

    // Verify setup
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM roles");
    $result = $stmt->fetch();
    $roleCount = $result['count'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    $userCount = $result['count'] ?? 0;

    echo "\n╔════════════════════════════════════════════╗\n";
    echo "║  ✓ MYSQL SETUP COMPLETE                    ║\n";
    echo "╚════════════════════════════════════════════╝\n\n";

    echo "Database Statistics:\n";
    echo "  • Database:  learnway_web\n";
    echo "  • Host:      127.0.0.1\n";
    echo "  • Roles:     $roleCount\n";
    echo "  • Users:     $userCount\n";
    echo "\n";

    echo "Admin Credentials:\n";
    echo "  Email:       admin@learnway.com\n";
    echo "  Password:    2005\n";
    echo "\n";

    echo "Access the application:\n";
    echo "  URL: http://127.0.0.1:8000\n";
    echo "\n";

} catch (PDOException $e) {
    echo "\n╔════════════════════════════════════════════╗\n";
    echo "║  ✗ MYSQL ERROR                             ║\n";
    echo "╚════════════════════════════════════════════╝\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nDatabase connection details:\n";
    echo "  Host:     127.0.0.1\n";
    echo "  Database: learnway_web\n";
    echo "  User:     root\n";
    echo "\nMake sure MySQL is running and the credentials are correct.\n\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done!\n\n";
?>

