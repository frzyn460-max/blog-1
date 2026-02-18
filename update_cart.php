<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

$data       = json_decode(file_get_contents('php://input'), true);
$product_id = filter_var($data['product_id'] ?? 0, FILTER_VALIDATE_INT);
$quantity   = filter_var($data['quantity']   ?? 0, FILTER_VALIDATE_INT);

if (!$product_id || $quantity < 1) {
    echo json_encode(['status' => 'error', 'message' => 'داده نامعتبر']);
    exit();
}

$_SESSION['cart'][$product_id] = $quantity;

echo json_encode([
    'status'      => 'success',
    'message'     => 'تعداد بروزرسانی شد',
    'quantity'    => $quantity,
    'total_items' => array_sum($_SESSION['cart'])
]);