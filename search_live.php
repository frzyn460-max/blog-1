<?php
/**
 * search_live.php
 * Live search endpoint — returns JSON {products:[...], posts:[...]}
 * مسیر: /search_live.php (کنار index.php)
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

require_once(__DIR__ . "/include/config.php");
require_once(__DIR__ . "/include/db.php");

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if (mb_strlen($q) < 1) {
    echo json_encode(['products' => [], 'posts' => []]);
    exit;
}

$like = '%' . $q . '%';

// ── محصولات ──
$products_raw = fetchAll($db,
    "SELECT id, name, `new-price` AS new_price, price AS old_price, pic
     FROM product
     WHERE name LIKE ? OR description LIKE ?
     ORDER BY id DESC
     LIMIT 5",
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

function clean_val($v) {
    return htmlspecialchars_decode(strip_tags((string)$v));
}

$products = [];
foreach ($products_raw as $p) {
    $products[] = [
        'id'        => (int)$p['id'],
        'name'      => clean_val($p['name']),
        'price'     => (int)$p['new_price'],
        'old_price' => (int)$p['old_price'],
        'pic'       => basename($p['pic']),
    ];
}

$posts = [];
foreach ($posts_raw as $p) {
    $posts[] = [
        'id'     => (int)$p['id'],
        'title'  => clean_val($p['title']),
        'image'  => basename($p['image']),
        'author' => clean_val($p['author']),
    ];
}

echo json_encode([
    'products' => $products,
    'posts'    => $posts
], JSON_UNESCAPED_UNICODE);
exit;