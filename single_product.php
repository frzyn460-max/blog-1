<?php
include("./include/header.php");

$product = null;
$comments = [];
$category_title = '';
if (isset($_GET['product'])) {
    $product_id = (int)$_GET['product'];
    $product_stmt = $db->prepare('SELECT * FROM product WHERE id = :id');
    $product_stmt->execute(['id' => $product_id]);
    $product = $product_stmt->fetch();

    if ($product) {
        $cat_stmt = $db->prepare("SELECT title FROM categories WHERE id = :id");
        $cat_stmt->execute(['id' => $product['category_id']]);
        $cat_row = $cat_stmt->fetch();
        $category_title = $cat_row ? htmlspecialchars($cat_row['title']) : 'دسته‌بندی نامشخص';

        $comments_stmt = $db->prepare("SELECT name, comment, created_at FROM product_comments WHERE product_id = :id ORDER BY id DESC");
        $comments_stmt->execute(['id' => $product_id]);
        $comments = $comments_stmt->fetchAll();
    }
}

$error_msg = '';
if (isset($_POST['product_comment']) && $product) {
    $name = trim($_POST['name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    if ($name === '' || $comment === '') {
        $error_msg = 'لطفاً نام و متن نظر را پر کنید.';
    } else {
        $insert = $db->prepare("INSERT INTO product_comments (name, comment, product_id, created_at) VALUES (:name, :comment, :pid, NOW())");
        $insert->execute(['name' => $name, 'comment' => $comment, 'pid' => $product_id]);
        header("Location: single_product.php?product=$product_id");
        exit();
    }
}
?>

<!-- فقط این بخش استایل دارد — هیچ تداخلی با هدر/فوتر ندارد -->
<div class="product-page">
    <div class="container">
        <div class="product-layout">
            <main class="main-content">
                <?php if ($product): ?>
                    <div class="product-card">
                        <div class="product-image-container">
                            <img 
                                src="./upload/products/<?= htmlspecialchars($product['pic']) ?>" 
                                alt="<?= htmlspecialchars($product['name']) ?>"
                                class="product-image"
                            >
                        </div>

                        <div class="product-content">
                            <div class="product-header">
                                <div class="badges">
                                    <span class="badge category"><?= $category_title ?></span>
                                    <span class="badge stock <?= ((int)$product['number'] > 0) ? 'in-stock' : 'out-of-stock' ?>">
                                        <?= ((int)$product['number'] > 0) ? 'موجود' : 'ناموجود' ?>
                                    </span>
                                </div>
                                <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>
                            </div>

                            <div class="product-section">
                                <h3 class="section-title">توضیحات</h3>
                                <div class="description">
                                    <?= nl2br(htmlspecialchars($product['description'] ?? 'توضیحاتی ثبت نشده.')) ?>
                                </div>
                            </div>

                            <div class="product-section specs">
                                <h3 class="section-title">مشخصات</h3>
                                <div class="specs-grid">
                                    <div class="spec-item">
                                        <span class="label">موجودی:</span>
                                        <span class="value"><?= (int)$product['number'] ?> عدد</span>
                                    </div>
                                    <?php if ($product['new-price'] && $product['new-price'] < $product['price']): ?>
                                        <div class="spec-item">
                                            <span class="label">قیمت قبلی:</span>
                                            <span class="value old"><?= number_format($product['price']) ?> تومان</span>
                                        </div>
                                        <div class="spec-item">
                                            <span class="label">قیمت ویژه:</span>
                                            <span class="value highlight"><?= number_format($product['new-price']) ?> تومان</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="spec-item">
                                            <span class="label">قیمت:</span>
                                            <span class="value"><?= number_format($product['price']) ?> تومان</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="product-actions">
                                <button 
                                    id="add-to-cart-btn" 
                                    class="btn primary"
                                    onclick="addToCart(<?= (int)$product['id'] ?>)"
                                >
                                    🛒 افزودن به سبد خرید
                                </button>
                                <a href="cart.php" id="go-to-cart-btn" class="btn outline hidden">
                                    ✅ مشاهده سبد خرید
                                </a>
                            </div>
                        </div>
                    </div>

                    <section class="comments-section">
                        <div class="section-header">
                            <h2 class="section-title">
                                💬 نظرات کاربران
                                <span class="comment-count">(<?= count($comments) ?>)</span>
                            </h2>
                        </div>

                        <?php if ($error_msg): ?>
                            <div class="alert error"><?= htmlspecialchars($error_msg) ?></div>
                        <?php endif; ?>

                        <form method="post" class="comment-form">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="name">نام شما *</label>
                                    <input type="text" name="name" id="name" required>
                                </div>
                                <div class="form-group full">
                                    <label for="comment">نظر شما *</label>
                                    <textarea name="comment" id="comment" rows="4" required placeholder="نظر خود را بنویسید..."></textarea>
                                </div>
                            </div>
                            <button type="submit" name="product_comment" class="btn secondary">
                                📤 ثبت نظر
                            </button>
                        </form>

                        <div class="comments-list">
                            <?php if ($comments): ?>
                                <?php foreach ($comments as $c): ?>
                                    <div class="comment-card">
                                        <div class="comment-avatar">
                                            <div class="avatar-initial"><?= strtoupper(substr($c['name'], 0, 1)) ?></div>
                                        </div>
                                        <div class="comment-body">
                                            <div class="comment-head">
                                                <strong><?= htmlspecialchars($c['name']) ?></strong>
                                                <time><?= $c['created_at'] ? date('Y/m/d H:i', strtotime($c['created_at'])) : 'اکنون' ?></time>
                                            </div>
                                            <p class="comment-text"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="icon">💬</div>
                                    <p>هنوز نظری ثبت نشده است.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </section>

                <?php else: ?>
                    <div class="empty-product">
                        <div class="icon">⚠️</div>
                        <h2>محصولی یافت نشد</h2>
                        <a href="index.php" class="btn outline">بازگشت</a>
                    </div>
                <?php endif; ?>
            </main>

            <aside class="sidebar">
                <?php include("./include/sidebar.php") ?>
            </aside>
        </div>
    </div>
</div>

<?php include("./include/footer.php") ?>

<script>
function addToCart(productId) {
    fetch(`add_to_cart.php?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            const btn = document.getElementById("add-to-cart-btn");
            const link = document.getElementById("go-to-cart-btn");
            if (data.success) {
                btn.innerHTML = '✅ اضافه شد!';
                btn.classList.add('success');
                btn.disabled = true;
                link.classList.remove('hidden');
                btn.animate([
                    { transform: 'translateY(0)' },
                    { transform: 'translateY(-3px)' },
                    { transform: 'translateY(0)' }
                ], { duration: 300 });
            } else {
                alert('خطا در افزودن به سبد خرید');
            }
        })
        .catch(() => alert('خطا در ارتباط'));
}
</script>

<style>
/* === فقط برای این صفحه — بدون تداخل با هدر/بدنه سایت === */
:where(.product-page) {
    /* متغیرهای رنگ — دقیق و تست‌شده برای دارک/لایت */
    --surface: #ffffff;
    --surface-soft: #f8fafc;
    --on-surface: #1e293b;
    --on-surface-soft: #475569;
    --on-surface-muted: #64748b;
    --primary: #635bff;
    --primary-soft: #e8e6ff;
    --secondary: #ff3b60;
    --secondary-soft: #ffe7eb;
    --success: #00c896;
    --border: #e2e8f0;
    --divider: #f1f5f9;
    --shadow: 0 2px 8px rgba(0,0,0,0.05);
    --shadow-hover: 0 6px 16px rgba(0,0,0,0.08);
}

[data-theme="dark"] :where(.product-page) {
    --surface: #1e1e2e;
    --surface-soft: #1a1a2a;
    --on-surface: #e2e2ff;
    --on-surface-soft: #b0b0d0;
    --on-surface-muted: #8a8ab8;
    --primary: #9d8cff;
    --primary-soft: #2d2a48;
    --secondary: #ff7a92;
    --secondary-soft: #421d2a;
    --success: #4cd9ac;
    --border: #333347;
    --divider: #282838;
    --shadow: 0 2px 10px rgba(0,0,0,0.2);
    --shadow-hover: 0 6px 20px rgba(0,0,0,0.3);
}

* {
    font-family: tanha;
}

/* کانتینر اصلی */
.product-page {
    
    direction: rtl;
}
.product-page .container {
    max-width: 1200px;
    width: 95%;
    margin: 0 auto;
    padding: 20px 0;
}

/* لAYOUT */
.product-page .product-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 28px;
}
@media (max-width: 992px) {
    .product-page .product-layout { grid-template-columns: 1fr; }
}

/* === کارت محصول === */
.product-page .product-card {
    background: var(--surface);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: box-shadow 0.3s;
}
.product-page .product-card:hover {
    box-shadow: var(--shadow-hover);
}

.product-page .product-image-container {
    background: var(--surface-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.product-page .product-image {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    display: block;
}

.product-page .product-content {
    padding: 32px;
}
@media (max-width: 768px) {
    .product-page .product-content { padding: 24px 20px; }
}

.product-page .badges {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.product-page .badge {
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}
.product-page .badge.category {
    background: var(--primary-soft);
    color: var(--primary);
}
.product-page .badge.stock.in-stock {
    background: rgba(0, 200, 150, 0.15);
    color: var(--success);
}
.product-page .badge.stock.out-of-stock {
    background: var(--secondary-soft);
    color: var(--secondary);
}

.product-page .product-title {
    font-size: 1.75rem;
    font-weight: 700;
    margin: 0 0 24px;
    color: var(--on-surface);
}

/* بخش‌ها */
.product-page .product-section {
    margin-bottom: 32px;
}
.product-page .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--divider);
    color: var(--on-surface);
}

.product-page .description {
    color: var(--on-surface-soft);
    line-height: 1.7;
    font-size: 1.05rem;
    text-align: justify;
}

.product-page .specs-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
@media (max-width: 576px) {
    .product-page .specs-grid { grid-template-columns: 1fr; }
}
.product-page .label {
    font-size: 0.9rem;
    color: var(--on-surface-muted);
    margin-bottom: 4px;
}
.product-page .value {
    font-weight: 600;
    color: var(--on-surface);
}
.product-page .value.old {
    text-decoration: line-through;
    color: var(--on-surface-muted);
}
.product-page .value.highlight {
    color: var(--secondary);
}

/* دکمه‌ها */
.product-page .product-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.product-page .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 12px;
    cursor: pointer;
    border: none;
    transition: all 0.2s;
    text-decoration: none;
    min-width: 180px;
}
.product-page .btn.primary {
    background: var(--primary);
    color: white;
}
.product-page .btn.primary:hover:not(:disabled) {
    opacity: 0.92;
    transform: translateY(-2px);
}
.product-page .btn.success {
    background: var(--success) !important;
    color: #0f0f1a !important;
}
.product-page .btn.outline {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}
.product-page .btn.outline:hover {
    background: var(--primary);
    color: white;
}
.product-page .btn.secondary {
    background: var(--surface-soft);
    color: var(--on-surface);
}
.product-page .btn.secondary:hover {
    background: var(--divider);
}
.product-page .hidden { display: none; }

/* === نظرات === */
.product-page .comments-section {
    background: var(--surface);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-top: 28px;
}

.product-page .section-header {
    background: var(--surface-soft);
    padding: 20px 32px;
    border-bottom: 1px solid var(--border);
}
.product-page .comment-count {
    background: var(--primary-soft);
    color: var(--primary);
    padding: 2px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-right: 8px;
}

.product-page .alert {
    padding: 14px 20px;
    margin: 20px 32px;
    border-radius: 12px;
    font-weight: 500;
}
.product-page .alert.error {
    background: #fef2f2;
    color: #d32f2f;
    border: 1px solid #ffcdd2;
}
[data-theme="dark"] .product-page .alert.error {
    background: #2f1b1e;
    border-color: #5c1a1f;
    color: #fecaca;
}

.product-page .comment-form {
    padding: 0 32px 28px;
}
.product-page .form-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 20px;
    margin-bottom: 24px;
}
@media (max-width: 768px) {
    .product-page .form-grid { grid-template-columns: 1fr; }
}
.product-page .form-group.full { grid-column: 1 / -1; }
.product-page .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--on-surface);
    font-size: 0.95rem;
}
.product-page .form-group input,
.product-page .form-group textarea {
    width: 100%;
    padding: 14px;
    background: var(--surface-soft);
    border: 1px solid var(--border);
    border-radius: 12px;
    font-family: inherit;
    font-size: 1rem;
    color: var(--on-surface);
}
.product-page .form-group input::placeholder,
.product-page .form-group textarea::placeholder {
    color: var(--on-surface-muted);
}
.product-page .form-group input:focus,
.product-page .form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 91, 255, 0.2);
}

.product-page .comments-list {
    padding: 0 32px 32px;
}
.product-page .comment-card {
    display: flex;
    gap: 16px;
    padding: 20px;
    margin-bottom: 16px;
    background: var(--surface-soft);
    border-radius: 12px;
}
.product-page .comment-card:hover {
    background: var(--divider);
}
.product-page .avatar-initial {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
}
.product-page .comment-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 8px;
}
.product-page .comment-head strong {
    font-size: 1.05rem;
    color: var(--on-surface);
}
.product-page .comment-head time {
    font-size: 0.85rem;
    color: var(--on-surface-muted);
}
.product-page .comment-text {
    line-height: 1.65;
    color: var(--on-surface-soft);
}

.product-page .empty-state {
    text-align: center;
    padding: 40px 20px;
}
.product-page .empty-state .icon {
    font-size: 3rem;
    margin-bottom: 16px;
    color: var(--on-surface-muted);
}
.product-page .empty-state p {
    color: var(--on-surface-soft);
}

/* EMPTY */
.product-page .empty-product {
    text-align: center;
    padding: 60px 20px;
    background: var(--surface);
    border-radius: 16px;
    box-shadow: var(--shadow);
}
.product-page .empty-product .icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
    color: var(--secondary);
}
.product-page .empty-product h2 {
    font-size: 1.6rem;
    margin-bottom: 16px;
    color: var(--on-surface);
}

/* SIDEBAR */
.product-page .sidebar {
    position: sticky;
    top: 100px;
    height: fit-content;
    align-self: flex-start;
}
@media (max-width: 992px) {
    .product-page .sidebar { position: static; }
}
</style>