<?php
require_once 'db.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data (could be JSON or form-data)
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? $_POST['email'] ?? '';
$password = $data['password'] ?? $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, role, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Verify password (assuming plain text for now as per instructions, ideally password_verify)
    // You should use password_verify in real production, but for upgrading this prototype...
    // Let's assume plain text or md5 if not hashed, wait, the user didn't specify password hashing.
    // I will use a direct comparison for this prototype, or password_verify if hashed.
    // I'll do a direct comparison to match the previous JS mock logic ("123456", "admin123")
    
    if ($user && $user['password'] === $password) {
        unset($user['password']); // don't send password back
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
