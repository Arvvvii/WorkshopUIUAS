<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$order_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing order ID']);
    exit;
}

try {
    // Get basic order details
    $stmtOrder = $pdo->prepare("
        SELECT o.id, o.total_amount, o.status, o.order_date, o.tracking_number, o.shipping_address, 
               u.name as user_name, u.email as user_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmtOrder->execute([$order_id]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        exit;
    }

    // Get order items
    $stmtItems = $pdo->prepare("
        SELECT oi.quantity, oi.price, p.name as product_name, p.image_url as product_image,
               v.variant_name, v.image_url as variant_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN product_variants v ON oi.variant_id = v.id
        WHERE oi.order_id = ?
    ");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    // Format item names and images correctly
    foreach ($items as &$item) {
        if (!empty($item['variant_name'])) {
            $item['name'] = $item['product_name'] . ' - ' . $item['variant_name'];
        } else {
            $item['name'] = $item['product_name'];
        }

        // Prefer variant image if available
        $item['image'] = (!empty($item['variant_image']) && $item['variant_image'] !== 'null') 
                         ? $item['variant_image'] 
                         : $item['product_image'];
        
        unset($item['product_name'], $item['variant_name'], $item['product_image'], $item['variant_image']);
    }

    $order['items'] = $items;

    echo json_encode(['success' => true, 'data' => $order]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
