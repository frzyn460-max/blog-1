<?php
require_once("./include/header.php");

$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
if ($category_id) {
    $posts    = fetchAll($db, 'SELECT * FROM posts WHERE category_id = ? ORDER BY id DESC LIMIT 4',   [$category_id]);
    $products = fetchAll($db, 'SELECT * FROM product WHERE category_id = ? ORDER BY id DESC LIMIT 6', [$category_id]);
} else {
    $posts    = fetchAll($db, "SELECT * FROM posts ORDER BY id DESC LIMIT 4");
    $products = fetchAll($db, "SELECT * FROM product ORDER BY id DESC LIMIT 6");
}
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}
function formatPrice($price) { return number_format($price) . ' تومان'; }
?>

<link rel="stylesheet" href="./css/style.css">

<!-- Hero -->
<section class="hero-section">
    <div class="container">
        <div class="hero-wrapper">
            <div class="hero-content" data-aos="fade-left">
                <span class="hero-badge">🎉 بهترین قیمت‌ها</span>
                <h1 class="hero-title">دنیای <span class="gradient-text">کتاب</span> <br>در یک کلیک</h1>
                <p class="hero-desc">بیش از <strong>10,000</strong> عنوان کتاب در دسته‌بندی‌های مختلف<br>با بهترین کیفیت و قیمت مناسب</p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/></svg>
                        مشاهده محصولات
                    </a>
                    <a href="#posts-section" class="btn-secondary">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M14,3V5H17.59L7.76,14.83L9.17,16.24L19,6.41V10H21V3M19,19H5V5H12V3H5C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V12H19V19Z"/></svg>
                        مقالات آموزشی
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item"><strong>10K+</strong><span>کتاب</span></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item"><strong>5K+</strong><span>کاربر فعال</span></div>
                    <div class="stat-divider"></div>
                    <div class="stat-item"><strong>24/7</strong><span>پشتیبانی</span></div>
                </div>
            </div>
            <div class="hero-image" data-aos="fade-right">
                <div class="image-wrapper">
                    <img src="./img/25.jpg" alt="کتاب" class="main-image">
                    <div class="floating-card card-1">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M5,3C3.89,3 3,3.9 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3H5M5,5H19V19H5V5M7,7V9H17V7H7M7,11V13H17V11H7M7,15V17H14V15H7Z"/></svg>
                        <div><strong>تخفیف ویژه</strong><span>تا 50%</span></div>
                    </div>
                    <div class="floating-card card-2">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/></svg>
                        <div><strong>ارسال رایگان</strong><span>برای خرید بالای 200 هزار</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- main -->
<div class="main-wrapper">
    <main class="main-content">

        <!-- محصولات -->
        <section class="section products-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/></svg>
                    <h2 class="section-title">محصولات ویژه</h2>
                </div>
                <a href="products.php" class="view-all-link">مشاهده همه <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg></a>
            </div>
            <div class="products-grid">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $index => $product): ?>
                        <?php $cat = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$product['category_id']]); ?>
                        <article class="product-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            <div class="product-image-wrapper">
                                <img src="./upload/products/<?= escape($product['pic']) ?>" alt="<?= escape($product['name']) ?>" class="product-image" loading="lazy">
                                <div class="product-badges">
                                    <?php if ($product['price'] != $product['new-price']): ?>
                                        <?php $disc = round((($product['price']-$product['new-price'])/$product['price'])*100); ?>
                                        <span class="discount-badge"><?= $disc ?>% تخفیف</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-overlay">
                                    <a href="single_product.php?product=<?= $product['id'] ?>" class="quick-view">مشاهده سریع</a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?= escape($cat['title'] ?? 'نامشخص') ?></span>
                                <h3 class="product-name"><a href="single_product.php?product=<?= $product['id'] ?>"><?= escape($product['name']) ?></a></h3>
                                <div class="product-pricing">
                                    <?php if ($product['price'] != $product['new-price']): ?>
                                        <span class="old-price"><?= formatPrice($product['price']) ?></span>
                                    <?php endif; ?>
                                    <span class="new-price"><?= formatPrice($product['new-price']) ?></span>
                                </div>
                                <a href="single_product.php?product=<?= $product['id'] ?>" class="btn-add-cart">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/></svg>
                                    افزودن به سبد
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data-message"><h3>محصولی یافت نشد!</h3></div>
                <?php endif; ?>
            </div>
        </section>

        <!-- تایمر -->
        <section class="countdown-section" data-aos="zoom-in">
            <div class="countdown-container">
                <div class="countdown-content">
                    <div class="countdown-badge">🔥 پیشنهاد ویژه</div>
                    <h3 class="countdown-title">تخفیف ۵۰٪ تا پایان این هفته!</h3>
                    <p class="countdown-desc">فرصت طلایی برای خرید کتاب‌های مورد علاقه‌تان</p>
                    <div class="countdown-timer" id="countdown">
                        <div class="time-box"><span class="time-value" id="days">00</span><span class="time-label">روز</span></div>
                        <div class="time-separator">:</div>
                        <div class="time-box"><span class="time-value" id="hours">00</span><span class="time-label">ساعت</span></div>
                        <div class="time-separator">:</div>
                        <div class="time-box"><span class="time-value" id="minutes">00</span><span class="time-label">دقیقه</span></div>
                        <div class="time-separator">:</div>
                        <div class="time-box"><span class="time-value" id="seconds">00</span><span class="time-label">ثانیه</span></div>
                    </div>
                    <a href="products.php" class="countdown-btn">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/></svg>
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

        <!-- مقالات -->
        <section id="posts-section" class="section posts-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor"><path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/></svg>
                    <h2 class="section-title">جدیدترین مقالات</h2>
                </div>
                <a href="posts.php" class="view-all-link">مشاهده همه <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8.59,16.58L13.17,12L8.59,7.41L10,6L16,12L10,18L8.59,16.58Z"/></svg></a>
            </div>
            <div class="posts-grid">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $index => $post): ?>
                        <?php $pcat = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]); ?>
                        <article class="post-card" data-aos="fade-up" data-aos-delay="<?= $index * 100 ?>">
                            <div class="post-image-wrapper">
                                <img src="./upload/posts/<?= escape($post['image']) ?>" alt="<?= escape($post['title']) ?>" class="post-image" loading="lazy">
                                <span class="post-category-badge"><?= escape($pcat['title'] ?? 'نامشخص') ?></span>
                            </div>
                            <div class="post-content">
                                <h3 class="post-title"><a href="single.php?post=<?= $post['id'] ?>"><?= escape($post['title']) ?></a></h3>
                                <p class="post-excerpt"><?= escape(truncateText($post['body'], 120)) ?></p>
                                <div class="post-footer">
                                    <div class="post-author">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg>
                                        <?= escape($post['author']) ?>
                                    </div>
                                    <a href="single.php?post=<?= $post['id'] ?>" class="read-more">ادامه مطلب <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg></a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-data-message"><h3>مقاله‌ای یافت نشد!</h3></div>
                <?php endif; ?>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-banner" data-aos="fade-up">
            <div class="cta-content">
                <div class="cta-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z"/></svg>
                </div>
                <div class="cta-text">
                    <h3 class="cta-title">عضویت در خبرنامه کتاب‌نت</h3>
                    <p class="cta-desc">از جدیدترین کتاب‌ها، تخفیف‌ها و اخبار دنیای کتاب باخبر شوید</p>
                </div>
                <div class="cta-form">
                    <input type="email" placeholder="ایمیل خود را وارد کنید" class="cta-input">
                    <button class="cta-button"><span>عضویت</span><svg viewBox="0 0 24 24" fill="currentColor"><path d="M2.01,21L23,12L2.01,3L2,10L17,12L2,14L2.01,21Z"/></svg></button>
                </div>
            </div>
        </section>

        <!-- ویژگی‌ها -->
        <section class="features-section" data-aos="fade-up">
            <div class="features-grid">
                <div class="feature-card" data-aos="flip-left" data-aos-delay="100">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/></svg></div>
                    <h4 class="feature-title">ارسال رایگان</h4>
                    <p class="feature-desc">برای خریدهای بالای ۲۰۰ هزار تومان</p>
                </div>
                <div class="feature-card" data-aos="flip-left" data-aos-delay="200">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/></svg></div>
                    <h4 class="feature-title">پرداخت امن</h4>
                    <p class="feature-desc">با اطمینان خرید کنید</p>
                </div>
                <div class="feature-card" data-aos="flip-left" data-aos-delay="300">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M11,7H13V13H11V7M11,15H13V17H11V15Z"/></svg></div>
                    <h4 class="feature-title">پشتیبانی ۲۴/۷</h4>
                    <p class="feature-desc">همیشه در کنار شما هستیم</p>
                </div>
                <div class="feature-card" data-aos="flip-left" data-aos-delay="400">
                    <div class="feature-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9,11.75A1.25,1.25 0 0,0 7.75,13A1.25,1.25 0 0,0 9,14.25A1.25,1.25 0 0,0 10.25,13A1.25,1.25 0 0,0 9,11.75M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/></svg></div>
                    <h4 class="feature-title">بهترین کیفیت</h4>
                    <p class="feature-desc">کتاب‌های اصل و با کیفیت</p>
                </div>
            </div>
        </section>

    </main>
    <aside class="sidebar"><?php require_once("./include/sidebar.php"); ?></aside>
</div>
<?php require_once("./include/footer.php"); ?>

<style>
/* ══════════════════════════════════════════════════
   استایل‌های index.php  
   ══════════════════════════════════════════════════ */
.container { max-width:1400px; margin:0 auto; padding:0 1.5rem; }

/* Hero */
.hero-section { padding:8rem 0 4rem; background:linear-gradient(135deg,rgba(30,58,138,.05) 0%,rgba(59,130,246,.05) 100%); margin-bottom:4rem; }
body.dark-mode .hero-section { background:linear-gradient(135deg,rgba(30,58,138,.03) 0%,rgba(59,130,246,.03) 100%); }
.hero-wrapper { display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:center; }
.hero-badge { display:inline-block; background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:white; padding:.5rem 1.2rem; border-radius:50px; font-size:.9rem; font-weight:600; margin-bottom:1.5rem; }
.hero-title { font-size:3.5rem; font-weight:800; line-height:1.2; margin-bottom:1.5rem; color:var(--text-primary); }
.gradient-text { background:linear-gradient(135deg,#1e3a8a,#3b82f6); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
.hero-desc { font-size:1.2rem; color:var(--text-secondary); line-height:1.8; margin-bottom:2.5rem; }
.hero-desc strong { color:var(--accent-primary); }
.hero-buttons { display:flex; gap:1.5rem; margin-bottom:3rem; flex-wrap:wrap; }
.btn-primary,.btn-secondary { padding:1rem 2rem; border-radius:15px; font-weight:600; font-size:1rem; text-decoration:none; display:inline-flex; align-items:center; gap:.5rem; transition:all .3s ease; font-family:inherit; }
.btn-primary { background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:white; border:none; box-shadow:0 10px 30px rgba(30,58,138,.3); }
.btn-primary:hover { transform:translateY(-3px); box-shadow:0 15px 40px rgba(30,58,138,.4); }
.btn-secondary { background:transparent; color:var(--text-primary); border:2px solid var(--border-color); }
.btn-secondary:hover { background:var(--bg-primary); transform:translateY(-3px); box-shadow:var(--shadow-md); }
.btn-primary svg,.btn-secondary svg { width:20px; height:20px; }
.hero-stats { display:flex; align-items:center; gap:2rem; padding:1.5rem; background:var(--bg-primary); border-radius:20px; box-shadow:var(--shadow-md); }
.stat-item { display:flex; flex-direction:column; align-items:center; }
.stat-item strong { font-size:1.8rem; color:var(--accent-primary); font-weight:800; }
.stat-item span { font-size:.9rem; color:var(--text-secondary); }
.stat-divider { width:1px; height:40px; background:var(--border-color); }
.hero-image { position:relative; }
.image-wrapper { position:relative; animation:float 6s ease-in-out infinite; }
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-20px)} }
.main-image { width:100%; max-width:450px; border-radius:30px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.floating-card { position:absolute; background:var(--bg-primary); padding:1rem 1.5rem; border-radius:15px; box-shadow:var(--shadow-lg); display:flex; align-items:center; gap:1rem; animation:floatCard 4s ease-in-out infinite; }
.floating-card svg { width:40px; height:40px; color:var(--accent-primary); flex-shrink:0; }
.floating-card strong { display:block; font-size:.9rem; color:var(--text-primary); }
.floating-card span { font-size:.8rem; color:var(--text-secondary); }
.card-1 { top:10%; left:-10%; } .card-2 { bottom:15%; right:-10%; animation-delay:2s; }
@keyframes floatCard { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)} }

/* layout */
.main-wrapper { max-width:1400px; margin:0 auto; padding:0 1.5rem 4rem; display:grid; grid-template-columns:1fr 380px; gap:3rem; align-items:start; }
.section { margin-bottom:4rem; }
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:2.5rem; }
.section-title-wrapper { display:flex; align-items:center; gap:1rem; }
.section-icon { width:35px; height:35px; color:var(--accent-primary); }
.section-title { font-size:2rem; font-weight:800; color:var(--text-primary); }
.view-all-link { display:flex; align-items:center; gap:.5rem; color:var(--accent-primary); text-decoration:none; font-weight:600; transition:all .3s; }
.view-all-link:hover { gap:.8rem; } .view-all-link svg { width:20px; height:20px; }

/* محصولات */
.products-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:2rem; }
.product-card { background:var(--bg-primary); border-radius:20px; overflow:hidden; transition:all .4s cubic-bezier(.4,0,.2,1); border:1px solid var(--border-color); cursor:pointer; }
.product-card:hover { transform:translateY(-10px); box-shadow:var(--shadow-lg); }
.product-image-wrapper { position:relative; overflow:hidden; padding-top:100%; }
.product-image { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; transition:transform .5s ease; }
.product-card:hover .product-image { transform:scale(1.1); }
.product-badges { position:absolute; top:15px; right:15px; z-index:10; }
.discount-badge { background:linear-gradient(135deg,#ff6b6b,#ee5a6f); color:white; padding:.4rem .8rem; border-radius:10px; font-size:.85rem; font-weight:700; }
.product-overlay { position:absolute; inset:0; background:rgba(0,0,0,.7); display:flex; align-items:center; justify-content:center; opacity:0; transition:opacity .3s ease; }
.product-card:hover .product-overlay { opacity:1; }
.quick-view { background:white; color:var(--accent-primary); padding:.8rem 1.5rem; border-radius:12px; font-weight:600; text-decoration:none; transform:translateY(20px); transition:transform .3s; }
.product-card:hover .quick-view { transform:translateY(0); }
.product-info { padding:1.5rem; }
.product-category { display:inline-block; background:rgba(30,58,138,.1); color:var(--accent-primary); padding:.3rem .8rem; border-radius:8px; font-size:.8rem; font-weight:600; margin-bottom:.8rem; }
.product-name { font-size:1.1rem; font-weight:700; margin-bottom:1rem; line-height:1.4; }
.product-name a { color:var(--text-primary); text-decoration:none; transition:color .3s; }
.product-name a:hover { color:var(--accent-primary); }
.product-pricing { display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem; flex-wrap:wrap; }
.old-price { font-size:.9rem; color:var(--text-secondary); text-decoration:line-through; }
.new-price  { font-size:1.3rem; font-weight:800; color:#10b981; }
.btn-add-cart { width:100%; padding:.9rem; background:linear-gradient(135deg,var(--accent-primary),var(--accent-hover)); color:white; border:none; border-radius:12px; font-weight:600; text-decoration:none; display:flex; align-items:center; justify-content:center; gap:.5rem; cursor:pointer; transition:all .3s ease; font-family:inherit; }
.btn-add-cart:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(37,99,235,.3); }
.btn-add-cart svg { width:20px; height:20px; }

/* مقالات */
.posts-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:2rem; }
.post-card { background:var(--bg-primary); border-radius:20px; overflow:hidden; transition:all .4s cubic-bezier(.4,0,.2,1); border:1px solid var(--border-color); display:flex; flex-direction:column; }
.post-card:hover { transform:translateY(-10px); box-shadow:var(--shadow-lg); }
.post-image-wrapper { position:relative; overflow:hidden; padding-top:60%; }
.post-image { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; transition:transform .5s ease; }
.post-card:hover .post-image { transform:scale(1.1); }
.post-category-badge { position:absolute; bottom:15px; right:15px; background:rgba(0,0,0,.8); backdrop-filter:blur(10px); color:white; padding:.5rem 1rem; border-radius:10px; font-size:.85rem; font-weight:600; }
.post-content { padding:1.5rem; flex:1; display:flex; flex-direction:column; }
.post-title { font-size:1.2rem; font-weight:700; margin-bottom:1rem; line-height:1.4; }
.post-title a { color:var(--text-primary); text-decoration:none; transition:color .3s; }
.post-title a:hover { color:var(--accent-primary); }
.post-excerpt { color:var(--text-secondary); line-height:1.8; margin-bottom:1.5rem; flex:1; }
.post-footer { display:flex; justify-content:space-between; align-items:center; padding-top:1rem; border-top:1px solid var(--border-color); }
.post-author { display:flex; align-items:center; gap:.5rem; color:var(--text-secondary); font-size:.9rem; }
.post-author svg { width:18px; height:18px; color:var(--accent-primary); }
.read-more { display:flex; align-items:center; gap:.3rem; color:var(--accent-primary); text-decoration:none; font-weight:600; font-size:.9rem; transition:all .3s; }
.read-more:hover { gap:.6rem; } .read-more svg { width:18px; height:18px; }
.no-data-message { grid-column:1/-1; text-align:center; padding:4rem 2rem; background:var(--bg-primary); border-radius:20px; border:2px dashed var(--border-color); }

/* countdown */
.countdown-section { margin:4rem 0; padding:3rem 2rem; background:linear-gradient(135deg,#1e3a8a,#3b82f6); border-radius:30px; position:relative; overflow:hidden; }
.countdown-container { position:relative; z-index:10; display:grid; grid-template-columns:1.5fr 1fr; gap:3rem; align-items:center; }
.countdown-badge { display:inline-block; background:rgba(255,255,255,.2); color:white; padding:.5rem 1.2rem; border-radius:50px; font-size:.9rem; font-weight:700; margin-bottom:1rem; }
.countdown-title { font-size:2.5rem; font-weight:900; color:white; margin-bottom:1rem; line-height:1.2; }
.countdown-desc { font-size:1.1rem; color:rgba(255,255,255,.9); margin-bottom:2rem; }
.countdown-timer { display:flex; gap:1rem; margin-bottom:2rem; flex-wrap:wrap; }
.time-box { background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.3); padding:1.5rem 1rem; border-radius:15px; text-align:center; min-width:90px; }
.time-value { display:block; font-size:2.5rem; font-weight:900; color:white; line-height:1; margin-bottom:.5rem; }
.time-label { font-size:.9rem; color:rgba(255,255,255,.8); font-weight:600; }
.time-separator { color:white; font-size:2rem; font-weight:700; display:flex; align-items:center; }
.countdown-btn { display:inline-flex; align-items:center; gap:.75rem; background:white; color:var(--accent-primary); padding:1.2rem 2.5rem; border-radius:15px; font-weight:700; font-size:1.1rem; text-decoration:none; transition:all .3s ease; box-shadow:0 10px 30px rgba(0,0,0,.2); }
.countdown-btn:hover { transform:translateY(-3px); } .countdown-btn svg { width:24px; height:24px; }
.countdown-illustration { position:relative; height:300px; }
.floating-books { position:absolute; inset:0; }
.book { position:absolute; font-size:4rem; animation:floatBook 3s ease-in-out infinite; }
.book-1{top:10%;right:20%;animation-delay:0s} .book-2{top:60%;right:10%;animation-delay:.5s} .book-3{top:30%;right:70%;animation-delay:1s} .book-4{bottom:10%;right:50%;animation-delay:1.5s}
@keyframes floatBook { 0%,100%{transform:translateY(0) rotate(0deg)} 50%{transform:translateY(-30px) rotate(10deg)} }

/* CTA */
.cta-banner { margin:4rem 0; background:var(--bg-primary); border-radius:30px; padding:3rem; box-shadow:var(--shadow-lg); border:2px solid var(--border-color); }
.cta-content { display:grid; grid-template-columns:auto 1fr auto; gap:2rem; align-items:center; }
.cta-icon { width:80px; height:80px; background:linear-gradient(135deg,#1e3a8a,#3b82f6); border-radius:20px; display:flex; align-items:center; justify-content:center; color:white; flex-shrink:0; }
.cta-icon svg { width:40px; height:40px; }
.cta-title { font-size:1.8rem; font-weight:800; color:var(--text-primary); margin-bottom:.5rem; }
.cta-desc  { color:var(--text-secondary); font-size:1rem; }
.cta-form  { display:flex; gap:1rem; }
.cta-input { flex:1; padding:1rem 1.5rem; border:2px solid var(--border-color); border-radius:15px; font-size:1rem; font-family:inherit; background:var(--bg-secondary); color:var(--text-primary); transition:all .3s; min-width:250px; }
.cta-input:focus { outline:none; border-color:var(--accent-primary); }
.cta-button { display:flex; align-items:center; gap:.5rem; background:linear-gradient(135deg,#1e3a8a,#3b82f6); color:white; padding:1rem 2rem; border:none; border-radius:15px; font-weight:700; font-size:1rem; cursor:pointer; transition:all .3s; font-family:inherit; white-space:nowrap; }
.cta-button:hover { transform:translateY(-2px); } .cta-button svg { width:20px; height:20px; }

/* ویژگی‌ها */
.features-section { margin:4rem 0; }
.features-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:2rem; }
.feature-card { background:var(--bg-primary); padding:2rem; border-radius:20px; text-align:center; border:1px solid var(--border-color); transition:all .4s cubic-bezier(.4,0,.2,1); }
.feature-card:hover { transform:translateY(-10px); box-shadow:var(--shadow-lg); }
.feature-icon { width:80px; height:80px; background:linear-gradient(135deg,#1e3a8a,#3b82f6); border-radius:20px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem; color:white; transition:all .3s; }
.feature-card:hover .feature-icon { transform:scale(1.1) rotate(5deg); }
.feature-icon svg { width:40px; height:40px; }
.feature-title { font-size:1.3rem; font-weight:700; color:var(--text-primary); margin-bottom:.5rem; }
.feature-desc  { color:var(--text-secondary); font-size:.95rem; }

/* ══════════════════════════════════════════════════
   RESPONSIVE — تقویت‌شده کامل
   ══════════════════════════════════════════════════ */

/* لپتاپ */
@media (max-width: 1200px) {
    .main-wrapper { grid-template-columns:1fr; }
    .sidebar { position:static; order:2; }
    .products-grid { grid-template-columns:repeat(3,1fr); }
    .posts-grid    { grid-template-columns:repeat(2,1fr); }
}

/* تبلت بزرگ */
@media (max-width: 991px) {
    .hero-wrapper { grid-template-columns:1fr; gap:3rem; }
    .hero-title   { font-size:2.5rem; }
    .hero-image   { order:-1; text-align:center; }
    .floating-card { display:none; }

    .products-grid { grid-template-columns:repeat(2,1fr); gap:1.5rem; }
    .posts-grid    { grid-template-columns:repeat(2,1fr); gap:1.5rem; }

    .countdown-container  { grid-template-columns:1fr; text-align:center; }
    .countdown-timer      { justify-content:center; }
    .countdown-illustration { height:200px; }
    .countdown-title      { font-size:2rem; }

    .cta-content { grid-template-columns:1fr; text-align:center; }
    .cta-form    { flex-direction:column; }
    .cta-input   { min-width:100%; }
    .cta-icon    { margin:0 auto; }

    .section-title { font-size:1.7rem; }
    .main-wrapper  { padding:0 1.25rem 3rem; gap:2rem; }
}

/* تبلت کوچک */
@media (max-width: 768px) {
    .hero-section  { padding:6.5rem 0 3.5rem; margin-bottom:3rem; }
    .hero-title    { font-size:2.2rem; }
    .hero-desc     { font-size:1.05rem; }
    .hero-stats    { gap:1.5rem; padding:1.25rem; }
    .stat-item strong { font-size:1.6rem; }

    .products-grid { grid-template-columns:repeat(2,1fr); gap:1.25rem; }
    .posts-grid    { grid-template-columns:repeat(2,1fr); gap:1.25rem; }

    .countdown-section { padding:2.5rem 1.5rem; }
    .countdown-title { font-size:1.8rem; }
    .time-box    { min-width:75px; padding:1.1rem .75rem; }
    .time-value  { font-size:2rem; }
    .countdown-timer { gap:.75rem; }

    .cta-banner { padding:2rem 1.5rem; }
    .cta-title  { font-size:1.5rem; }

    .features-grid { grid-template-columns:repeat(2,1fr); gap:1.25rem; }
    .feature-card  { padding:1.5rem; }
    .feature-icon  { width:65px; height:65px; } .feature-icon svg { width:32px; height:32px; }
    .feature-title { font-size:1.1rem; }

    .section-title { font-size:1.5rem; }
    .section-icon  { width:28px; height:28px; }
}

/* موبایل */
@media (max-width: 576px) {
    .hero-section  { padding:6rem 0 3rem; margin-bottom:2rem; }
    .hero-title    { font-size:1.9rem; }
    .hero-desc     { font-size:.97rem; margin-bottom:2rem; }
    .hero-buttons  { gap:.85rem; }
    .btn-primary,.btn-secondary { padding:.85rem 1.5rem; font-size:.92rem; }
    .hero-stats    { gap:1rem; padding:1.1rem 1rem; flex-wrap:wrap; justify-content:center; }
    .stat-item strong { font-size:1.4rem; }
    .stat-divider  { display:none; }

    .products-grid { grid-template-columns:repeat(2,1fr); gap:1rem; }
    .posts-grid    { grid-template-columns:1fr; gap:1rem; }

    .product-info  { padding:1rem; }
    .product-name  { font-size:.95rem; margin-bottom:.75rem; }
    .new-price     { font-size:1.1rem; }
    .btn-add-cart  { padding:.75rem; font-size:.85rem; }

    .countdown-section { border-radius:20px; padding:2rem 1.25rem; margin:3rem 0; }
    .countdown-title { font-size:1.5rem; }
    .countdown-desc  { font-size:.95rem; }
    .time-box    { min-width:64px; padding:1rem .5rem; border-radius:12px; }
    .time-value  { font-size:1.75rem; }
    .time-label  { font-size:.78rem; }
    .countdown-timer { gap:.5rem; }
    .time-separator  { font-size:1.5rem; }
    .countdown-btn   { padding:1rem 1.75rem; font-size:.95rem; }
    .countdown-illustration { display:none; }

    .cta-banner   { padding:1.75rem 1.25rem; border-radius:20px; margin:3rem 0; }
    .cta-title    { font-size:1.3rem; }
    .cta-desc     { font-size:.9rem; }
    .cta-input    { padding:.85rem 1.1rem; }
    .cta-button   { padding:.85rem 1.25rem; font-size:.9rem; }
    .cta-icon     { width:60px; height:60px; } .cta-icon svg { width:30px; height:30px; }

    .features-grid { grid-template-columns:1fr 1fr; gap:1rem; }
    .feature-card  { padding:1.25rem; }
    .feature-icon  { width:55px; height:55px; margin-bottom:1rem; } .feature-icon svg { width:27px; height:27px; }
    .feature-title { font-size:1rem; }
    .feature-desc  { font-size:.85rem; }

    .section-title { font-size:1.3rem; }
    .section-header { margin-bottom:1.5rem; flex-wrap:wrap; gap:.75rem; }
    .main-wrapper  { padding:0 .85rem 2.5rem; }
    .section       { margin-bottom:3rem; }
}

/* موبایل خیلی کوچک */
@media (max-width: 420px) {
    .hero-title    { font-size:1.7rem; }
    .hero-buttons  { flex-direction:column; align-items:stretch; }
    .btn-primary,.btn-secondary { width:100%; justify-content:center; }

    .products-grid { grid-template-columns:1fr; gap:.85rem; }
    /* کارت افقی */
    .product-card  { display:grid; grid-template-columns:110px 1fr; border-radius:16px; }
    .product-image-wrapper { padding-top:0; min-height:145px; border-radius:16px 0 0 16px; }
    .product-image { position:absolute; border-radius:16px 0 0 16px; }
    .product-info  { padding:.85rem; }
    .btn-add-cart  { font-size:.78rem; padding:.6rem .5rem; }

    .features-grid { grid-template-columns:1fr; }

    .countdown-title { font-size:1.35rem; }
    .time-box { min-width:58px; }
    .time-value { font-size:1.5rem; }

    .section       { margin-bottom:2.5rem; }
    .main-wrapper  { padding:0 .75rem 2rem; }
}

/* AOS */
[data-aos] { opacity:0; transition-property:transform,opacity; transition-duration:.6s; transition-timing-function:cubic-bezier(.4,0,.2,1); }
[data-aos].aos-animate { opacity:1; }
[data-aos="fade-up"]  { transform:translateY(30px); } [data-aos="fade-up"].aos-animate  { transform:translateY(0); }
[data-aos="fade-left"]{ transform:translateX(-30px);} [data-aos="fade-left"].aos-animate{ transform:translateX(0); }
[data-aos="fade-right"]{transform:translateX(30px); } [data-aos="fade-right"].aos-animate{transform:translateX(0); }
[data-aos="zoom-in"]  { transform:scale(0.9);       } [data-aos="zoom-in"].aos-animate  { transform:scale(1); }
[data-aos="flip-left"]{ transform:perspective(2500px) rotateY(-100deg); } [data-aos="flip-left"].aos-animate{ transform:perspective(2500px) rotateY(0); }
</style>

<script>
function startCountdown() {
    const end = new Date(); end.setDate(end.getDate() + 7);
    function update() {
        const d = end - Date.now();
        if (d < 0) return;
        document.getElementById('days').textContent    = String(Math.floor(d/86400000)).padStart(2,'0');
        document.getElementById('hours').textContent   = String(Math.floor(d/3600000%24)).padStart(2,'0');
        document.getElementById('minutes').textContent = String(Math.floor(d/60000%60)).padStart(2,'0');
        document.getElementById('seconds').textContent = String(Math.floor(d/1000%60)).padStart(2,'0');
    }
    update(); setInterval(update, 1000);
}
document.addEventListener('DOMContentLoaded', () => {
    startCountdown();
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting) e.target.classList.add('aos-animate'); });
    }, {threshold:.1, rootMargin:'0px 0px -80px 0px'});
    document.querySelectorAll('[data-aos]').forEach(el => obs.observe(el));
});
</script>