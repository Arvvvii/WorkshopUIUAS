<?php
require_once __DIR__ . '/../api/db.php';

try {
    // Check if they are already in the DB
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE category_id = 1");
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['success' => false, 'message' => 'Albums already exist in database']);
        exit;
    }

    $albums = [
        [
            'category_id' => 1,
            'name' => 'Born Pink Album',
            'price' => 350000,
            'description' => 'Official BLACKPINK 2nd Full Album [BORN PINK] (BOX SET Version). Includes photobook, sticker, poster, and exclusive tracks.',
            'stock' => 100,
            'image_url' => 'assets/albums/born-pink.jpg'
        ],
        [
            'category_id' => 1,
            'name' => 'Deadline World Tour Package',
            'price' => 1200000,
            'description' => 'Exclusive Deadline World Tour Premium VIP Package. Includes photobook, lanyard, concert poster, and keyrings.',
            'stock' => 50,
            'image_url' => 'assets/albums/deadline.jpeg'
        ],
        [
            'category_id' => 1,
            'name' => 'Kill This Love Album',
            'price' => 280000,
            'description' => 'Official BLACKPINK 2nd Mini Album [KILL THIS LOVE]. Features standard photobook, lyric book, and random photocards.',
            'stock' => 80,
            'image_url' => 'assets/albums/kill-this-love.png'
        ],
        [
            'category_id' => 1,
            'name' => 'Square Up Album',
            'price' => 250000,
            'description' => 'Official BLACKPINK 1st Mini Album [SQUARE UP]. Includes 3D lenticular postcard, photo booklet, and photocard.',
            'stock' => 75,
            'image_url' => 'assets/albums/squareup.jpg'
        ],
        [
            'category_id' => 1,
            'name' => 'The Album',
            'price' => 320000,
            'description' => 'Official BLACKPINK 1st Full Album [THE ALBUM]. Includes hardcover photobook, postcard set, and group poster.',
            'stock' => 90,
            'image_url' => 'assets/albums/the-album.jpg'
        ]
    ];

    $insertStmt = $pdo->prepare("INSERT INTO products (category_id, name, price, description, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");

    foreach ($albums as $album) {
        $insertStmt->execute([
            $album['category_id'],
            $album['name'],
            $album['price'],
            $album['description'],
            $album['stock'],
            $album['image_url']
        ]);
        echo "Inserted: " . $album['name'] . "\n";
    }

    echo "Done!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
