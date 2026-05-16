<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    echo "ARTICLES:\n";
    foreach($pdo->query("SELECT id, title, created_at FROM articles ORDER BY created_at DESC") as $r) {
        echo $r['id'] . "|" . $r['title'] . "|" . $r['created_at'] . "\n";
    }
    echo "\nPRODUCTS:\n";
    foreach($pdo->query("SELECT id, name FROM products") as $r) {
        echo $r['id'] . "|" . $r['name'] . "\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
