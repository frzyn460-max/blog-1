<?php
/**
 * صفحه اصلی سایت - نسخه پیشرفته
 * شامل المان‌های متحرک و جذاب
 */

require_once("./include/header.php");

$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;

if ($category_id) {
    $posts = fetchAll($db, 'SELECT * FROM posts WHERE category_id = ? ORDER BY id DESC LIMIT 4', [$category_id]);
    $products = fetchAll($db, 'SELECT * FROM product WHERE category_id = ? ORDER BY id DESC LIMIT 6', [$category_id]);
} else {
    $posts = fetchAll($db, "SELECT * FROM posts ORDER BY id DESC LIMIT 4");
    $products = fetchAll($db, "SELECT * FROM product ORDER BY id DESC LIMIT 6");
}

function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

function formatPrice($price) {
    return number_format($price) . ' تومان';
}
?>

<link rel="stylesheet" href="./css/style.css">

<!-- ═══════════════════════════════════════════════════════════
     🎨 بخش Hero
     ═══════════════════════════════════════════════════════════ -->

<section class="hero-section">
    <div class="container">
        <div class="hero-wrapper">
            
            <!-- محتوای متنی -->
            <div class="hero-content" data-aos="fade-left">
                <span class="hero-badge">🎉 بهترین قیمت‌ها</span>
                <h1 class="hero-title">
                    دنیای <span class="gradient-text">کتاب</span> 
                    <br>در یک کلیک
                </h1>
                <p class="hero-desc">
                    بیش از <strong>10,000</strong> عنوان کتاب در دسته‌بندی‌های مختلف
                    <br>
                    با بهترین کیفیت و قیمت مناسب
                </p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                        </svg>
                        مشاهده محصولات
                    </a>
                    <a href="#posts-section" class="btn-secondary">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/>
                        </svg>
                        مقالات آموزشی
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <strong>10K+</strong>
                        <span>کتاب</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>5K+</strong>
                        <span>کاربر فعال</span>
                    </div>
                    <div class="stat-divider"></div>
                    <div class="stat-item">
                        <strong>24/7</strong>
                        <span>پشتیبانی</span>
                    </div>
                </div>
            </div>

            <!-- تصویر -->
            <div class="hero-image" data-aos="fade-right">
                <div class="image-wrapper">
                    <img src="./img/25.jpg" alt="کتاب" class="main-image">
                    <div class="floating-card card-1">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5,3C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3H5M5,5H19V19H5V5M7,7V9H17V7H7M7,11V13H17V11H7M7,15V17H14V15H7Z"/>
                        </svg>
                        <div>
                            <strong>تخفیف ویژه</strong>
                            <span>تا 50%</span>
                        </div>
                    </div>
                    <div class="floating-card card-2">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
                        </svg>
                        <div>
                            <strong>ارسال رایگان</strong>
                            <span>برای خرید بالای 200 هزار</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- کانتینر اصلی -->
<div class="main-wrapper">
    
    <!-- محتوای اصلی -->
    <main class="main-content">
        
        <!-- بخش محصولات -->
        <section class="section products-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                    </svg>
                    <h2 class="section-title">محصولات ویژه</h2>
                </div>
                <a href="products.php" class="view-all-link">
                    مشاهده همه
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
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
                                        <span class="discount-badge"><?= $discount ?>% تخفیف</span>
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
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════
             🔥 تایمر تخفیف ویژه
             ═══════════════════════════════════════════════════════════ -->
        
        <section class="countdown-section" data-aos="zoom-in">
            <div class="countdown-container">
                <div class="countdown-content">
                    <div class="countdown-badge">🔥 پیشنهاد ویژه</div>
                    <h3 class="countdown-title">تخفیف ۵۰٪ تا پایان این هفته!</h3>
                    <p class="countdown-desc">فرصت طلایی برای خرید کتاب‌های مورد علاقه‌تان</p>
                    
                    <div class="countdown-timer" id="countdown">
                        <div class="time-box">
                            <span class="time-value" id="days">00</span>
                            <span class="time-label">روز</span>
                        </div>
                        <div class="time-separator">:</div>
                        <div class="time-box">
                            <span class="time-value" id="hours">00</span>
                            <span class="time-label">ساعت</span>
                        </div>
                        <div class="time-separator">:</div>
                        <div class="time-box">
                            <span class="time-value" id="minutes">00</span>
                            <span class="time-label">دقیقه</span>
                        </div>
                        <div class="time-separator">:</div>
                        <div class="time-box">
                            <span class="time-value" id="seconds">00</span>
                            <span class="time-label">ثانیه</span>
                        </div>
                    </div>

                    <a href="products.php" class="countdown-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                        </svg>
                        مشاهده محصولات تخفیف‌دار
                    </a>
                </div>

                <div class="countdown-illustration">
                    <div class="floating-books">
                        <div class="book book-1">📕</div>
                        <div class="book book-2">📗</div>
                        <div class="book book-3">📘</div>
                        <div class="book book-4">📙</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- بخش مقالات -->
        <section id="posts-section" class="section posts-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/>
                    </svg>
                    <h2 class="section-title">جدیدترین مقالات</h2>
                </div>
                <a href="posts.php" class="view-all-link">
                    مشاهده همه
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/>
                    </svg>
                </a>
            </div>

            <div class="posts-grid">
                <?php if (!empty($posts)): ?>
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
                <?php else: ?>
                    <div class="no-data-message">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z"/>
                        </svg>
                        <h3>مقاله‌ای یافت نشد!</h3>
                        <p>در حال حاضر مقاله‌ای در این دسته‌بندی وجود ندارد.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════
             🎯 بنر Call To Action
             ═══════════════════════════════════════════════════════════ -->
        
        <section class="cta-banner" data-aos="fade-up">
            <div class="cta-content">
                <div class="cta-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/>
                    </svg>
                </div>
                <div class="cta-text">
                    <h3 class="cta-title">عضویت در خبرنامه کتاب‌نت</h3>
                    <p class="cta-desc">از جدیدترین کتاب‌ها، تخفیف‌ها و اخبار دنیای کتاب باخبر شوید</p>
                </div>
                <div class="cta-form">
                    <input type="email" placeholder="ایمیل خود را وارد کنید" class="cta-input">
                    <button class="cta-button">
                        <span>عضویت</span>
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M2.01,21L23,12L2.01,3L2,10L17,12L2,14L2.01,21Z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- ═══════════════════════════════════════════════════════════
             💎 ویژگی‌های سایت
             ═══════════════════════════════════════════════════════════ -->
        
        <section class="features-section" data-aos="fade-up">
            <div class="features-grid">
                <div class="feature-card" data-aos="flip-left" data-aos-delay="100">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">ارسال رایگان</h4>
                    <p class="feature-desc">برای خریدهای بالای ۲۰۰ هزار تومان</p>
                </div>

                <div class="feature-card" data-aos="flip-left" data-aos-delay="200">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">پرداخت امن</h4>
                    <p class="feature-desc">با اطمینان خرید کنید</p>
                </div>

                <div class="feature-card" data-aos="flip-left" data-aos-delay="300">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,7H13V13H11V7M11,15H13V17H11V15Z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">پشتیبانی ۲۴/۷</h4>
                    <p class="feature-desc">همیشه در کنار شما هستیم</p>
                </div>

                <div class="feature-card" data-aos="flip-left" data-aos-delay="400">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9,11.75A1.25,1.25 0 0,0 7.75,13A1.25,1.25 0 0,0 9,14.25A1.25,1.25 0 0,0 10.25,13A1.25,1.25 0 0,0 9,11.75M15,11.75A1.25,1.25 0 0,0 13.75,13A1.25,1.25 0 0,0 15,14.25A1.25,1.25 0 0,0 16.25,13A1.25,1.25 0 0,0 15,11.75M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M12,20C7.59,20 4,16.41 4,12C4,11.71 4,11.42 4.05,11.14C6.41,10.09 8.28,8.16 9.26,5.77C11.07,8.33 14.05,10 17.42,10C18.2,10 18.95,9.91 19.67,9.74C19.88,10.45 20,11.21 20,12C20,16.41 16.41,20 12,20Z"/>
                        </svg>
                    </div>
                    <h4 class="feature-title">بهترین کیفیت</h4>
                    <p class="feature-desc">کتاب‌های اصل و با کیفیت</p>
                </div>
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
/* ═══════════════════════════════════════════════════════════
   🎨 بخش Hero
   ═══════════════════════════════════════════════════════════ */

.hero-section {
    padding: 8rem 0 4rem;
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%);
    margin-bottom: 4rem;
}

body.dark-mode .hero-section {
    background: linear-gradient(135deg, rgba(30, 58, 138, 0.03) 0%, rgba(59, 130, 246, 0.03) 100%);
}

.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.hero-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-badge {
    display: inline-block;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    animation: bounceIn 1s ease;
}

@keyframes bounceIn {
    0% { transform: scale(0); opacity: 0; }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); opacity: 1; }
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    color: var(--text-primary);
}

.gradient-text {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.hero-desc {
    font-size: 1.2rem;
    color: var(--text-secondary);
    line-height: 1.8;
    margin-bottom: 2.5rem;
}

.hero-desc strong {
    color: var(--accent-primary);
    font-weight: 700;
}

.hero-buttons {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
    padding: 1rem 2rem;
    border-radius: 15px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-primary {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    border: none;
    box-shadow: 0 10px 30px rgba(30, 58, 138, 0.3);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(30, 58, 138, 0.4);
}

.btn-secondary {
    background: transparent;
    color: var(--text-primary);
    border: 2px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--bg-primary);
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.btn-primary svg, .btn-secondary svg {
    width: 20px;
    height: 20px;
}

.hero-stats {
    display: flex;
    align-items: center;
    gap: 2rem;
    padding: 1.5rem;
    background: var(--bg-primary);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-item strong {
    font-size: 1.8rem;
    color: var(--accent-primary);
    font-weight: 800;
}

.stat-item span {
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: var(--border-color);
}

/* تصویر Hero */
.hero-image {
    position: relative;
}

.image-wrapper {
    position: relative;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

.main-image {
    width: 100%;
    max-width: 450px;
    border-radius: 30px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
}

.main-image:hover {
    transform: scale(1.05);
}

.floating-card {
    position: absolute;
    background: var(--bg-primary);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    box-shadow: var(--shadow-lg);
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: floatCard 4s ease-in-out infinite;
}

@keyframes floatCard {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.floating-card svg {
    width: 40px;
    height: 40px;
    color: var(--accent-primary);
    flex-shrink: 0;
}

.floating-card strong {
    display: block;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.floating-card span {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.card-1 {
    top: 10%;
    left: -10%;
}

.card-2 {
    bottom: 15%;
    right: -10%;
    animation-delay: 2s;
}

/* ═══════════════════════════════════════════════════════════
   🔥 تایمر تخفیف ویژه
   ═══════════════════════════════════════════════════════════ */

.countdown-section {
    margin: 4rem 0;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    border-radius: 30px;
    position: relative;
    overflow: hidden;
}

.countdown-section::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
    opacity: 0.3;
}

.countdown-container {
    position: relative;
    z-index: 10;
    display: grid;
    grid-template-columns: 1.5fr 1fr;
    gap: 3rem;
    align-items: center;
}

.countdown-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    padding: 0.5rem 1.2rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.countdown-title {
    font-size: 2.5rem;
    font-weight: 900;
    color: white;
    margin-bottom: 1rem;
    line-height: 1.2;
}

.countdown-desc {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 2rem;
}

.countdown-timer {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.time-box {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 1.5rem 1rem;
    border-radius: 15px;
    text-align: center;
    min-width: 90px;
}

.time-value {
    display: block;
    font-size: 2.5rem;
    font-weight: 900;
    color: white;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.time-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 600;
}

.time-separator {
    color: white;
    font-size: 2rem;
    font-weight: 700;
    display: flex;
    align-items: center;
}

.countdown-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: white;
    color: var(--accent-primary);
    padding: 1.2rem 2.5rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.countdown-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.countdown-btn svg {
    width: 24px;
    height: 24px;
}

.countdown-illustration {
    position: relative;
    height: 300px;
}

.floating-books {
    position: absolute;
    inset: 0;
}

.book {
    position: absolute;
    font-size: 4rem;
    animation: floatBook 3s ease-in-out infinite;
}

.book-1 {
    top: 10%;
    right: 20%;
    animation-delay: 0s;
}

.book-2 {
    top: 60%;
    right: 10%;
    animation-delay: 0.5s;
}

.book-3 {
    top: 30%;
    right: 70%;
    animation-delay: 1s;
}

.book-4 {
    bottom: 10%;
    right: 50%;
    animation-delay: 1.5s;
}

@keyframes floatBook {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) rotate(10deg);
    }
}

/* ═══════════════════════════════════════════════════════════
   🎯 بنر CTA
   ═══════════════════════════════════════════════════════════ */

.cta-banner {
    margin: 4rem 0;
    background: var(--bg-primary);
    border-radius: 30px;
    padding: 3rem;
    box-shadow: var(--shadow-lg);
    border: 2px solid var(--border-color);
}

.cta-content {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 2rem;
    align-items: center;
}

.cta-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.cta-icon svg {
    width: 40px;
    height: 40px;
}

.cta-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.cta-desc {
    color: var(--text-secondary);
    font-size: 1rem;
}

.cta-form {
    display: flex;
    gap: 1rem;
}

.cta-input {
    flex: 1;
    padding: 1rem 1.5rem;
    border: 2px solid var(--border-color);
    border-radius: 15px;
    font-size: 1rem;
    font-family: inherit;
    background: var(--bg-secondary);
    color: var(--text-primary);
    transition: all 0.3s ease;
    min-width: 250px;
}

.cta-input:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
}

.cta-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--gradient-primary);
    color: white;
    padding: 1rem 2rem;
    border: none;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
    white-space: nowrap;
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-blue);
}

.cta-button svg {
    width: 20px;
    height: 20px;
}

/* ═══════════════════════════════════════════════════════════
   💎 ویژگی‌ها
   ═══════════════════════════════════════════════════════════ */

.features-section {
    margin: 4rem 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.feature-card {
    background: var(--bg-primary);
    padding: 2rem;
    border-radius: 20px;
    text-align: center;
    border: 1px solid var(--border-color);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-blue);
}

.feature-icon {
    width: 80px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    color: white;
    transition: all 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: scale(1.1) rotate(5deg);
}

.feature-icon svg {
    width: 40px;
    height: 40px;
}

.feature-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.feature-desc {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

/* ═══════════════════════════════════════════════════════════
   📱 RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 991px) {
    .hero-wrapper {
        grid-template-columns: 1fr;
        gap: 3rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-image {
        order: -1;
        text-align: center;
    }

    .floating-card {
        display: none;
    }

    .countdown-container {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .countdown-timer {
        justify-content: center;
    }

    .countdown-illustration {
        height: 200px;
    }

    .cta-content {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .cta-form {
        flex-direction: column;
    }

    .cta-input {
        min-width: 100%;
    }
}

@media (max-width: 576px) {
    .hero-section {
        padding: 6rem 0 3rem;
    }

    .hero-title {
        font-size: 2rem;
    }

    .hero-desc {
        font-size: 1rem;
    }

    .hero-buttons {
        flex-direction: column;
        gap: 1rem;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }

    .hero-stats {
        flex-direction: column;
        gap: 1.5rem;
    }

    .stat-divider {
        width: 100%;
        height: 1px;
    }

    .countdown-title {
        font-size: 1.8rem;
    }

    .countdown-timer {
        gap: 0.5rem;
    }

    .time-box {
        min-width: 70px;
        padding: 1rem 0.5rem;
    }

    .time-value {
        font-size: 2rem;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }
}

/* انیمیشن AOS */
[data-aos="zoom-in"] {
    transform: scale(0.9);
    opacity: 0;
}

[data-aos="zoom-in"].aos-animate {
    transform: scale(1);
    opacity: 1;
}

[data-aos="flip-left"] {
    transform: perspective(2500px) rotateY(-100deg);
    opacity: 0;
}

[data-aos="flip-left"].aos-animate {
    transform: perspective(2500px) rotateY(0);
    opacity: 1;
}
</style>

<script>
// تایمر شمارش معکوس
function startCountdown() {
    const endDate = new Date();
    endDate.setDate(endDate.getDate() + 7); // 7 روز از الان

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = endDate - now;

        if (distance < 0) {
            document.getElementById('countdown').innerHTML = '<div class="time-box"><span class="time-value">پایان یافته</span></div>';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').textContent = String(days).padStart(2, '0');
        document.getElementById('hours').textContent = String(hours).padStart(2, '0');
        document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
        document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
}

// انیمیشن AOS
document.addEventListener('DOMContentLoaded', () => {
    startCountdown();

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-animate');
            }
        });
    }, observerOptions);

    document.querySelectorAll('[data-aos]').forEach(el => {
        observer.observe(el);
    });
});
</script>