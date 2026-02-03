<?php
/**
 * فایل اتصال به دیتابیس با مدیریت خطا
 * استفاده از PDO برای امنیت بیشتر
 * 
 * @author Your Name
 * @version 2.0
 */

try {
    // ایجاد اتصال PDO با تنظیمات امنیتی
    $db = new PDO(DSN, DB_USER, DB_PASS, [
        // تنظیم حالت خطا به Exception
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        
        // غیرفعال کردن Prepared Statement Emulation برای امنیت بیشتر
        PDO::ATTR_EMULATE_PREPARES => false,
        
        // تنظیم حالت Fetch پیش‌فرض به Associative Array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // غیرفعال کردن تبدیل خودکار به رشته
        PDO::ATTR_STRINGIFY_FETCHES => false,
        
        // استفاده از اتصال پایدار برای بهبود عملکرد
        PDO::ATTR_PERSISTENT => false
    ]);
    
    // تنظیم charset برای جلوگیری از مشکلات امنیتی
    $db->exec("SET NAMES utf8mb4 COLLATE utf8mb4_persian_ci");
    
} catch (PDOException $e) {
    // مدیریت خطای اتصال
    
    // در محیط توسعه می‌توانید خطا را نمایش دهید
    if (error_reporting() !== 0) {
        die("خطا در اتصال به دیتابیس: " . $e->getMessage());
    }
    
    // در محیط تولید یک پیام کلی نمایش دهید
    die("متأسفانه مشکلی در اتصال به سرور پیش آمده. لطفاً بعداً تلاش کنید.");
}

/**
 * تابع کمکی برای اجرای کوئری امن
 * 
 * @param PDO $db اتصال دیتابیس
 * @param string $query کوئری SQL
 * @param array $params پارامترها
 * @return PDOStatement نتیجه کوئری
 */
function executeQuery($db, $query, $params = []) {
    try {
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        // لاگ کردن خطا
        error_log("Database Error: " . $e->getMessage());
        
        // بازگشت false در صورت خطا
        return false;
    }
}

/**
 * تابع کمکی برای دریافت یک ردیف از دیتابیس
 * 
 * @param PDO $db اتصال دیتابیس
 * @param string $query کوئری SQL
 * @param array $params پارامترها
 * @return array|false نتیجه یا false
 */
function fetchOne($db, $query, $params = []) {
    $stmt = executeQuery($db, $query, $params);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * تابع کمکی برای دریافت چند ردیف از دیتابیس
 * 
 * @param PDO $db اتصال دیتابیس
 * @param string $query کوئری SQL
 * @param array $params پارامترها
 * @return array نتایج
 */
function fetchAll($db, $query, $params = []) {
    $stmt = executeQuery($db, $query, $params);
    return $stmt ? $stmt->fetchAll() : [];
}