<?php

$host = 'localhost';
$db = 'learnway_web';
$user = 'root';
$pass = '2005';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  // Fix profile pictures with full paths (forward slashes)
  $sql = "UPDATE users SET profile_picture = SUBSTRING_INDEX(profile_picture, '/', -1) WHERE profile_picture LIKE '%/%'";
  $pdo->exec($sql);
  echo "Fixed forward slash paths\n";
  
  // Fix profile pictures with backslashes
  $sql2 = "UPDATE users SET profile_picture = SUBSTRING_INDEX(profile_picture, '\\\\', -1) WHERE profile_picture LIKE '%\\\\%'";
  $pdo->exec($sql2);
  echo "Fixed backslash paths\n";
  
  // Remove any entries that are null or empty
  $pdo->exec("UPDATE users SET profile_picture = NULL WHERE profile_picture = ''");
  echo "Cleaned up empty values\n";
  
  echo "Profile pictures fixed successfully!\n";
} catch (Exception $e) {
  echo 'Error: ' . $e->getMessage();
  exit(1);
}
