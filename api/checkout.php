<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please login first.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Use session user_id for security
$user_id = $_SESSION['user_id'];
$shipping_address = $data['shipping_address'] ?? '';
$items = $data['items'] ?? [];

if (empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid checkout data (Items missing)']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Insert order with initial total 0, we'll update it later
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, 0, ?, 'pending')");
    $stmt->execute([$user_id, $shipping_address]);
    $order_id = $pdo->lastInsertId();

    $calculated_total = 0;
    $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, variant_id, quantity, price) VALUES (?, ?, ?, ?, ?)");

    foreach ($items as $item) {
        $id_raw = $item['id'] ?? null;
        $product_id = 1; // Fallback
        $variant_id = null;
        
        if (is_string($id_raw) && strpos($id_raw, '-') !== false) {
            $parts = explode('-', $id_raw);
            $product_id = intval($parts[0]);
            $variant_id = intval($parts[1]);
        } else {
            $product_id = intval($id_raw) > 0 ? intval($id_raw) : 1;
        }

        $quantity = $item['qty'] ?? 1;

        // 1. Lock Product and Check Stock/Price
        $stmtProd = $pdo->prepare("SELECT price, stock FROM products WHERE id = ? FOR UPDATE");
        $stmtProd->execute([$product_id]);
        $product = $stmtProd->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product ID $product_id not found.");
        }

        if ($product['stock'] < $quantity) {
            throw new Exception("Insufficient stock for product ID $product_id.");
        }

        $final_price = (float)$product['price'];

        // 2. Lock Variant and Check Stock/Price if variant exists
        if ($variant_id) {
            $stmtVar = $pdo->prepare("SELECT additional_price, stock FROM product_variants WHERE id = ? FOR UPDATE");
            $stmtVar->execute([$variant_id]);
            $variant = $stmtVar->fetch(PDO::FETCH_ASSOC);

            if (!$variant) {
                throw new Exception("Variant ID $variant_id not found.");
            }

            if ($variant['stock'] < $quantity) {
                throw new Exception("Insufficient stock for variant ID $variant_id.");
            }

            $final_price += (float)$variant['additional_price'];

            // Decrement variant stock
            $stmtUpdateVar = $pdo->prepare("UPDATE product_variants SET stock = stock - ? WHERE id = ?");
            $stmtUpdateVar->execute([$quantity, $variant_id]);
        }

        // Decrement product stock
        $stmtUpdateProd = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmtUpdateProd->execute([$quantity, $product_id]);

        // Insert order item with correct price
        $stmtItem->execute([$order_id, $product_id, $variant_id, $quantity, $final_price]);

        $calculated_total += ($final_price * $quantity);
    }

    // Update order with calculated total
    $stmtUpdateTotal = $pdo->prepare("UPDATE orders SET total_amount = ? WHERE id = ?");
    $stmtUpdateTotal->execute([$calculated_total, $order_id]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Checkout successful', 'order_id' => $order_id, 'total_amount' => $calculated_total]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()]);
}
?>
