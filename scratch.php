<?php
require 'vendor/autoload.php';
$kernel = new App\Kernel('dev', true);
$kernel->boot();
$conn = $kernel->getContainer()->get('doctrine')->getConnection();
echo $conn->fetchOne('SHOW CREATE TABLE chapters', [], 1) . "\n";
echo $conn->fetchOne('SHOW CREATE TABLE forum_posts', [], 1) . "\n";
