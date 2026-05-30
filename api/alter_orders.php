<?php
require 'db.php';
try {
    $pdo->exec("ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) NULL AFTER status");
    echo "SUCCESS";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
