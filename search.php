<?php
/**
 * search_live.php
 * Live search endpoint — returns JSON {products:[...], posts:[...]}
 * Used by header search modal (no full page reload needed)
 */

// جلوگیری از cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('X-Content-Type-Options: nosniff');

// فقط GET قبول می‌کنیم
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once(__DIR__ . "/include/config.php");
require_once(__DIR__ . "/include/db.php");

// کلمه جستجو
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 2) {
    echo json_encode(['products' => [], 'posts' => []]);
    exit;
}

$like = '%' . $q . '%';

// ── محصولات ──
$products_raw = fetchAll($db,
    "SELECT id, name, `new-price` AS price, price AS old_price, pic
     FROM product
     WHERE name LIKE ? OR description LIKE ?
     ORDER BY id DESC
     LIMIT 6",
    [$like, $like]
);

// ── مقالات ──
$posts_raw = fetchAll($db,
    "SELECT id, title, image, author
     FROM posts
     WHERE title LIKE ? OR body LIKE ?
     ORDER BY id DESC
     LIMIT 4",
    [$like, $like]
);

// sanitize output
function clean($v) {
    return htmlspecialchars_decode(strip_tags((string)$v));
}

$products = [];
foreach ($products_raw as $p) {
    $products[] = [
        'id'        => (int)$p['id'],
        'name'      => clean($p['name']),
        'price'     => (int)$p['price'],
        'old_price' => (int)$p['old_price'],
        'pic'       => basename($p['pic']),
    ];
}

$posts = [];
foreach ($posts_raw as $p) {
    $posts[] = [
        'id'     => (int)$p['id'],
        'title'  => clean($p['title']),
        'image'  => basename($p['image']),
        'author' => clean($p['author']),
    ];
}

echo json_encode(['products' => $products, 'posts' => $posts], JSON_UNESCAPED_UNICODE);
exit;