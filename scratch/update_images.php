<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $updates = [
        1 => 'assets/DeadlineTour.jpeg',
        2 => 'assets/JisooSolo.jpeg',
        3 => 'assets/Boxset.jpeg'
    ];

    foreach ($updates as $id => $url) {
        $stmt = $pdo->prepare("UPDATE articles SET image_url = ? WHERE id = ?");
        $stmt->execute([$url, $id]);
        echo "Updated article ID $id with $url\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
