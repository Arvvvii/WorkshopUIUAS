<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update Deadline Tour article image (ID 1)
    $stmt = $pdo->prepare("UPDATE articles SET image_url = ? WHERE id = 1");
    $stmt->execute(['assets/DeadlineTourc.jpeg']);
    echo "Updated Deadline Tour article image to assets/DeadlineTourc.jpeg\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
