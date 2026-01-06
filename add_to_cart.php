<?php
session_start();
header('Content-Type: application/json');

// چک کردن وجود product_id در درخواست
if (isset($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // بررسی مقدار product_id معتبر است یا خیر
    if ($product_id <= 0) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'شناسه محصول نامعتبر است']);
        exit();
    }

    // اگر سبد خرید وجود ندارد، آن را ایجاد می‌کنیم
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // اگر محصول قبلا در سبد بوده، تعداد آن را افزایش می‌دهیم، وگرنه مقدار 1 قرار می‌دهیم
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }

    // پاسخ موفقیت آمیز
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'محصول به سبد خرید افزوده شد',
        'cart' => $_SESSION['cart']
    ]);
} else {
    // اگر product_id ارسال نشده باشد
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'شناسه محصول ارسال نشده است']);
}
?>
