<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$name = $data['name'] ?? $_POST['name'] ?? '';
$category_name = $data['category'] ?? $_POST['category'] ?? ''; // or category_id
$price = $data['price'] ?? $_POST['price'] ?? 0;
$stock = $data['stock'] ?? $_POST['stock'] ?? 0;
$image = $data['image'] ?? $_POST['image'] ?? 'https://placehold.co/400x400/f3f4f6/f72585?text=Merch';

if (empty($name) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Name and price are required']);
    exit;
}

try {
    // Basic category lookup
    $category_id = null;
    if ($category_name) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ? LIMIT 1");
        $stmt->execute(["%$category_name%"]);
        $cat = $stmt->fetch();
        if ($cat) {
            $category_id = $cat['id'];
        } else {
            // insert new category if doesn't exist? Or just leave null
        }
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $price, $stock, $image]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully'
    ]);
} catch (PDOException $e) {
    // If table schema is different (e.g., category instead of category_id), try a fallback
    if ($e->getCode() == '42S22') {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category_name, $price, $stock, $image]);
            echo json_encode(['success' => true, 'message' => 'Product added successfully (fallback schema)']);
        } catch (Exception $e2) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e2->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
