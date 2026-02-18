<?php
/**
 * افزودن محصول به سبد خرید
 * با خروجی JSON
 */

// شروع session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تنظیم header برای JSON
header('Content-Type: application/json; charset=utf-8');

// دریافت شناسه محصول
$product_id = isset($_GET['product_id']) ? filter_var($_GET['product_id'], FILTER_VALIDATE_INT) : 0;

// بررسی معتبر بودن
if (!$product_id || $product_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'شناسه محصول نامعتبر است'
    ]);
    exit();
}

// ایجاد سبد خرید اگر وجود نداشته باشد
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// افزودن یا افزایش تعداد
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

// محاسبه تعداد کل
$total_items = array_sum($_SESSION['cart']);

// پاسخ موفق
echo json_encode([
    'success' => true,
    'message' => 'محصول با موفقیت به سبد خرید اضافه شد',
    'product_id' => $product_id,
    'quantity' => $_SESSION['cart'][$product_id],
    'total_items' => $total_items
]);
exit();