<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch users: ' . $e->getMessage()]);
}
?>
