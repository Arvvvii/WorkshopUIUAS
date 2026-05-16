<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    foreach($pdo->query("SELECT id, title, image_url FROM articles") as $r) {
        echo $r['id'] . " | " . $r['title'] . " | " . $r['image_url'] . "\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
