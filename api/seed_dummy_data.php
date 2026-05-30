<?php
require_once 'db.php';

try {
    $pdo->beginTransaction();

    // Insert dummy users if less than 10
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'user'");
    $user_count = $stmt->fetch(PDO::FETCH_ASSOC)['cnt'];
    
    if ($user_count < 10) {
        for ($i = 1; $i <= 10 - $user_count; $i++) {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute(['Dummy User ' . $i, 'dummy'.$i.'@example.com', password_hash('password123', PASSWORD_DEFAULT)]);
        }
    }

    // Set some product variants to low stock
    $pdo->query("UPDATE product_variants SET stock = 3 WHERE id IN (SELECT id FROM (SELECT id FROM product_variants LIMIT 5) as tmp)");

    // Insert dummy orders for the chart to look nice (over the last 7 days and last 6 months)
    $stmt = $pdo->query("SELECT id FROM users WHERE role = 'user' LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($users) > 0) {
        // Daily orders
        for ($i = 0; $i <= 7; $i++) {
            $num_orders = rand(2, 5);
            for ($j = 0; $j < $num_orders; $j++) {
                $user_id = $users[array_rand($users)];
                $amount = rand(150000, 850000);
                $status_arr = ['completed', 'paid', 'processing', 'shipped', 'delivered'];
                $status = $status_arr[array_rand($status_arr)];
                
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, order_date, created_at) VALUES (?, ?, ?, DATE_SUB(CURDATE(), INTERVAL ? DAY), DATE_SUB(NOW(), INTERVAL ? DAY))");
                $stmt->execute([$user_id, $amount, $status, $i, $i]);
            }
        }
        
        // Monthly orders
        for ($i = 1; $i <= 5; $i++) {
            $num_orders = rand(10, 20);
            for ($j = 0; $j < $num_orders; $j++) {
                $user_id = $users[array_rand($users)];
                $amount = rand(200000, 1500000);
                
                $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, order_date, created_at) VALUES (?, ?, 'completed', DATE_SUB(CURDATE(), INTERVAL ? MONTH), DATE_SUB(NOW(), INTERVAL ? MONTH))");
                $stmt->execute([$user_id, $amount, $i, $i]);
            }
        }
    }

    $pdo->commit();
    echo 'Dummy data seeded successfully.';
} catch (Exception $e) {
    $pdo->rollBack();
    echo 'Error seeding data: ' . $e->getMessage();
}
?>
