<?php
require 'db.php';
$pdo->exec("ALTER TABLE order_items ADD COLUMN variant_id INT NULL AFTER product_id");
echo "OK";
