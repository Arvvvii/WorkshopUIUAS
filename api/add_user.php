<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$name     = $data['name']     ?? $_POST['name']     ?? '';
$email    = $data['email']    ?? $_POST['email']    ?? '';
$password = $data['password'] ?? $_POST['password'] ?? '';
$role     = $data['role']     ?? $_POST['role']     ?? 'user';

if (empty($name) || empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Nama, email, dan password wajib diisi']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar oleh pengguna lain']);
        exit;
    }

    // Insert user
    $stmt = $pdo->prepare(
        "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$name, $email, $password, $role]);

    echo json_encode(['success' => true, 'message' => 'Pengguna berhasil ditambahkan']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal menambahkan pengguna: ' . $e->getMessage()]);
}
?>
