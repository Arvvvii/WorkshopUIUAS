<?php
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['name']) || !isset($data['price']) || !isset($data['stock'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, stock = ?, category_id = (SELECT id FROM categories WHERE name = ? LIMIT 1) WHERE id = ?");
    $stmt->execute([
        $data['name'],
        (float)$data['price'],
        (int)$data['stock'],
        $data['category'],
        (int)$data['id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
