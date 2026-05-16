<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$title     = $data['title']     ?? $_POST['title']     ?? '';
$content   = $data['content']   ?? $_POST['content']   ?? '';
$category  = $data['category']  ?? $_POST['category']  ?? '';
$author    = $data['author']    ?? $_POST['author']    ?? 'Admin';
$image_url = $data['image_url'] ?? $_POST['image_url'] ?? 'https://placehold.co/800x450/f3f4f6/f72585?text=News';

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Title and content are required']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO articles (title, content, category, author, image_url, created_at) VALUES (?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$title, $content, $category, $author, $image_url]);

    echo json_encode(['success' => true, 'message' => 'Article published successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to save article: ' . $e->getMessage()]);
}
?>
