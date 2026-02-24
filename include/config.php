<?php
/**
 * فایل پیکربندی اصلی پروژه
 * شامل تنظیمات دیتابیس و متغیرهای محیطی
 * 
 * @author Your Name
 * @version 2.0
 */

// غیرفعال کردن نمایش خطاها در محیط تولید
// در محیط توسعه این خط را کامنت کنید
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// تنظیمات دیتابیس
define("DB_HOST", "localhost");
define("DB_NAME", "blog_webprog");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_CHARSET", "utf8mb4");

// ساخت DSN برای اتصال PDO
define("DSN", sprintf(
    "mysql:host=%s;dbname=%s;charset=%s",
    DB_HOST,
    DB_NAME,
    DB_CHARSET
));

// تنظیمات امنیتی
define("SECURE_KEY", "your-secret-key-here-change-this"); // برای رمزنگاری و CSRF
define("SESSION_LIFETIME", 7200); // مدت زمان session به ثانیه (2 ساعت)

// تنظیمات سایت
define("SITE_NAME", "کتاب نت");
// در include/config.php این خط رو پیدا کن و درست کن:
define("SITE_URL", "http://localhost/php/blog-1");
//                  ↑ مسیر دقیق سایت شما از URL بار
define("ITEMS_PER_PAGE", 6); // تعداد آیتم در هر صفحه

// مسیرهای آپلود
define("UPLOAD_PATH_PRODUCTS", __DIR__ . "/../upload/products/");
define("UPLOAD_PATH_POSTS", __DIR__ . "/../upload/posts/");

// تنظیمات تایم زون
date_default_timezone_set('Asia/Tehran');

// فعال‌سازی session با تنظیمات امنیتی
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // در محیط تولید با HTTPS این را 1 کنید
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

/**
 * تابع کمکی برای خروج ایمن از HTML
 * 
 * @param string $string رشته ورودی
 * @return string رشته خروجی امن
 */
function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * تابع کمکی برای تولید توکن CSRF
 * 
 * @return string توکن CSRF
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * تابع کمکی برای اعتبارسنجی توکن CSRF
 * 
 * @param string $token توکن دریافتی
 * @return bool صحیح یا غلط
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * تابع کمکی برای ریدایرکت
 * 
 * @param string $url آدرس مقصد
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}