<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $query = "SELECT p.*, c.name as category_name,
              (SELECT COUNT(*) FROM product_variants WHERE product_id = p.id) as variant_count
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id";
              
    if ($search !== '') {
        $query .= " WHERE p.name LIKE :search OR p.description LIKE :search OR c.name LIKE :search";
    }
    
    $query .= " ORDER BY p.id DESC";
              
    $stmt = $pdo->prepare($query);
    
    if ($search !== '') {
        $stmt->execute(['search' => '%' . $search . '%']);
    } else {
        $stmt->execute();
    }
    
    $products = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $products
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to fetch products: ' . $e->getMessage()]);
}
?>
