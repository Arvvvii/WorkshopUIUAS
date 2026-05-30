<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$shipping_address = $data['shipping_address'] ?? '';
$total_amount = $data['total_amount'] ?? 0;
$items = $data['items'] ?? [];

if (!$user_id || empty($items)) {
    echo json_encode(['success' => false, 'message' => 'Invalid checkout data (User ID or Items missing)']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'paid')");
    $stmt->execute([$user_id, $total_amount, $shipping_address]);
    $order_id = $pdo->lastInsertId();

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
        $price = $item['price'] ?? 0;
        
        $stmtItem->execute([$order_id, $product_id, $variant_id, $quantity, $price]);

        // Decrement stock
        if ($variant_id) {
            $stmtVarStock = $pdo->prepare("UPDATE product_variants SET stock = GREATEST(0, stock - ?) WHERE id = ?");
            $stmtVarStock->execute([$quantity, $variant_id]);
        }
        $stmtProdStock = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
        $stmtProdStock->execute([$quantity, $product_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Checkout successful', 'order_id' => $order_id]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()]);
}
?>
