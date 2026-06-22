<?php
session_start();
require_once 'db.php';

// Proteksi IDOR
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden. Admin access required.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$id = $_POST['id'] ?? '';
$name = $_POST['name'] ?? '';
$category_name = $_POST['category'] ?? '';
$price = $_POST['price'] ?? 0;
$stock = $_POST['stock'] ?? 0;
$weight = $_POST['weight'] ?? 0;

if (empty($id) || empty($name) || empty($price)) {
    echo json_encode(['success' => false, 'message' => 'ID, name, and price are required']);
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

    // 2. Fetch current image to fallback or keep
    $stmtCurrent = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmtCurrent->execute([$id]);
    $currentProd = $stmtCurrent->fetch();
    $mainImageUrl = $currentProd['image_url'] ?? '';

    // Handle new main image upload
    $newMainImageUrl = uploadImage('main_image');
    if ($newMainImageUrl) {
        $mainImageUrl = $newMainImageUrl;
    }

    // 3. Update Base Product
    $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ?, weight = ?, image_url = ? WHERE id = ?");
    $stmt->execute([$name, $category_id, $price, $stock, $weight, $mainImageUrl, $id]);

    // 4. Handle Primary Image in product_images
    if ($newMainImageUrl) {
        // Remove old primary if it exists, or just set all existing primary to non-primary
        $stmtResetPrimary = $pdo->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
        $stmtResetPrimary->execute([$id]);

        // Insert new primary
        $stmtImg = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)");
        $stmtImg->execute([$id, $mainImageUrl, 1]);
    }

    // 5. Insert Additional Gallery Images
    if (isset($_FILES['gallery_images'])) {
        $stmtImg = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_primary) VALUES (?, ?, ?)");
        $count = count($_FILES['gallery_images']['name']);
        for ($i = 0; $i < $count; $i++) {
            $imgUrl = uploadImage('gallery_images', true, $i);
            if ($imgUrl) {
                $stmtImg->execute([$id, $imgUrl, 0]);
            }
        }
    }

    // 6. Update Variants
    // Delete existing variants and re-insert to keep order and handle deletions easily
    $stmtDelVars = $pdo->prepare("DELETE FROM product_variants WHERE product_id = ?");
    $stmtDelVars->execute([$id]);

    if (isset($_POST['var_name']) && is_array($_POST['var_name'])) {
        $stmtVar = $pdo->prepare("INSERT INTO product_variants (product_id, variant_name, stock, additional_price, image_url) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($_POST['var_name'] as $index => $varName) {
            if (empty($varName)) continue;
            
            $varStock = $_POST['var_stock'][$index] ?? 0;
            $varPrice = $_POST['var_price'][$index] ?? 0;
            
            // Handle image for variant
            $varImgUrl = null;
            // 1. Check if new file was uploaded for this row
            $varImgKey = 'var_image_' . $index;
            $uploadedVarImg = uploadImage($varImgKey);
            
            if ($uploadedVarImg) {
                $varImgUrl = $uploadedVarImg;
            } else {
                // 2. Fallback to existing image path
                $varImgUrl = $_POST['var_existing_image'][$index] ?? null;
                if ($varImgUrl === '' || $varImgUrl === 'null') {
                    $varImgUrl = null;
                }
            }
            
            $stmtVar->execute([$id, $varName, $varStock, $varPrice, $varImgUrl]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
