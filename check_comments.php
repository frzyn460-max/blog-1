<?php
/**
 * اسکریپت بررسی و تست جداول نظرات
 * برای دیباگ مشکلات
 */

require_once("./include/config.php");
require_once("./include/db.php");

echo "<style>
body { font-family: Arial; padding: 20px; direction: rtl; }
.success { color: green; padding: 10px; background: #d4edda; margin: 10px 0; border-radius: 5px; }
.error { color: red; padding: 10px; background: #f8d7da; margin: 10px 0; border-radius: 5px; }
.info { color: blue; padding: 10px; background: #d1ecf1; margin: 10px 0; border-radius: 5px; }
table { width: 100%; border-collapse: collapse; margin: 20px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: right; }
th { background: #4CAF50; color: white; }
</style>";

echo "<h1>🔍 بررسی جداول نظرات</h1>";

// بررسی جدول comments (برای مقالات)
echo "<h2>📝 جدول comments (مقالات)</h2>";

try {
    // بررسی ساختار جدول
    $structure = $db->query("DESCRIBE comments")->fetchAll();
    echo "<div class='success'>✅ جدول comments وجود دارد</div>";
    
    echo "<h3>ساختار جدول:</h3>";
    echo "<table><tr><th>ستون</th><th>نوع</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($structure as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // تعداد نظرات
    $count = $db->query("SELECT COUNT(*) as total FROM comments")->fetch();
    echo "<div class='info'>📊 تعداد کل نظرات: {$count['total']}</div>";
    
    // نمونه نظرات
    $samples = $db->query("SELECT * FROM comments ORDER BY id DESC LIMIT 5")->fetchAll();
    if ($samples) {
        echo "<h3>آخرین نظرات:</h3>";
        echo "<table><tr><th>ID</th><th>نام</th><th>نظر</th><th>Post ID</th><th>Status</th><th>تاریخ</th></tr>";
        foreach ($samples as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['comment'], 0, 50)) . "...</td>";
            echo "<td>{$row['post_id']}</td>";
            echo "<td>" . (isset($row['status']) ? $row['status'] : 'ندارد') . "</td>";
            echo "<td>" . (isset($row['created_at']) ? $row['created_at'] : 'ندارد') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='info'>هیچ نظری ثبت نشده</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ خطا: جدول comments وجود ندارد یا مشکلی دارد<br>";
    echo "پیام خطا: " . $e->getMessage() . "</div>";
    
    // پیشنهاد ایجاد جدول
    echo "<div class='info'><strong>💡 راه حل:</strong> اجرای کوئری زیر برای ایجاد جدول:<br><br>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    post_id INT NOT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;";
    echo "</pre></div>";
}

echo "<hr>";

// بررسی جدول product_comments (برای محصولات)
echo "<h2>🛒 جدول product_comments (محصولات)</h2>";

try {
    // بررسی ساختار جدول
    $structure = $db->query("DESCRIBE product_comments")->fetchAll();
    echo "<div class='success'>✅ جدول product_comments وجود دارد</div>";
    
    echo "<h3>ساختار جدول:</h3>";
    echo "<table><tr><th>ستون</th><th>نوع</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($structure as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // تعداد نظرات
    $count = $db->query("SELECT COUNT(*) as total FROM product_comments")->fetch();
    echo "<div class='info'>📊 تعداد کل نظرات: {$count['total']}</div>";
    
    // نمونه نظرات
    $samples = $db->query("SELECT * FROM product_comments ORDER BY id DESC LIMIT 5")->fetchAll();
    if ($samples) {
        echo "<h3>آخرین نظرات:</h3>";
        echo "<table><tr><th>ID</th><th>نام</th><th>نظر</th><th>Product ID</th><th>Status</th><th>تاریخ</th></tr>";
        foreach ($samples as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['comment'], 0, 50)) . "...</td>";
            echo "<td>{$row['product_id']}</td>";
            echo "<td>" . (isset($row['status']) ? $row['status'] : 'ندارد') . "</td>";
            echo "<td>" . (isset($row['created_at']) ? $row['created_at'] : 'ندارد') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<div class='info'>هیچ نظری ثبت نشده</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='error'>❌ خطا: جدول product_comments وجود ندارد یا مشکلی دارد<br>";
    echo "پیام خطا: " . $e->getMessage() . "</div>";
    
    // پیشنهاد ایجاد جدول
    echo "<div class='info'><strong>💡 راه حل:</strong> اجرای کوئری زیر برای ایجاد جدول:<br><br>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    echo "CREATE TABLE IF NOT EXISTS product_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    comment TEXT NOT NULL,
    product_id INT NOT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX(product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;";
    echo "</pre></div>";
}

echo "<hr>";
echo "<h2>📝 تست ثبت نظر</h2>";

// تست ثبت نظر برای مقاله
if (isset($_POST['test_post_comment'])) {
    try {
        $result = $db->prepare("INSERT INTO comments (name, comment, post_id, created_at) VALUES (?, ?, ?, NOW())");
        $result->execute(['تست', 'این یک نظر تستی است', 1]);
        echo "<div class='success'>✅ نظر تستی با موفقیت در جدول comments ثبت شد!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>❌ خطا در ثبت نظر تستی: " . $e->getMessage() . "</div>";
    }
}

// تست ثبت نظر برای محصول
if (isset($_POST['test_product_comment'])) {
    try {
        $result = $db->prepare("INSERT INTO product_comments (name, comment, product_id, created_at) VALUES (?, ?, ?, NOW())");
        $result->execute(['تست', 'این یک نظر تستی است', 1]);
        echo "<div class='success'>✅ نظر تستی با موفقیت در جدول product_comments ثبت شد!</div>";
    } catch (PDOException $e) {
        echo "<div class='error'>❌ خطا در ثبت نظر تستی: " . $e->getMessage() . "</div>";
    }
}

echo "<form method='post' style='margin: 20px 0;'>";
echo "<button type='submit' name='test_post_comment' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;'>تست ثبت نظر مقاله</button>";
echo "<button type='submit' name='test_product_comment' style='padding: 10px 20px; background: #2196F3; color: white; border: none; border-radius: 5px; cursor: pointer;'>تست ثبت نظر محصول</button>";
echo "</form>";

echo "<hr>";
echo "<div class='info'><strong>📌 نکات مهم:</strong><br>";
echo "1. اگر ستون status وجود داشت، مقدار 1 برای نمایش و 0 برای مخفی است<br>";
echo "2. اگر ستون created_at وجود نداشت، باید اضافه شود<br>";
echo "3. پس از اطمینان از درستی ساختار، این فایل را حذف کنید<br>";
echo "</div>";
?>