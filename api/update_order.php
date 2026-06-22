<?php
session_start();
require_once 'db.php';

// Proteksi IDOR
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden. Admin access required.']);
    exit;
}

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

$tracking_number = isset($data['tracking_number']) ? $data['tracking_number'] : null;
$order_id = (int)$data['order_id'];
$new_status = $data['status'];

try {
    $pdo->beginTransaction();

    $stmtCheck = $pdo->prepare("SELECT status FROM orders WHERE id = ? FOR UPDATE");
    $stmtCheck->execute([$order_id]);
    $order = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }

    if ($order['status'] === $new_status) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Order status unchanged.']);
        exit;
    }

    if ($tracking_number !== null) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, tracking_number = ? WHERE id = ?");
        $stmt->execute([$new_status, $tracking_number, $order_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
    }

    // Logika Pemulihan Stok
    if ($new_status === 'cancelled') {
        $stmtItems = $pdo->prepare("SELECT product_id, variant_id, quantity FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$order_id]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        $stmtRestoreProd = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
        $stmtRestoreVar = $pdo->prepare("UPDATE product_variants SET stock = stock + ? WHERE id = ?");

        foreach ($items as $item) {
            $stmtRestoreProd->execute([$item['quantity'], $item['product_id']]);
            if ($item['variant_id']) {
                $stmtRestoreVar->execute([$item['quantity'], $item['variant_id']]);
            }
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
