<?php

$host = '127.0.0.1';
$db   = 'learnway_web';
$user = 'root';
$pass = '2532001';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$tables = [];
$result = $pdo->query("SHOW TABLES");
while ($row = $result->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

$return = "";

foreach ($tables as $table) {
    $result = $pdo->query("SELECT * FROM $table");
    $num_fields = $result->columnCount();

    $return .= 'DROP TABLE IF EXISTS '.$table.';';
    $row2 = $pdo->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_NUM);
    $return .= "\n\n".$row2[1].";\n\n";

    for ($i = 0; $i < $num_fields; $i++) {
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $return .= 'INSERT INTO '.$table.' VALUES(';
            for ($j = 0; $j < $num_fields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                if (isset($row[$j])) {
                    $return .= '"'.$row[$j].'"';
                } else {
                    $return .= '""';
                }
                if ($j < ($num_fields - 1)) {
                    $return .= ',';
                }
            }
            $return .= ");\n";
        }
    }
    $return .= "\n\n\n";
}

$folder = __DIR__ . '/../database_dumps';
if (!is_dir($folder)) {
    mkdir($folder, 0777, true);
}

$file = $folder . '/learnway_web_dump_' . date('Y-m-d_H-i-s') . '.sql';
file_put_contents($file, $return);

echo "Database dumped successfully to: " . realpath($file) . "\n";
