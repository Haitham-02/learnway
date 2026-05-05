<?php
// Generate bcrypt hash for password: Admin@123
$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 13]);
echo "Generated bcrypt hash for 'Admin@123':\n";
echo $hash . "\n\n";
echo "Use this hash in your SQL insert statement.\n";
?>
