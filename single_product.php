<?php
/**
 * صفحه نمایش محصول - Midnight Blue Theme
 * طراحی مدرن و سازگار با سایت
 */

require_once("./include/header.php");

$product = null;
$comments = [];
$category_title = '';
$related_products = [];

if (isset($_GET['product'])) {
    $product_id = filter_var($_GET['product'], FILTER_VALIDATE_INT);
    
    if ($product_id) {
        // دریافت محصول
        $product = fetchOne($db, 'SELECT * FROM product WHERE id = ?', [$product_id]);

        if ($product) {
            // دریافت دسته‌بندی
            $category = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$product['category_id']]);
            $category_title = $category ? escape($category['title']) : 'دسته‌بندی نامشخص';

            // دریافت نظرات - جدول این سایت ستون "text" داره نه "comment" و created_at هم نداره
            try {
                $comments = fetchAll($db, "SELECT name, text AS comment, created_at FROM product_comments WHERE product_id = ? ORDER BY id DESC", [$product_id]);
            } catch (PDOException $e) {
                try {
                    $comments = fetchAll($db, "SELECT name, text AS comment FROM product_comments WHERE product_id = ? ORDER BY id DESC", [$product_id]);
                } catch (PDOException $e2) {
                    try {
                        $comments = fetchAll($db, "SELECT name, comment FROM product_comments WHERE product_id = ? ORDER BY id DESC", [$product_id]);
                    } catch (PDOException $e3) {
                        $comments = [];
                    }
                }
            }
            
            // محصولات مرتبط
            $related_products = fetchAll($db, "SELECT * FROM product WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 4", [$product['category_id'], $product_id]);
        }
    }
}

// پردازش فرم نظر
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_comment']) && $product) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_msg = 'درخواست نامعتبر است!';
    } else {
        $name    = trim($_POST['name']    ?? '');
        $comment = trim($_POST['comment'] ?? '');

        if ($name === '' || $comment === '') {
            $error_msg = 'لطفاً نام و متن نظر را پر کنید.';
        } else {
            // تلاش به ترتیب برای چهار حالت مختلف ساختار جدول
            $queries = [
                "INSERT INTO product_comments (name, text,    product_id, created_at) VALUES (?, ?, ?, NOW())",
                "INSERT INTO product_comments (name, text,    product_id)             VALUES (?, ?, ?)",
                "INSERT INTO product_comments (name, comment, product_id, created_at) VALUES (?, ?, ?, NOW())",
                "INSERT INTO product_comments (name, comment, product_id)             VALUES (?, ?, ?)",
            ];

            $done = false;
            foreach ($queries as $sql) {
                try {
                    $result = executeQuery($db, $sql, [$name, $comment, $product_id]);
                    if ($result) {
                        $done = true;
                        break;
                    }
                } catch (PDOException $e) {
                    continue; // کوئری بعدی رو امتحان کن
                }
            }

            if ($done) {
                $success_msg = 'نظر شما با موفقیت ثبت شد!';
                // دوباره دریافت نظرات
                $comments = fetchAll($db, "SELECT name, text AS comment, created_at FROM product_comments WHERE product_id = ? ORDER BY id DESC", [$product_id]);
                if (empty($comments)) {
                    $comments = fetchAll($db, "SELECT name, comment, created_at FROM product_comments WHERE product_id = ? ORDER BY id DESC", [$product_id]);
                }
            } else {
                $error_msg = 'خطایی رخ داد. لطفاً با مدیر سایت تماس بگیرید.';
            }
        }
    }
}

function formatPrice($price) {
    return number_format($price) . ' تومان';
}
?>

<link rel="stylesheet" href="./css/style.css">

<div class="product-page-wrapper">
    <div class="container-custom">
        
        <?php if ($product): ?>
            
            <!-- Breadcrumb -->
            <nav class="breadcrumb" data-aos="fade-down">
                <a href="index.php">خانه</a>
                <span class="separator">›</span>
                <a href="products.php">محصولات</a>
                <span class="separator">›</span>
                <span class="current"><?= escape($product['name']) ?></span>
            </nav>

            <div class="product-layout">
                
                <!-- محتوای اصلی -->
                <main class="product-main">
                    
                    <!-- کارت محصول اصلی -->
                    <div class="product-showcase" data-aos="fade-up">
                        <div class="product-gallery">
                            <div class="main-image">
                                <img src="./upload/products/<?= escape($product['pic']) ?>" 
                                     alt="<?= escape($product['name']) ?>"
                                     id="mainProductImage">
                                <?php if ($product['price'] != $product['new-price']): ?>
                                    <?php 
                                    $discount = round((($product['price'] - $product['new-price']) / $product['price']) * 100);
                                    ?>
                                    <span class="discount-badge"><?= $discount ?>% تخفیف</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-details">
                            <div class="product-header">
                                <span class="category-tag"><?= $category_title ?></span>
                                <h1 class="product-title"><?= escape($product['name']) ?></h1>
                                
                                <div class="product-meta">
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/>
                                        </svg>
                                        <span>4.5 از 5</span>
                                    </div>
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5M12,17C9.24,17 7,14.76 7,12C7,9.24 9.24,7 12,7C14.76,7 17,9.24 17,12C17,14.76 14.76,17 12,17M12,9C10.34,9 9,10.34 9,12C9,13.66 10.34,15 12,15C13.66,15 15,13.66 15,12C15,10.34 13.66,9 12,9Z"/>
                                        </svg>
                                        <span><?= rand(100, 500) ?> بازدید</span>
                                    </div>
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                                        </svg>
                                        <span><?= rand(50, 200) ?> فروش</span>
                                    </div>
                                </div>
                            </div>

                            <div class="description-box">
                                <h3>توضیحات محصول</h3>
                                <p><?= nl2br(escape($product['description'] ?? 'توضیحاتی برای این محصول ثبت نشده است.')) ?></p>
                            </div>

                            <div class="specs-list">
                                <div class="spec-row">
                                    <span class="spec-label">وضعیت:</span>
                                    <span class="spec-value">
                                        <?php if ((int)$product['number'] > 0): ?>
                                            <span class="badge-success">✓ موجود</span>
                                        <?php else: ?>
                                            <span class="badge-error">✗ ناموجود</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="spec-row">
                                    <span class="spec-label">موجودی انبار:</span>
                                    <span class="spec-value"><?= (int)$product['number'] ?> عدد</span>
                                </div>
                            </div>

                            <div class="pricing-section">
                                <?php if ($product['price'] != $product['new-price']): ?>
                                    <div class="price-old"><?= formatPrice($product['price']) ?></div>
                                    <div class="price-new"><?= formatPrice($product['new-price']) ?></div>
                                    <div class="price-save">
                                        شما <?= formatPrice($product['price'] - $product['new-price']) ?> صرفه‌جویی می‌کنید!
                                    </div>
                                <?php else: ?>
                                    <div class="price-new"><?= formatPrice($product['price']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="action-buttons">
                                <button id="addToCartBtn" class="btn-add-cart" onclick="addToCart(<?= (int)$product['id'] ?>)">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                                    </svg>
                                    افزودن به سبد خرید
                                </button>
                                <a href="cart.php" id="goToCartBtn" class="btn-cart-link" style="display: none;">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9,20A2,2 0 0,1 7,22A2,2 0 0,1 5,20A2,2 0 0,1 7,18A2,2 0 0,1 9,20M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22A2,2 0 0,1 15,20A2,2 0 0,1 17,18M7.17,14.75L7.2,14.63L8.1,13H15.55C16.3,13 16.96,12.59 17.3,11.97L21.16,4.96L19.42,4H19.41L18.31,6L15.55,11H8.53L8.4,10.73L6.16,6L5.21,4L4.27,2H1V4H3L6.6,11.59L5.25,14.04C5.09,14.32 5,14.65 5,15A2,2 0 0,0 7,17H19V15H7.42C7.29,15 7.17,14.89 7.17,14.75Z"/>
                                    </svg>
                                    مشاهده سبد خرید
                                </a>
                            </div>

                            <div class="product-features">
                                <div class="feature-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                                    </svg>
                                    <span>گارانتی اصالت کالا</span>
                                </div>
                                <div class="feature-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
                                    </svg>
                                    <span>ارسال رایگان</span>
                                </div>
                                <div class="feature-item">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,7H13V13H11V7M11,15H13V17H11V15Z"/>
                                    </svg>
                                    <span>پشتیبانی 24/7</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- بخش نظرات -->
                    <section class="comments-section" data-aos="fade-up">
                        <div class="section-head">
                            <h2 class="section-title">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M6,7H18V9H6V7M6,11H15V13H6V11Z"/>
                                </svg>
                                نظرات کاربران
                                <span class="count">(<?= count($comments) ?>)</span>
                            </h2>
                        </div>

                        <?php if ($error_msg): ?>
                            <div class="alert alert-error"><?= escape($error_msg) ?></div>
                        <?php endif; ?>

                        <?php if ($success_msg): ?>
                            <div class="alert alert-success"><?= escape($success_msg) ?></div>
                        <?php endif; ?>

                        <form method="post" class="comment-form">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="form-group">
                                <label for="name">نام شما</label>
                                <input type="text" name="name" id="name" placeholder="نام خود را وارد کنید" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="comment">نظر شما</label>
                                <textarea name="comment" id="comment" rows="4" placeholder="نظر خود را بنویسید..." required></textarea>
                            </div>
                            
                            <button type="submit" name="product_comment" class="btn-submit">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M2,21L23,12L2,3V10L17,12L2,14V21Z"/>
                                </svg>
                                ثبت نظر
                            </button>
                        </form>

                        <div class="comments-list">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-item">
                                        <div class="comment-avatar">
                                            <?= mb_substr($comment['name'], 0, 1) ?>
                                        </div>
                                        <div class="comment-content">
                                            <div class="comment-header">
                                                <strong class="comment-author"><?= escape($comment['name']) ?></strong>
                                                <time class="comment-date">
                                                    <?= !empty($comment['created_at']) ? date('Y/m/d', strtotime($comment['created_at'])) : 'اخیراً' ?>
                                                </time>
                                            </div>
                                            <p class="comment-text"><?= nl2br(escape($comment['comment'] ?? $comment['text'] ?? '')) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-comments">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z"/>
                                    </svg>
                                    <p>هنوز نظری ثبت نشده است. اولین نفر باشید!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                    <!-- محصولات مرتبط -->
                    <?php if (!empty($related_products)): ?>
                        <section class="related-products" data-aos="fade-up">
                            <h2 class="section-title">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,19H5V5H19M19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5A2,2 0 0,0 19,3M13.96,12.29L11.21,15.83L9.25,13.47L6.5,17H17.5L13.96,12.29Z"/>
                                </svg>
                                محصولات مرتبط
                            </h2>
                            <div class="related-grid">
                                <?php foreach ($related_products as $related): ?>
                                    <a href="single_product.php?product=<?= $related['id'] ?>" class="related-card">
                                        <img src="./upload/products/<?= escape($related['pic']) ?>" alt="<?= escape($related['name']) ?>">
                                        <h3><?= escape($related['name']) ?></h3>
                                        <p class="price"><?= formatPrice($related['new-price'] ?: $related['price']) ?></p>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                </main>

                <!-- سایدبار -->
                <aside class="product-sidebar">
                    <?php require_once("./include/sidebar.php"); ?>
                </aside>

            </div>

        <?php else: ?>
            
            <div class="product-not-found">
                <div class="not-found-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
                    </svg>
                </div>
                <h2>محصول مورد نظر یافت نشد!</h2>
                <p>متأسفانه محصولی با این شناسه وجود ندارد.</p>
                <a href="products.php" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    بازگشت به محصولات
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php require_once("./include/footer.php"); ?>

<script>
// ═══════════════════════════════════════════════════════════
// 🎨 Toast Notification با انیمیشن
// ═══════════════════════════════════════════════════════════

function showToast(message, type = 'success') {
    // ایجاد toast
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // آیکون بر اساس نوع
    const icons = {
        success: `<svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,16.5L6.5,12L7.91,10.59L11,13.67L16.59,8.09L18,9.5L11,16.5Z"/>
        </svg>`,
        error: `<svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M12,2C17.53,2 22,6.47 22,12C22,17.53 17.53,22 12,22C6.47,22 2,17.53 2,12C2,6.47 6.47,2 12,2M15.59,7L12,10.59L8.41,7L7,8.41L10.59,12L7,15.59L8.41,17L12,13.41L15.59,17L17,15.59L13.41,12L17,8.41L15.59,7Z"/>
        </svg>`,
        loading: `<div class="spinner"></div>`
    };
    
    toast.innerHTML = `
        <div class="toast-icon">${icons[type]}</div>
        <div class="toast-message">${message}</div>
    `;
    
    // اضافه کردن به body
    document.body.appendChild(toast);
    
    // انیمیشن ورود
    setTimeout(() => toast.classList.add('show'), 10);
    
    // حذف خودکار بعد از 3 ثانیه
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ═══════════════════════════════════════════════════════════
// 🛒 افزودن به سبد خرید
// ═══════════════════════════════════════════════════════════

function addToCart(productId) {
    const btn = document.getElementById('addToCartBtn');
    const cartLink = document.getElementById('goToCartBtn');
    const originalHTML = btn.innerHTML;
    
    // غیرفعال کردن دکمه
    btn.disabled = true;
    
    // نمایش loading
    btn.innerHTML = `
        <div class="btn-spinner"></div>
        در حال افزودن...
    `;
    
    // ارسال درخواست
    fetch(`add_to_cart.php?product_id=${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('خطا در ارتباط با سرور');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // تغییر دکمه به حالت موفق
                btn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21,7L9,19L3.5,13.5L4.91,12.09L9,16.17L19.59,5.59L21,7Z"/>
                    </svg>
                    اضافه شد!
                `;
                btn.classList.add('success');
                
                // نمایش toast موفقیت
                showToast('✅ محصول با موفقیت به سبد خرید اضافه شد', 'success');
                
                // نمایش لینک سبد خرید
                cartLink.style.display = 'inline-flex';
                
                // انیمیشن دکمه
                btn.animate([
                    { transform: 'scale(1)' },
                    { transform: 'scale(1.1)' },
                    { transform: 'scale(1)' }
                ], {
                    duration: 400,
                    easing: 'ease-in-out'
                });
                
                // به‌روزرسانی شمارنده سبد در navbar (اگر وجود داشته باشد)
                updateCartBadge(data.total_items);
                
                // Confetti انیمیشن 🎉
                createConfetti();
                
            } else {
                throw new Error(data.message || 'خطا در افزودن به سبد خرید');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // نمایش toast خطا
            showToast('❌ ' + error.message, 'error');
            
            // بازگردانی دکمه
            btn.innerHTML = originalHTML;
            btn.disabled = false;
            btn.classList.remove('success');
        });
}

// ═══════════════════════════════════════════════════════════
// 🎊 انیمیشن Confetti
// ═══════════════════════════════════════════════════════════

function createConfetti() {
    const colors = ['#1e3a8a', '#3b82f6', '#60a5fa', '#10b981', '#f59e0b'];
    const confettiCount = 30;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti';
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.animationDelay = Math.random() * 0.3 + 's';
        confetti.style.animationDuration = Math.random() * 2 + 2 + 's';
        
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 4000);
    }
}

// ═══════════════════════════════════════════════════════════
// 🔢 به‌روزرسانی Badge سبد خرید
// ═══════════════════════════════════════════════════════════

function updateCartBadge(count) {
    const badge = document.querySelector('.header-cart-count');
    if (badge) {
        badge.textContent = count;
        badge.animate([
            { transform: 'scale(1)' },
            { transform: 'scale(1.3)' },
            { transform: 'scale(1)' }
        ], {
            duration: 300,
            easing: 'ease-in-out'
        });
    }
}

// ═══════════════════════════════════════════════════════════
// ✨ انیمیشن AOS
// ═══════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', () => {
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

<style>
/* ═══════════════════════════════════════════════════════════
   🎨 صفحه محصول - Midnight Blue Theme
   ═══════════════════════════════════════════════════════════ */

.product-page-wrapper {
    padding: 2rem 0 4rem;
}

.container-custom {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 0 2rem;
    font-size: 0.95rem;
}

.breadcrumb a {
    color: var(--text-secondary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: var(--accent-primary);
}

.breadcrumb .separator {
    color: var(--text-tertiary);
}

.breadcrumb .current {
    color: var(--text-primary);
    font-weight: 600;
}

/* Layout */
.product-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 3rem;
    align-items: start;
}

/* کارت محصول */
.product-showcase {
    background: var(--bg-primary);
    border-radius: 30px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.product-gallery {
    position: relative;
    background: var(--bg-secondary);
    padding: 3rem;
}

.main-image {
    position: relative;
    text-align: center;
}

.main-image img {
    max-width: 100%;
    height: auto;
    max-height: 500px;
    object-fit: contain;
    border-radius: 20px;
    box-shadow: var(--shadow-md);
}

.discount-badge {
    position: absolute;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 15px;
    font-size: 1rem;
    font-weight: 700;
    box-shadow: 0 8px 20px rgba(255, 107, 107, 0.4);
}

.product-details {
    padding: 3rem;
}

.product-header {
    margin-bottom: 2rem;
}

.category-tag {
    display: inline-block;
    background: var(--accent-light);
    color: var(--accent-primary);
    padding: 0.5rem 1.2rem;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1rem;
}

body.dark-mode .category-tag {
    background: rgba(30, 58, 138, 0.2);
}

.product-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1.3;
    margin-bottom: 1.5rem;
}

.product-meta {
    display: flex;
    gap: 2rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.95rem;
}

.meta-item svg {
    width: 20px;
    height: 20px;
    color: var(--accent-primary);
}

.description-box {
    background: var(--bg-secondary);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
}

.description-box h3 {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.description-box p {
    color: var(--text-secondary);
    line-height: 1.8;
}

.specs-list {
    margin-bottom: 2rem;
}

.spec-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.spec-row:last-child {
    border-bottom: none;
}

.spec-label {
    color: var(--text-secondary);
    font-weight: 600;
}

.spec-value {
    color: var(--text-primary);
    font-weight: 700;
}

.badge-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.9rem;
}

.badge-error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error);
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.9rem;
}

.pricing-section {
    background: var(--gradient-primary);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
}

.price-old {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1.1rem;
    text-decoration: line-through;
    margin-bottom: 0.5rem;
}

.price-new {
    color: white;
    font-size: 2.5rem;
    font-weight: 900;
    margin-bottom: 0.5rem;
}

.price-save {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1rem;
    font-weight: 600;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.btn-add-cart {
    flex: 1;
    background: var(--gradient-primary);
    color: white;
    padding: 1.2rem 2rem;
    border: none;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-add-cart:hover:not(:disabled) {
    transform: translateY(-3px);
    box-shadow: var(--shadow-blue);
}

.btn-add-cart:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-add-cart.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.btn-add-cart svg {
    width: 24px;
    height: 24px;
}

.btn-cart-link {
    flex: 1;
    background: transparent;
    color: var(--accent-primary);
    border: 2px solid var(--accent-primary);
    padding: 1.2rem 2rem;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
}

.btn-cart-link:hover {
    background: var(--accent-primary);
    color: white;
}

.btn-cart-link svg {
    width: 24px;
    height: 24px;
}

.product-features {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: 12px;
}

.feature-item svg {
    width: 28px;
    height: 28px;
    color: var(--accent-primary);
    flex-shrink: 0;
}

.feature-item span {
    color: var(--text-secondary);
    font-size: 0.95rem;
    font-weight: 600;
}

/* بخش نظرات */
.comments-section {
    background: var(--bg-primary);
    border-radius: 30px;
    padding: 3rem;
    margin-top: 3rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.section-head {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 800;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.section-title svg {
    width: 32px;
    height: 32px;
    color: var(--accent-primary);
}

.section-title .count {
    background: var(--accent-light);
    color: var(--accent-primary);
    padding: 0.3rem 0.8rem;
    border-radius: 10px;
    font-size: 1rem;
}

body.dark-mode .section-title .count {
    background: rgba(30, 58, 138, 0.2);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    font-weight: 600;
}

.alert-error {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.alert-success {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.comment-form {
    margin-bottom: 3rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 1rem 1.5rem;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 15px;
    font-family: inherit;
    font-size: 1rem;
    color: var(--text-primary);
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent-primary);
    box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
}

.btn-submit {
    background: var(--gradient-primary);
    color: white;
    padding: 1rem 2.5rem;
    border: none;
    border-radius: 15px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.3s ease;
    font-family: inherit;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-blue);
}

.btn-submit svg {
    width: 20px;
    height: 20px;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.comment-item {
    display: flex;
    gap: 1.5rem;
    padding: 1.5rem;
    background: var(--bg-secondary);
    border-radius: 20px;
    transition: all 0.3s ease;
}

.comment-item:hover {
    box-shadow: var(--shadow-md);
}

.comment-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--gradient-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    font-weight: 700;
    flex-shrink: 0;
}

.comment-content {
    flex: 1;
}

.comment-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.comment-author {
    color: var(--text-primary);
    font-size: 1.05rem;
}

.comment-date {
    color: var(--text-tertiary);
    font-size: 0.9rem;
}

.comment-text {
    color: var(--text-secondary);
    line-height: 1.7;
}

.empty-comments {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-comments svg {
    width: 80px;
    height: 80px;
    color: var(--text-tertiary);
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-comments p {
    color: var(--text-secondary);
    font-size: 1.1rem;
}

/* محصولات مرتبط */
.related-products {
    margin-top: 3rem;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 2rem;
}

.related-card {
    background: var(--bg-primary);
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
    text-decoration: none;
}

.related-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-blue);
}

.related-card img {
    width: 100%;
    aspect-ratio: 1;
    object-fit: cover;
}

.related-card h3 {
    padding: 1rem 1.5rem 0.5rem;
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 700;
}

.related-card .price {
    padding: 0 1.5rem 1.5rem;
    color: var(--success);
    font-weight: 800;
    font-size: 1.1rem;
}

/* محصول یافت نشد */
.product-not-found {
    text-align: center;
    padding: 5rem 2rem;
    background: var(--bg-primary);
    border-radius: 30px;
    box-shadow: var(--shadow-lg);
}

.not-found-icon svg {
    width: 100px;
    height: 100px;
    color: var(--error);
    margin-bottom: 2rem;
}

.product-not-found h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.product-not-found p {
    color: var(--text-secondary);
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--gradient-primary);
    color: white;
    padding: 1rem 2.5rem;
    border-radius: 15px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-blue);
}

.btn-back svg {
    width: 24px;
    height: 24px;
}

/* Loading */
.loading {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Button Spinner */
.btn-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 0.6s linear infinite;
}

/* ═══════════════════════════════════════════════════════════
   🎨 Toast Notification
   ═══════════════════════════════════════════════════════════ */

.toast {
    position: fixed;
    top: 100px;
    right: -400px;
    background: white;
    padding: 1.2rem 1.5rem;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 1rem;
    z-index: 10000;
    min-width: 320px;
    transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-right: 5px solid;
}

body.dark-mode .toast {
    background: var(--bg-primary);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
}

.toast.show {
    right: 2rem;
}

.toast-success {
    border-color: #10b981;
}

.toast-error {
    border-color: #ef4444;
}

.toast-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.toast-success .toast-icon {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.toast-error .toast-icon {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.toast-icon svg {
    width: 24px;
    height: 24px;
}

.toast-message {
    flex: 1;
    color: var(--text-primary);
    font-weight: 600;
    font-size: 0.95rem;
}

/* Spinner در Toast */
.spinner {
    width: 24px;
    height: 24px;
    border: 3px solid rgba(30, 58, 138, 0.2);
    border-radius: 50%;
    border-top-color: #1e3a8a;
    animation: spin 0.6s linear infinite;
}

/* ═══════════════════════════════════════════════════════════
   🎊 Confetti انیمیشن
   ═══════════════════════════════════════════════════════════ */

.confetti {
    position: fixed;
    width: 10px;
    height: 10px;
    top: -10px;
    z-index: 9999;
    animation: confettiFall linear forwards;
    pointer-events: none;
}

@keyframes confettiFall {
    0% {
        transform: translateY(0) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(100vh) rotate(720deg);
        opacity: 0;
    }
}

/* ═══════════════════════════════════════════════════════════
   📱 Toast Responsive
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 576px) {
    .toast {
        min-width: auto;
        right: -100%;
        left: 1rem;
        right: 1rem;
    }

    .toast.show {
        right: 1rem;
    }
}

/* Sidebar */
.product-sidebar {
    position: sticky;
    top: 100px;
}

/* Responsive */
@media (max-width: 1200px) {
    .product-layout {
        grid-template-columns: 1fr;
    }

    .product-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .product-showcase {
        border-radius: 20px;
    }

    .product-gallery {
        padding: 2rem;
    }

    .product-details {
        padding: 2rem;
    }

    .product-title {
        font-size: 1.5rem;
    }

    .price-new {
        font-size: 2rem;
    }

    .action-buttons {
        flex-direction: column;
    }

    .product-features {
        grid-template-columns: 1fr;
    }

    .comments-section {
        padding: 2rem;
        border-radius: 20px;
    }

    .related-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }
}

/* انیمیشن AOS */
[data-aos] {
    opacity: 0;
    transition: all 0.6s ease;
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

[data-aos="fade-down"] {
    transform: translateY(-30px);
}

[data-aos="fade-down"].aos-animate {
    transform: translateY(0);
}
</style>