<?php
/**
 * صفحه محصولات
 * نمایش تمام محصولات با فیلتر و جستجو
 */

// فراخوانی هدر
require_once("./include/header.php");

// دریافت پارامترهای فیلتر
$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// آرایه دسته‌بندی‌های مجاز (کتاب‌ها)
$allowed_categories = [14, 15, 16, 17, 18];

// ساخت کوئری پایه
$query = "SELECT * FROM product WHERE category_id IN (" . implode(',', array_fill(0, count($allowed_categories), '?')) . ")";
$params = $allowed_categories;

// اضافه کردن فیلتر دسته‌بندی
if ($category_id && in_array($category_id, $allowed_categories)) {
    $query = "SELECT * FROM product WHERE category_id = ?";
    $params = [$category_id];
}

// اضافه کردن جستجو
if ($search) {
    if ($category_id) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
    } else {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
    }
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$query .= " ORDER BY id DESC";

// اجرای کوئری
$products = fetchAll($db, $query, $params);

// تابع کمکی برای کوتاه کردن متن
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

// تابع کمکی برای فرمت قیمت
function formatPrice($price) {
    return number_format($price) . ' تومان';
}
?>

<!-- کانتینر اصلی -->
<div class="page-wrapper">
    
    <!-- محتوای اصلی -->
    <main class="main-content">
        
        <!-- بخش محصولات -->
        <section class="section products-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                    </svg>
                    <h2 class="section-title">
                        <?php if ($category_id): ?>
                            <?php
                            $cat_info = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$category_id]);
                            echo escape($cat_info['title']);
                            ?>
                        <?php elseif ($search): ?>
                            نتایج جستجو: "<?= escape($search) ?>"
                        <?php else: ?>
                            تمام محصولات
                        <?php endif; ?>
                    </h2>
                </div>
            </div>

            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <?php
                        $product_category = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$product['category_id']]);
                        ?>
                        <article class="product-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            <div class="product-image-wrapper">
                                <img src="./upload/products/<?= escape($product['pic']) ?>" 
                                     alt="<?= escape($product['name']) ?>" 
                                     class="product-image"
                                     loading="lazy">
                                <div class="product-badges">
                                    <?php if ($product['price'] != $product['new-price']): ?>
                                        <?php 
                                        $discount = round((($product['price'] - $product['new-price']) / $product['price']) * 100);
                                        ?>
                                        <span class="badge discount-badge"><?= $discount ?>% تخفیف</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-overlay">
                                    <a href="single_product.php?product=<?= $product['id'] ?>" class="quick-view">
                                        مشاهده سریع
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-category">
                                    <?= escape($product_category['title'] ?? 'نامشخص') ?>
                                </span>
                                <h3 class="product-name">
                                    <a href="single_product.php?product=<?= $product['id'] ?>">
                                        <?= escape($product['name']) ?>
                                    </a>
                                </h3>
                                <div class="product-pricing">
                                    <?php if ($product['price'] != $product['new-price']): ?>
                                        <span class="old-price"><?= formatPrice($product['price']) ?></span>
                                    <?php endif; ?>
                                    <span class="new-price"><?= formatPrice($product['new-price']) ?></span>
                                </div>
                                <a href="single_product.php?product=<?= $product['id'] ?>" class="btn-add-cart">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                                    </svg>
                                    افزودن به سبد
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data-message">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                        </svg>
                        <h3>محصولی یافت نشد!</h3>
                        <p>در حال حاضر محصولی در این دسته‌بندی وجود ندارد.</p>
                        <a href="products.php" class="btn-back">بازگشت به همه محصولات</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- سایدبار -->
    <aside class="sidebar">
        <?php require_once("./include/sidebar.php"); ?>
    </aside>

</div>

<?php require_once("./include/footer.php"); ?>

<style>
    /* ===== تنظیمات پایه ===== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Vazirmatn', Tahoma, sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        overflow-x: hidden;
    }

    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    /* ===== کانتینر اصلی ===== */
    .page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 10rem 1.5rem 4rem;
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 3rem;
        align-items: start;
    }

    /* ===== بخش‌ها ===== */
    .section {
        margin-bottom: 4rem;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
    }

    .section-title-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-icon {
        width: 35px;
        height: 35px;
        color: var(--accent-primary);
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    /* ===== گرید محصولات ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 2rem;
    }

    .product-card {
        background: var(--bg-primary);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        cursor: pointer;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .product-image-wrapper {
        position: relative;
        overflow: hidden;
        padding-top: 100%;
    }

    .product-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.1);
    }

    .product-badges {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
    }

    .discount-badge {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
    }

    .product-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .product-card:hover .product-overlay {
        opacity: 1;
    }

    .quick-view {
        background: white;
        color: var(--accent-primary);
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        transform: translateY(20px);
        transition: transform 0.3s ease;
    }

    .product-card:hover .quick-view {
        transform: translateY(0);
    }

    .product-info {
        padding: 1.5rem;
    }

    .product-category {
        display: inline-block;
        background: rgba(102, 126, 234, 0.1);
        color: var(--accent-primary);
        padding: 0.3rem 0.8rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 0.8rem;
    }

    .product-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .product-name a {
        color: var(--text-primary);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .product-name a:hover {
        color: var(--accent-primary);
    }

    .product-pricing {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.2rem;
    }

    .old-price {
        font-size: 0.9rem;
        color: var(--text-secondary);
        text-decoration: line-through;
    }

    .new-price {
        font-size: 1.3rem;
        font-weight: 800;
        color: #10b981;
    }

    .btn-add-cart {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-hover) 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .btn-add-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }

    .btn-add-cart svg {
        width: 20px;
        height: 20px;
    }

    /* ===== پیام بدون داده ===== */
    .no-data-message {
        grid-column: 1 / -1;
        text-align: center;
        padding: 4rem 2rem;
        background: var(--bg-primary);
        border-radius: 20px;
        border: 2px dashed var(--border-color);
    }

    .no-data-message svg {
        width: 80px;
        height: 80px;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }

    .no-data-message h3 {
        font-size: 1.5rem;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .no-data-message p {
        color: var(--text-secondary);
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .btn-back {
        display: inline-block;
        padding: 1rem 2rem;
        background: var(--accent-primary);
        color: white;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }

    /* ===== Dark Mode ===== */
    body.dark-mode {
        background: var(--bg-secondary);
    }

    /* ===== انیمیشن AOS ===== */
    [data-aos] {
        opacity: 0;
        transition-property: transform, opacity;
        transition-duration: 0.6s;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-aos].aos-animate {
        opacity: 1;
    }

    [data-aos="fade-up"] {
        transform: translateY(30px);
    }

    [data-aos="fade-up"].aos-animate {
        transform: translateY(0);
    }

    [data-aos="fade-left"] {
        transform: translateX(-30px);
    }

    [data-aos="fade-left"].aos-animate {
        transform: translateX(0);
    }

    [data-aos="fade-right"] {
        transform: translateX(30px);
    }

    [data-aos="fade-right"].aos-animate {
        transform: translateX(0);
    }

    /* ===== Responsive ===== */
    @media (max-width: 1200px) {
        .page-wrapper {
            grid-template-columns: 1fr;
        }

        .sidebar {
            order: 2;
        }
    }

    @media (max-width: 991px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .page-wrapper {
            padding: 8rem 1.5rem 4rem;
        }
    }

    @media (max-width: 576px) {
        .section-title {
            font-size: 1.5rem;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .page-wrapper {
            padding: 7rem 1rem 3rem;
        }
    }
</style>

<script>
    // اسکریپت انیمیشن AOS
    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('aos-animate');
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-aos]').forEach(function(el) {
            observer.observe(el);
        });
    });
</script>