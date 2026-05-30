<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit;
}

try {
    // 1. Fetch base product
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }

    // 2. Fetch product images
    $stmtImg = $pdo->prepare("SELECT id, image_url, is_primary FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, id ASC");
    $stmtImg->execute([$id]);
    $product['images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

    // If no images in product_images, fallback to base image_url
    if (empty($product['images']) && !empty($product['image_url'])) {
        $product['images'] = [
            ['id' => 'base', 'image_url' => $product['image_url'], 'is_primary' => 1]
        ];
    }

    // 3. Fetch product variants
    $stmtVar = $pdo->prepare("SELECT id, variant_name, stock, additional_price, image_url FROM product_variants WHERE product_id = ?");
    $stmtVar->execute([$id]);
    $product['variants'] = $stmtVar->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $product
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
