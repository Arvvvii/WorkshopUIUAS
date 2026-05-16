<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $s = $pdo->query("SELECT id, title FROM articles");
    foreach($s as $r) {
        echo $r['id'] . "|" . $r['title'] . "\n";
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
