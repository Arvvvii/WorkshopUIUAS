<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    // 1. Calculate General Statistics
    // A. Total Revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) AS total FROM orders WHERE status NOT IN ('pending', 'cancelled')");
    $total_revenue = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // B. Total Orders
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM orders");
    $total_orders = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // C. Total Registered Customers
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM users WHERE role = 'user'");
    $total_customers = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // D. Low Stock Variant Count (stock <= 5)
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM product_variants WHERE stock <= 5");
    $low_stock_count = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // 2. Fetch 5 Recent Orders
    $stmt = $pdo->query("
        SELECT o.id, o.total_amount, o.status, o.order_date, u.name AS user_name, u.email AS user_email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.order_date DESC
        LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch 5 Low Stock Products
    $stmt = $pdo->query("
        SELECT pv.id, pv.variant_name, pv.stock, p.name AS product_name, pv.image_url
        FROM product_variants pv
        JOIN products p ON pv.product_id = p.id
        WHERE pv.stock <= 5
        ORDER BY pv.stock ASC
        LIMIT 5
    ");
    $low_stock_alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Generate Sales Trends (Daily - Last 7 Days)
    $daily_trend = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $daily_trend[$date] = 0;
    }
    $stmtDaily = $pdo->query("
        SELECT DATE(order_date) AS date, SUM(total_amount) AS total
        FROM orders
        WHERE status NOT IN ('pending', 'cancelled')
          AND order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(order_date)
    ");
    foreach ($stmtDaily->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($daily_trend[$row['date']])) {
            $daily_trend[$row['date']] = (int)$row['total'];
        }
    }

    // Convert daily trend to lists for frontend charting
    $daily_labels = [];
    $daily_data = [];
    foreach ($daily_trend as $date => $total) {
        // Format to "D, d M" e.g. "Sat, 30 May"
        $formatted_date = date('D, d M', strtotime($date));
        $daily_labels[] = $formatted_date;
        $daily_data[] = $total;
    }

    // 5. Generate Sales Trends (Monthly - Last 6 Months)
    $monthly_trend = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthly_trend[$month] = 0;
    }
    $stmtMonthly = $pdo->query("
        SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, SUM(total_amount) AS total
        FROM orders
        WHERE status NOT IN ('pending', 'cancelled')
          AND order_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ");
    foreach ($stmtMonthly->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (isset($monthly_trend[$row['month']])) {
            $monthly_trend[$row['month']] = (int)$row['total'];
        }
    }

    // Convert monthly trend to lists for frontend charting
    $monthly_labels = [];
    $monthly_data = [];
    foreach ($monthly_trend as $month => $total) {
        $formatted_month = date('F Y', strtotime($month . "-01"));
        $monthly_labels[] = $formatted_month;
        $monthly_data[] = $total;
    }

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_sales' => $total_revenue,
            'total_orders' => $total_orders,
            'total_users' => $total_customers,
            'low_stock_count' => $low_stock_count
        ],
        'recent_orders' => $recent_orders,
        'low_stock_alerts' => $low_stock_alerts,
        'trends' => [
            'daily' => [
                'labels' => $daily_labels,
                'data' => $daily_data
            ],
            'monthly' => [
                'labels' => $monthly_labels,
                'data' => $monthly_data
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
