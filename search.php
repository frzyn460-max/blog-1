<?php
/**
 * صفحه جستجو
 * نمایش نتایج جستجو در محصولات و مقالات
 */

// فراخوانی هدر
require_once("./include/header.php");

// دریافت کلمه کلیدی جستجو
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// متغیرهای نتایج
$products = [];
$posts = [];
$total_results = 0;

// اگر کلمه کلیدی وارد شده
if (!empty($keyword)) {
    // جستجو در محصولات
    $products_query = "SELECT * FROM product WHERE name LIKE ? OR description LIKE ? ORDER BY id DESC";
    $products = fetchAll($db, $products_query, ["%{$keyword}%", "%{$keyword}%"]);
    
    // جستجو در مقالات
    $posts_query = "SELECT * FROM posts WHERE title LIKE ? OR body LIKE ? ORDER BY id DESC";
    $posts = fetchAll($db, $posts_query, ["%{$keyword}%", "%{$keyword}%"]);
    
    // محاسبه تعداد کل نتایج
    $total_results = count($products) + count($posts);
}

// تابع کمکی
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

function formatPrice($price) {
    return number_format($price) . ' تومان';
}
?>

<!-- بخش هدر جستجو -->
<section class="search-hero">
    <div class="container">
        <div class="search-header" data-aos="fade-up">
            <div class="search-icon-wrapper">
                <svg class="search-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
            </div>
            <div class="search-info">
                <h1 class="search-title">
                    <?php if (!empty($keyword)): ?>
                        نتایج جستجو برای: <span class="keyword">"<?= escape($keyword) ?>"</span>
                    <?php else: ?>
                        جستجو در سایت
                    <?php endif; ?>
                </h1>
                <?php if (!empty($keyword)): ?>
                    <p class="search-count">
                        <strong><?= $total_results ?></strong> نتیجه یافت شد
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- کانتینر اصلی -->
<div class="page-wrapper">
    
    <!-- محتوای اصلی -->
    <main class="main-content">
        
        <?php if (empty($keyword)): ?>
            <!-- پیام جستجو خالی -->
            <div class="empty-search" data-aos="fade-up">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                </svg>
                <h3>لطفاً کلمه کلیدی را وارد کنید</h3>
                <p>برای جستجو از فرم بالای صفحه استفاده کنید</p>
            </div>
        
        <?php elseif ($total_results === 0): ?>
            <!-- پیام نتیجه نیافته -->
            <div class="no-results" data-aos="fade-up">
                <svg viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
                </svg>
                <h3>نتیجه‌ای یافت نشد!</h3>
                <p>متأسفانه چیزی با کلمه کلیدی "<?= escape($keyword) ?>" پیدا نشد.</p>
                <p class="suggestion">پیشنهادات:</p>
                <ul class="suggestion-list">
                    <li>کلمات کلیدی دیگری امتحان کنید</li>
                    <li>املای کلمات را بررسی کنید</li>
                    <li>از کلمات کلی‌تر استفاده کنید</li>
                </ul>
            </div>
        
        <?php else: ?>
            
            <!-- بخش محصولات -->
            <?php if (!empty($products)): ?>
                <section class="section products-section" data-aos="fade-up">
                    <div class="section-header">
                        <div class="section-title-wrapper">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                            </svg>
                            <h2 class="section-title">محصولات (<?= count($products) ?>)</h2>
                        </div>
                    </div>

                    <div class="products-grid">
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
                    </div>
                </section>
            <?php endif; ?>

            <!-- بخش مقالات -->
            <?php if (!empty($posts)): ?>
                <section class="section posts-section" data-aos="fade-up">
                    <div class="section-header">
                        <div class="section-title-wrapper">
                            <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/>
                            </svg>
                            <h2 class="section-title">مقالات (<?= count($posts) ?>)</h2>
                        </div>
                    </div>

                    <div class="posts-grid">
                        <?php foreach ($posts as $index => $post): ?>
                            <?php
                            $post_category = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]);
                            ?>
                            <article class="post-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                                <div class="post-image-wrapper">
                                    <img src="./upload/posts/<?= escape($post['image']) ?>" 
                                         alt="<?= escape($post['title']) ?>" 
                                         class="post-image"
                                         loading="lazy">
                                    <span class="post-category-badge">
                                        <?= escape($post_category['title'] ?? 'نامشخص') ?>
                                    </span>
                                </div>
                                <div class="post-content">
                                    <h3 class="post-title">
                                        <a href="single.php?post=<?= $post['id'] ?>">
                                            <?= escape($post['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="post-excerpt">
                                        <?= escape(truncateText($post['body'], 120)) ?>
                                    </p>
                                    <div class="post-footer">
                                        <div class="post-author">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                            </svg>
                                            <?= escape($post['author']) ?>
                                        </div>
                                        <a href="single.php?post=<?= $post['id'] ?>" class="read-more">
                                            ادامه مطلب
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

        <?php endif; ?>

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

    /* ===== بخش هدر جستجو ===== */
    .search-hero {
        padding: 50px;
        margin-top: 20px;
        border-radius: 12px;
        background:linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        position: relative;
        overflow: hidden;
    }

    .search-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
        opacity: 0.3;
    }

    .search-header {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 2rem;
        color: white;
    }

    .search-icon-wrapper {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .search-icon {
        width: 50px;
        height: 50px;
    }

    .search-info {
        flex: 1;
    }

    .search-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .keyword {
        color: #ffd700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .search-count {
        font-size: 1.2rem;
        opacity: 0.95;
    }

    /* ===== کانتینر اصلی ===== */
    .page-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 4rem 1.5rem;
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

    /* ===== پیام‌های خالی ===== */
    .empty-search,
    .no-results {
        text-align: center;
        padding: 5rem 2rem;
        background: var(--bg-primary);
        border-radius: 20px;
        border: 2px dashed var(--border-color);
    }

    .empty-search svg,
    .no-results svg {
        width: 100px;
        height: 100px;
        color: var(--text-secondary);
        margin-bottom: 2rem;
        opacity: 0.5;
    }

    .empty-search h3,
    .no-results h3 {
        font-size: 1.8rem;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }

    .empty-search p,
    .no-results p {
        color: var(--text-secondary);
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .suggestion {
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .suggestion-list {
        list-style: none;
        padding: 0;
        display: inline-block;
        text-align: right;
    }

    .suggestion-list li {
        color: var(--text-secondary);
        padding: 0.5rem 0;
        position: relative;
        padding-right: 1.5rem;
    }

    .suggestion-list li::before {
        content: '✓';
        position: absolute;
        right: 0;
        color: var(--accent-primary);
        font-weight: 700;
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

    /* ===== گرید مقالات ===== */
    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    .post-card {
        background: var(--bg-primary);
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        cursor: pointer;
    }

    .post-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-lg);
    }

    .post-image-wrapper {
        position: relative;
        overflow: hidden;
        padding-top: 60%;
    }

    .post-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .post-card:hover .post-image {
        transform: scale(1.1);
    }

    .post-category-badge {
        position: absolute;
        bottom: 15px;
        right: 15px;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 10;
    }

    .post-content {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .post-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .post-title a {
        color: var(--text-primary);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .post-title a:hover {
        color: var(--accent-primary);
    }

    .post-excerpt {
        color: var(--text-secondary);
        line-height: 1.8;
        margin-bottom: 1.5rem;
        flex: 1;
    }

    .post-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 1rem;
        border-top: 1px solid var(--border-color);
    }

    .post-author {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
    }

    .post-author svg {
        width: 18px;
        height: 18px;
        color: var(--accent-primary);
    }

    .read-more {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        color: var(--accent-primary);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .read-more:hover {
        gap: 0.6rem;
    }

    .read-more svg {
        width: 18px;
        height: 18px;
    }

    /* ===== Dark Mode ===== */
    body.dark-mode .search-hero {
        background:linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
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
        .search-hero {
            padding: 10rem 0 3rem;
        }

        .search-header {
            flex-direction: column;
            text-align: center;
        }

        .search-title {
            font-size: 2rem;
        }

        .products-grid,
        .posts-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .search-hero {
            padding: 8rem 0 2rem;
        }

        .search-icon-wrapper {
            width: 80px;
            height: 80px;
        }

        .search-icon {
            width: 40px;
            height: 40px;
        }

        .search-title {
            font-size: 1.5rem;
        }

        .search-count {
            font-size: 1rem;
        }

        .page-wrapper {
            padding: 3rem 1rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .products-grid,
        .posts-grid {
            grid-template-columns: 1fr;
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

        // هایلایت کردن کلمه کلیدی در نتایج
        const keyword = '<?= addslashes($keyword) ?>';
        if (keyword) {
            const regex = new RegExp(`(${keyword})`, 'gi');
            
            document.querySelectorAll('.product-name a, .post-title a').forEach(function(el) {
                const text = el.textContent;
                if (regex.test(text)) {
                    el.innerHTML = text.replace(regex, '<mark style="background: #ffd700; padding: 2px 4px; border-radius: 3px;">$1</mark>');
                }
            });
        }
    });
</script>