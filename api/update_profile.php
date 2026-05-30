<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$user_id = $data['user_id'] ?? null;
$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$current_password = $data['current_password'] ?? null;
$new_password = $data['new_password'] ?? null;

if (!$user_id || !$name || !$email) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields (user_id, name, email).']);
    exit;
}

try {
    // 1. Check if email is already taken by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email ini sudah terdaftar oleh pengguna lain.']);
        exit;
    }

    // 2. Password verification & update logic
    if (!empty($new_password)) {
        if (empty($current_password)) {
            echo json_encode(['success' => false, 'message' => 'Password saat ini diperlukan untuk mengganti password.']);
            exit;
        }

        // Fetch stored password to compare
        $stmtPass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmtPass->execute([$user_id]);
        $storedUser = $stmtPass->fetch();

        if (!$storedUser) {
            echo json_encode(['success' => false, 'message' => 'User tidak ditemukan.']);
            exit;
        }

        // Plaintext comparison to match prototype logins
        if ($storedUser['password'] !== $current_password) {
            echo json_encode(['success' => false, 'message' => 'Password saat ini yang Anda masukkan salah.']);
            exit;
        }

        // Update name, email, and password
        $stmtUpdate = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmtUpdate->execute([$name, $email, $new_password, $user_id]);
    } else {
        // Update only name and email
        $stmtUpdate = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmtUpdate->execute([$name, $email, $user_id]);
    }

    // 3. Fetch newly updated user data to return
    $stmtUser = $pdo->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ?");
    $stmtUser->execute([$user_id]);
    $updatedUser = $stmtUser->fetch();

    echo json_encode([
        'success' => true,
        'message' => 'Profil berhasil diperbarui.',
        'user' => $updatedUser
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
