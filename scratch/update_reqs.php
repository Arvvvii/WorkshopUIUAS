<?php
$host = 'localhost';
$dbname = 'blinkco_db';
$user = 'root';
$pass = 'admin123';
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Task 4: Update product images
    $stmt1 = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = 1");
    $stmt1->execute(['assets/Lsv2.jpeg']);
    echo "Updated Lightstick image.\n";

    $stmt2 = $pdo->prepare("UPDATE products SET image_url = ? WHERE id = 2");
    $stmt2->execute(['assets/hoodiebp.jpeg']);
    echo "Updated Hoodie image.\n";

    // Task 1: Update Article Deadline Tour created_at to be top
    $stmt3 = $pdo->prepare("UPDATE articles SET created_at = NOW() WHERE id = 1");
    $stmt3->execute();
    echo "Updated Deadline Tour article timestamp.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
