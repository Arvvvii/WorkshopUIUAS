<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$id       = $data['id']       ?? $_POST['id']       ?? null;
$name     = $data['name']     ?? $_POST['name']     ?? '';
$email    = $data['email']    ?? $_POST['email']    ?? '';
$role     = $data['role']     ?? $_POST['role']     ?? '';
$password = $data['password'] ?? $_POST['password'] ?? '';

if (!$id || empty($name) || empty($email) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'ID, nama, email, dan role wajib diisi']);
    exit;
}

try {
    // Check if email already exists for another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar oleh pengguna lain']);
        exit;
    }

    if (!empty($password)) {
        // Update user including password
        $stmt = $pdo->prepare(
            "UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?"
        );
        $stmt->execute([$name, $email, $role, $password, $id]);
    } else {
        // Update user excluding password
        $stmt = $pdo->prepare(
            "UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?"
        );
        $stmt->execute([$name, $email, $role, $id]);
    }

    echo json_encode(['success' => true, 'message' => 'Pengguna berhasil diperbarui']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal memperbarui pengguna: ' . $e->getMessage()]);
}
?>
