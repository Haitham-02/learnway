<?php
/**
 * Messaging System Diagnostic Script
 * Run this via CLI to test Redis connection and messaging flow
 * 
 * Usage: php test_redis_connection.php
 */

// Autoload composer dependencies
require_once __DIR__ . '/vendor/autoload.php';

use Predis\Client as RedisClient;

echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║  MESSAGING SYSTEM - REDIS TEST             ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";

// Load .env
$dotenv = new \Symfony\Component\Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/.env');

$redisHost = $_ENV['REDIS_HOST'] ?? 'localhost';
echo "[1/5] Loaded configuration:\n";
echo "      REDIS_HOST = {$redisHost}\n";
echo "      REDIS_PORT = 6379\n";
echo "\n";

// Test connection
echo "[2/5] Testing Redis connection...\n";
try {
    $redis = new RedisClient(['host' => $redisHost, 'port' => 6379]);
    
    // Try to ping
    $response = $redis->ping();
    
    echo "      ✓ Connected successfully!\n";
    echo "      ✓ Server responded: {$response}\n";
} catch (\Exception $e) {
    echo "      ✗ Connection failed!\n";
    echo "      ✗ Error: {$e->getMessage()}\n";
    echo "\n";
    echo "Troubleshooting:\n";
    echo "1. Is Docker running? Run: docker compose ps\n";
    echo "2. Is Redis container running? Look for 'learnway-redis-1 Running'\n";
    echo "3. Is port 6379 accessible? Check: docker compose logs redis\n";
    exit(1);
}
echo "\n";

// Test publish
echo "[3/5] Testing message publishing...\n";
try {
    $testMessage = json_encode([
        'room' => 'conversation_test',
        'html' => '<div>Test message</div>',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
    $result = $redis->publish('chat-messages', $testMessage);
    
    echo "      ✓ Published successfully!\n";
    echo "      ✓ Subscribers notified: {$result}\n";
    
    if ($result == 0) {
        echo "      ⚠ Note: 0 subscribers listening (Socket.IO server might not be connected)\n";
    }
} catch (\Exception $e) {
    echo "      ✗ Publish failed!\n";
    echo "      ✗ Error: {$e->getMessage()}\n";
    exit(1);
}
echo "\n";

// Test subscription (briefly)
echo "[4/5] Testing subscription channel...\n";
try {
    // Create a second connection for subscription
    $redisSub = new RedisClient(['host' => $redisHost, 'port' => 6379]);
    
    // Subscribe
    $redisSub->subscribe('chat-messages', function($pubsub, $message) {
        if ($message->kind === 'subscribe') {
            echo "      ✓ Subscription successful!\n";
            echo "      ✓ Listening on channel: {$message->channel}\n";
            return false; // Stop listening
        }
    });
} catch (\Exception $e) {
    echo "      ⚠ Subscription test skipped (would block): {$e->getMessage()}\n";
}
echo "\n";

// Summary
echo "[5/5] Summary:\n";
echo "\n";
echo "╔════════════════════════════════════════════╗\n";
echo "║  ✅ REDIS CONNECTION OK                    ║\n";
echo "╚════════════════════════════════════════════╝\n";
echo "\n";
echo "Next steps:\n";
echo "1. Verify Socket.IO server is running:\n";
echo "   docker compose logs socket-server -f\n";
echo "\n";
echo "2. Open messaging page: http://localhost:8000/messages\n";
echo "\n";
echo "3. Send a test message\n";
echo "\n";
echo "4. Check for these logs:\n";
echo "   Browser (F12 Console):\n";
echo "   - 'SocketController: Connected!'\n";
echo "   - 'SocketController: Received new message'\n";
echo "\n";
echo "   Docker logs:\n";
echo "   - '📨 Broadcasting to room'\n";
echo "\n";
echo "If everything is connected but messages don't appear,\n";
echo "check the browser console for JavaScript errors.\n";
echo "\n";
