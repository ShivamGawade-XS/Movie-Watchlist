<?php
require_once __DIR__ . '/config.php';

try {
    $conn = db_connect();
    $res = $conn->query("SHOW TABLES");
    $tables = [];
    while ($row = $res->fetch_row()) {
        $tables[] = $row[0];
    }
    header('Content-Type: text/plain; charset=utf-8');
    echo "DB connection successful\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Tables (" . count($tables) . "):\n";
    foreach ($tables as $t) echo " - $t\n";
} catch (Throwable $e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "DB connection failed:\n" . $e->getMessage();
}
