<?php
session_start();
header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'product_id missing']);
    exit;
}

$product_id = intval($_GET['product_id']);

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'product not found in cart']);
}
