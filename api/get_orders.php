<?php
require_once 'db.php';

try {
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : null;

    if ($user_id) {
        // Fetch orders for a specific user, with item details
        $stmt = $pdo->prepare("
            SELECT
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                u.name AS user_name,
                u.email AS user_email,
                GROUP_CONCAT(
                    CONCAT(oi.quantity, 'x ', p.name)
                    ORDER BY oi.id
                    SEPARATOR ', '
                ) AS items_summary,
                COUNT(oi.id) AS item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$user_id]);
    } else {
        // Fetch ALL orders (admin view)
        $stmt = $pdo->query("
            SELECT
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                u.name AS user_name,
                u.email AS user_email,
                GROUP_CONCAT(
                    CONCAT(oi.quantity, 'x ', p.name)
                    ORDER BY oi.id
                    SEPARATOR ', '
                ) AS items_summary,
                COUNT(oi.id) AS item_count
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON oi.order_id = o.id
            LEFT JOIN products p ON oi.product_id = p.id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ");
    }

    $orders = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $orders]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
