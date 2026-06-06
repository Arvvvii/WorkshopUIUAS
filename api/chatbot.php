<?php
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($data['message'] ?? ''));

$response = "";

if (strpos($message, 'halo') !== false || strpos($message, 'hai') !== false || strpos($message, 'hello') !== false) {
    $response = "Halo! Saya BLINKBot. Saya bisa membantu Anda mengecek <b>stok barang</b>, <b>berita terbaru</b>, atau <b>melacak pesanan</b> Anda. Ada yang bisa saya bantu?";
} elseif (strpos($message, 'stok') !== false || strpos($message, 'barang') !== false || strpos($message, 'produk') !== false) {
    try {
        $stmt = $pdo->query("SELECT p.name, IFNULL(SUM(pv.stock), p.stock) as total_stock FROM products p LEFT JOIN product_variants pv ON p.id = pv.product_id GROUP BY p.id LIMIT 3");
        $products = $stmt->fetchAll();
        $response = "Berikut adalah info stok beberapa produk kami:<br><br>";
        foreach($products as $p) {
            $response .= "• " . htmlspecialchars($p['name']) . ": <b>" . $p['total_stock'] . " item</b><br>";
        }
        $response .= "<br>Silahkan kunjungi halaman <a href='katalog.html' class='text-primary underline'>Shop</a> untuk detail lebih lanjut.";
    } catch (PDOException $e) {
        $response = "Maaf, terjadi kesalahan saat mengecek stok: " . $e->getMessage();
    }
} elseif (strpos($message, 'berita') !== false || strpos($message, 'artikel') !== false || strpos($message, 'news') !== false) {
    try {
        $stmt = $pdo->query("SELECT title FROM articles ORDER BY created_at DESC LIMIT 3");
        $articles = $stmt->fetchAll();
        $response = "Ini dia berita terbaru dari BLINKCO:<br><br>";
        foreach($articles as $a) {
            $response .= "• " . htmlspecialchars($a['title']) . "<br>";
        }
        $response .= "<br>Kunjungi <a href='arsip-artikel.html' class='text-primary underline'>News</a> untuk selengkapnya.";
    } catch (PDOException $e) {
        $response = "Maaf, terjadi kesalahan saat mengecek berita: " . $e->getMessage();
    }
} elseif (preg_match('/(?:lacak|resi|pesanan|order)\s+(\d+)/i', $message, $matches)) {
    $order_id = $matches[1];
    try {
        $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        if ($order) {
            $response = "Status pesanan Anda (ID: $order_id) saat ini adalah: <b class='uppercase text-primary'>" . htmlspecialchars($order['status']) . "</b>.";
        } else {
            $response = "Maaf, pesanan dengan ID $order_id tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $response = "Maaf, terjadi kesalahan saat melacak pesanan: " . $e->getMessage();
    }
} elseif (strpos($message, 'lacak') !== false || strpos($message, 'resi') !== false || strpos($message, 'pesanan') !== false || strpos($message, 'order') !== false) {
    $response = "Untuk melacak pesanan, ketik 'lacak [ID Pesanan]', contoh: <b>lacak 12</b>.";
} elseif (strpos($message, 'pesan') !== false || strpos($message, 'beli') !== false) {
    $response = "Ingin membeli merchandise? Silakan kunjungi halaman <a href='katalog.html' class='text-primary underline font-bold'>Shop</a> kami untuk memesan barang favorit Anda!";
} else {
    $response = "Maaf, saya kurang mengerti. Anda bisa bertanya tentang:<br>• Stok barang<br>• Berita terbaru<br>• Lacak pesanan (contoh: <i>lacak 12</i>)<br>• Cara pesan/beli";
}

echo json_encode(['success' => true, 'response' => $response]);
?>
