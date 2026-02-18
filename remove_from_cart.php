<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$product_id = filter_var($_GET['product_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$product_id) {
    echo json_encode(['status' => 'error', 'message' => 'شناسه نامعتبر']);
    exit();
}

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
    echo json_encode([
        'status'      => 'success',
        'message'     => 'محصول حذف شد',
        'total_items' => array_sum($_SESSION['cart'] ?? [])
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'محصول در سبد نیست']);
}