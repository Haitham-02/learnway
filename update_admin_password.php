<?php
/**
 * Update Admin Password Script
 * Run: php update_admin_password.php
 */

echo "\n╔════════════════════════════════════════════╗\n";
echo "║  UPDATE ADMIN PASSWORD                     ║\n";
echo "╚════════════════════════════════════════════╝\n\n";

require 'vendor/autoload.php';

$dbPath = __DIR__ . '/var/data.db';
$newPassword = '2005';

try {
    // Connect to SQLite
    $pdo = new PDO("sqlite:$dbPath", '', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "[1/2] Connecting to database... ✓\n";

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE email = ?");
    $stmt->execute(['admin@learnway.com']);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        echo "  ! Admin user not found. Run generate_hash.php first.\n";
        exit(1);
    }

    // Hash new password
    echo "[2/2] Updating password...\n";
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    if ($stmt->execute([$hashedPassword, 'admin@learnway.com'])) {
        echo "  ✓ Password updated successfully\n";
    } else {
        echo "  ! Failed to update password\n";
        exit(1);
    }

    echo "\n╔════════════════════════════════════════════╗\n";
    echo "║  ✓ PASSWORD UPDATED                        ║\n";
    echo "╚════════════════════════════════════════════╝\n\n";

    echo "Admin Credentials:\n";
    echo "  Email:       admin@learnway.com\n";
    echo "  Password:    $newPassword\n";
    echo "\n";

    echo "Access the application:\n";
    echo "  URL: http://127.0.0.1:8000\n";
    echo "\n";

} catch (PDOException $e) {
    echo "\n╔════════════════════════════════════════════╗\n";
    echo "║  ✗ DATABASE ERROR                          ║\n";
    echo "╚════════════════════════════════════════════╝\n\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nDatabase path: $dbPath\n";
    echo "Database file exists: " . (file_exists($dbPath) ? "Yes" : "No") . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Done!\n\n";
?>

