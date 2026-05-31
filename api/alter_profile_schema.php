<?php
require_once 'db.php';

try {
    $cols = [
        "phone" => "VARCHAR(20) NULL",
        "gender" => "VARCHAR(10) NULL",
        "birthdate" => "DATE NULL",
        "address_name" => "VARCHAR(100) NULL",
        "address_phone" => "VARCHAR(20) NULL",
        "address_province" => "VARCHAR(100) NULL",
        "address_city" => "VARCHAR(100) NULL",
        "address_postal_code" => "VARCHAR(10) NULL",
        "address_detail" => "TEXT NULL"
    ];

    foreach ($cols as $colName => $colDef) {
        try {
            $pdo->exec("ALTER TABLE users ADD COLUMN $colName $colDef");
            echo "Added column $colName successfully.\n";
        } catch (PDOException $e) {
            echo "Column $colName already exists or error: " . $e->getMessage() . "\n";
        }
    }
    echo "Schema update completed.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
