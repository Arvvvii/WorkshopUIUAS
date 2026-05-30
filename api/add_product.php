<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = $_POST['name'] ?? '';
$category_name = $_POST['category'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$weight = $_POST['weight'] ?? 0;

if (empty($name) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'Name and price are required']);
    exit;
}

// Function to handle file upload
function uploadImage($fileKey, $isMultiple = false, $index = null) {
    $uploadDir = '../assets/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    if ($isMultiple && $index !== null) {
        if (isset($_FILES[$fileKey]['name'][$index]) && $_FILES[$fileKey]['error'][$index] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . $index . '_' . basename($_FILES[$fileKey]['name'][$index]);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'][$index], $targetPath)) {
                return 'assets/products/' . $fileName;
            }
        }
    } else {
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . basename($_FILES[$fileKey]['name']);
            $targetPath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath)) {
                return 'assets/products/' . $fileName;
            }
        }
    }
    return null;
}

try {
    $pdo->beginTransaction();

    // 1. Resolve Category
    $category_id = null;
    if ($category_name) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name LIKE ? LIMIT 1");
        $stmt->execute(["%$category_name%"]);
        $cat = $stmt->fetch();
        if ($cat) {
            $category_id = $cat['id'];
        }
    }

    // Main image logic
    $mainImageUrl = uploadImage('main_image');
    if (!$mainImageUrl) {
        $mainImageUrl = 'https://placehold.co/800x800/f3f4f6/f72585?text=Merch';
    }

    // 2. Insert Base Product
    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, stock, weight, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $category_id, $price, $stock, $weight, $mainImageUrl]);
    $productId = $pdo->lastInsertId();

    // 3. Insert Product Images
    $stmtImg = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)");
    $stmtImg->execute([$productId, $mainImageUrl, 1]); // Primary
    
    if (isset($_FILES['gallery_images'])) {
        $count = count($_FILES['gallery_images']['name']);
        for ($i = 0; $i < $count; $i++) {
            $imgUrl = uploadImage('gallery_images', true, $i);
            if ($imgUrl) {
                $stmtImg->execute([$productId, $imgUrl, 0]);
            }
        }
    }

    // 4. Insert Variants
    if (isset($_POST['var_name']) && is_array($_POST['var_name'])) {
        $stmtVar = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, stock, additional_price, image_url) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($_POST['var_name'] as $index => $varName) {
            if (empty($varName)) continue;
            
            $varStock = $_POST['var_stock'][$index] ?? 0;
            $varPrice = $_POST['var_price'][$index] ?? 0;
            
            $varImgKey = 'var_image_' . $index;
            $varImgUrl = uploadImage($varImgKey);
            
            $stmtVar->execute([$productId, $varName, $varStock, $varPrice, $varImgUrl]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Product added successfully']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    // Fallback if schema is missing columns (e.g. weight)
    if ($e->getCode() == '42S22') {
        echo json_encode(['success' => false, 'message' => 'Schema error, some columns missing: ' . $e->getMessage()]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>
