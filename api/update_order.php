<?php
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing order_id or status.']);
    exit;
}

$allowed_statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($data['status'], $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$data['status'], (int)$data['order_id']]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or status unchanged.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
