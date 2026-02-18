<?php
/**
 * صفحه سبد خرید - Midnight Blue Theme
 */

require_once("./include/header.php");

$cart_items = $_SESSION['cart'] ?? [];
$products = [];
$total_price = 0;

if (!empty($cart_items)) {
    $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
    $stmt = $db->prepare("SELECT * FROM product WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart_items));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($products as $product) {
        $pid = $product['id'];
        $price = $product['new-price'] ?? $product['price'];
        $total_price += $price * ($cart_items[$pid] ?? 1);
    }
}
?>

<link rel="stylesheet" href="./css/style.css">

<div class="cart-page-wrapper">
    <div class="container-custom">

        <!-- Breadcrumb -->
        <nav class="breadcrumb" data-aos="fade-down">
            <a href="index.php">خانه</a>
            <span class="separator">›</span>
            <span class="current">سبد خرید</span>
        </nav>

        <div class="cart-header" data-aos="fade-up">
            <div class="cart-title-wrapper">
                <div class="cart-title-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="cart-title">سبد خرید</h1>
                    <p class="cart-subtitle">
                        <?= empty($cart_items) ? 'سبد خرید شما خالی است' : count($cart_items) . ' محصول در سبد شما' ?>
                    </p>
                </div>
            </div>
            <?php if (!empty($cart_items)): ?>
                <a href="products.php" class="continue-shopping">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    ادامه خرید
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($cart_items)): ?>

            <!-- سبد خالی -->
            <div class="empty-cart" data-aos="zoom-in">
                <div class="empty-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5C21,5.17 20.95,5.34 20.88,5.5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15C5,14.65 5.09,14.32 5.24,14.04L6.6,11.59L3,4H1V2M7,18A2,2 0 0,1 9,20A2,2 0 0,1 7,22C5.89,22 5,21.1 5,20C5,18.89 5.89,18 7,18M16,11L18.78,6H6.14L8.5,11H16Z"/>
                    </svg>
                </div>
                <h2>سبد خرید شما خالی است!</h2>
                <p>هنوز محصولی به سبد خرید اضافه نکرده‌اید.</p>
                <a href="products.php" class="btn-shop-now">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/>
                    </svg>
                    شروع خرید
                </a>
            </div>

        <?php else: ?>

            <div class="cart-layout">

                <!-- آیتم‌های سبد -->
                <div class="cart-items-wrapper" data-aos="fade-up">
                    <div class="cart-items-list" id="cartItemsList">
                        <?php foreach ($products as $index => $product):
                            $pid      = $product['id'];
                            $quantity = $cart_items[$pid] ?? 1;
                            $price    = $product['new-price'] ?? $product['price'];
                            $subtotal = $price * $quantity;
                            $total_price += 0; // قبلاً محاسبه شد
                        ?>
                            <div class="cart-item" id="item-<?= $pid ?>" data-aos="fade-up" data-aos-delay="<?= $index * 80 ?>">

                                <div class="item-image">
                                    <img src="./upload/products/<?= escape($product['pic']) ?>"
                                         alt="<?= escape($product['name']) ?>">
                                </div>

                                <div class="item-details">
                                    <h3 class="item-name">
                                        <a href="single_product.php?product=<?= $pid ?>">
                                            <?= escape($product['name']) ?>
                                        </a>
                                    </h3>
                                    <p class="item-unit-price">
                                        قیمت واحد: <strong><?= number_format($price) ?> تومان</strong>
                                    </p>
                                </div>

                                <div class="item-quantity">
                                    <button class="qty-btn minus" onclick="changeQty(<?= $pid ?>, -1)">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,13H5V11H19V13Z"/></svg>
                                    </button>
                                    <input type="number" min="1"
                                           class="qty-input"
                                           id="qty-<?= $pid ?>"
                                           value="<?= $quantity ?>"
                                           onchange="updateQuantity(<?= $pid ?>, this)">
                                    <button class="qty-btn plus" onclick="changeQty(<?= $pid ?>, 1)">
                                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z"/></svg>
                                    </button>
                                </div>

                                <div class="item-subtotal">
                                    <span class="subtotal-label">جمع:</span>
                                    <span class="subtotal-value" id="subtotal-<?= $pid ?>">
                                        <?= number_format($subtotal) ?> تومان
                                    </span>
                                </div>

                                <button class="item-remove" onclick="removeFromCart(<?= $pid ?>)" title="حذف">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                    </svg>
                                </button>

                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- خلاصه سفارش -->
                <div class="order-summary" data-aos="fade-up" data-aos-delay="200">

                    <div class="summary-card">
                        <h2 class="summary-title">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M10,13H7V11H10V13M14,13H11V11H14V13M10,17H7V15H10V17M14,17H11V15H14V17Z"/>
                            </svg>
                            خلاصه سفارش
                        </h2>

                        <div class="summary-rows">
                            <div class="summary-row">
                                <span>تعداد محصولات</span>
                                <span id="totalItems"><?= array_sum($cart_items) ?> عدد</span>
                            </div>
                            <div class="summary-row">
                                <span>جمع محصولات</span>
                                <span id="subtotalDisplay"><?= number_format($total_price) ?> تومان</span>
                            </div>
                            <div class="summary-row">
                                <span>هزینه ارسال</span>
                                <span class="free-ship">
                                    <?php if ($total_price >= 200000): ?>
                                        <span class="badge-free">رایگان 🎉</span>
                                    <?php else: ?>
                                        <span>20,000 تومان</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <div class="summary-total">
                            <span>مبلغ قابل پرداخت</span>
                            <span id="totalDisplay">
                                <?php
                                $shipping = $total_price >= 200000 ? 0 : 20000;
                                echo number_format($total_price + $shipping);
                                ?> تومان
                            </span>
                        </div>

                        <?php if ($total_price < 200000): ?>
                            <div class="free-ship-notice">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11,9H13V7H11M12,20C7.59,20 4,16.41 4,12C4,7.59 7.59,4 12,4C16.41,4 20,7.59 20,12C20,16.41 16.41,20 12,20M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2M11,17H13V11H11V17Z"/>
                                </svg>
                                <?= number_format(200000 - $total_price) ?> تومان تا ارسال رایگان
                            </div>
                        <?php endif; ?>

                        <button class="btn-checkout" onclick="checkout()">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,8H4V6H20M20,18H4V12H20M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.11,4 20,4Z"/>
                            </svg>
                            پرداخت و ثبت سفارش
                        </button>

                        <a href="products.php" class="btn-continue">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                            </svg>
                            ادامه خرید
                        </a>

                        <div class="secure-badges">
                            <div class="secure-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                                </svg>
                                <span>پرداخت امن</span>
                            </div>
                            <div class="secure-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M18,18.5A1.5,1.5 0 0,1 16.5,17A1.5,1.5 0 0,1 18,15.5A1.5,1.5 0 0,1 19.5,17A1.5,1.5 0 0,1 18,18.5M19.5,9.5L21.46,12H17V9.5M6,18.5A1.5,1.5 0 0,1 4.5,17A1.5,1.5 0 0,1 6,15.5A1.5,1.5 0 0,1 7.5,17A1.5,1.5 0 0,1 6,18.5M20,8H17V4H3C1.89,4 1,4.89 1,6V17H3A3,3 0 0,0 6,20A3,3 0 0,0 9,17H15A3,3 0 0,0 18,20A3,3 0 0,0 21,17H23V12L20,8Z"/>
                                </svg>
                                <span>ارسال سریع</span>
                            </div>
                            <div class="secure-item">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22A10,10 0 0,1 2,12A10,10 0 0,1 12,2M12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4M11,7H13V13H11V7M11,15H13V17H11V15Z"/>
                                </svg>
                                <span>پشتیبانی 24/7</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast-cart"></div>


<script>
// ═══════════════════════════════════
// 🛒 مدیریت سبد خرید
// ═══════════════════════════════════

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className   = `toast-cart toast-${type} show`;
    setTimeout(() => toast.classList.remove('show'), 3000);
}

// حذف محصول
function removeFromCart(productId) {
    if (!confirm('آیا مطمئن هستید؟')) return;

    fetch('remove_from_cart.php?product_id=' + productId)
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                const row = document.getElementById('item-' + productId);
                row.style.transition = 'all 0.4s ease';
                row.style.opacity    = '0';
                row.style.transform  = 'translateX(30px)';
                setTimeout(() => {
                    row.remove();
                    updateSummary();
                    showToast('محصول از سبد حذف شد', 'success');
                    // اگه سبد خالی شد reload کن
                    if (!document.querySelectorAll('.cart-item').length) {
                        location.reload();
                    }
                }, 400);
            } else {
                showToast('خطا در حذف محصول', 'error');
            }
        })
        .catch(() => showToast('خطا در ارتباط با سرور', 'error'));
}

// تغییر تعداد با دکمه + و -
function changeQty(productId, delta) {
    const input = document.getElementById('qty-' + productId);
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    input.value = val;
    updateQuantity(productId, input);
}

// آپدیت تعداد
function updateQuantity(productId, input) {
    let quantity = parseInt(input.value);
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
        input.value = quantity;
    }

    // انیمیشن input
    input.style.transform = 'scale(1.1)';
    setTimeout(() => input.style.transform = 'scale(1)', 150);

    fetch('update_cart.php', {
        method : 'POST',
        headers: { 'Content-Type': 'application/json' },
        body   : JSON.stringify({ product_id: productId, quantity })
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            updateSubtotal(productId, quantity);
            updateSummary();
        } else {
            showToast('خطا در بروزرسانی تعداد', 'error');
        }
    })
    .catch(() => showToast('خطا در ارتباط با سرور', 'error'));
}

// آپدیت قیمت ردیف
function updateSubtotal(productId, quantity) {
    const unitPriceEl = document.querySelector(`#item-${productId} .item-unit-price strong`);
    if (!unitPriceEl) return;

    const priceStr  = unitPriceEl.textContent.replace(/[^0-9]/g, '');
    const unitPrice = parseInt(priceStr);
    const subtotal  = unitPrice * quantity;

    const subtotalEl = document.getElementById('subtotal-' + productId);
    if (subtotalEl) {
        subtotalEl.textContent = subtotal.toLocaleString('fa-IR') + ' تومان';
    }
}

// آپدیت خلاصه سفارش
function updateSummary() {
    let total     = 0;
    let itemCount = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
        const pid      = item.id.replace('item-', '');
        const qty      = parseInt(document.getElementById('qty-' + pid)?.value || 0);
        const priceStr = item.querySelector('.item-unit-price strong')?.textContent.replace(/[^0-9]/g, '') || '0';
        const price    = parseInt(priceStr);

        total     += price * qty;
        itemCount += qty;
    });

    const shipping = total >= 200000 ? 0 : 20000;
    const grand    = total + shipping;

    const fmt = n => n.toLocaleString('fa-IR');

    const sub   = document.getElementById('subtotalDisplay');
    const tot   = document.getElementById('totalDisplay');
    const items = document.getElementById('totalItems');

    if (sub)   sub.textContent   = fmt(total) + ' تومان';
    if (tot)   tot.textContent   = fmt(grand) + ' تومان';
    if (items) items.textContent = itemCount + ' عدد';
}

// پرداخت
function checkout() {
    alert('به زودی درگاه پرداخت اضافه می‌شود');
}

// AOS
document.addEventListener('DOMContentLoaded', () => {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('aos-animate'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('[data-aos]').forEach(el => obs.observe(el));
});
</script>

<style>
/* ═══════════════════════════════════════════════════════════
   🛒 صفحه سبد خرید - Midnight Blue
   ═══════════════════════════════════════════════════════════ */

.cart-page-wrapper {
    padding: 2rem 0 5rem;
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
    gap: .75rem;
    padding: 1rem 0 2rem;
    font-size: .95rem;
}
.breadcrumb a { color: var(--text-secondary); text-decoration: none; transition: color .3s; }
.breadcrumb a:hover { color: var(--accent-primary); }
.breadcrumb .separator { color: var(--text-tertiary); }
.breadcrumb .current { color: var(--text-primary); font-weight: 600; }

/* هدر صفحه */
.cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.cart-title-wrapper {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.cart-title-icon {
    width: 70px;
    height: 70px;
    background: var(--gradient-primary);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}
.cart-title-icon svg { width: 36px; height: 36px; }

.cart-title {
    font-size: 2rem;
    font-weight: 900;
    color: var(--text-primary);
    margin-bottom: .3rem;
}

.cart-subtitle {
    color: var(--text-secondary);
    font-size: 1rem;
}

.continue-shopping {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    color: var(--accent-primary);
    text-decoration: none;
    font-weight: 600;
    border: 2px solid var(--accent-primary);
    padding: .75rem 1.5rem;
    border-radius: 12px;
    transition: all .3s;
}
.continue-shopping:hover {
    background: var(--accent-primary);
    color: white;
}
.continue-shopping svg { width: 20px; height: 20px; }

/* ═══════ سبد خالی ═══════ */
.empty-cart {
    text-align: center;
    padding: 6rem 2rem;
    background: var(--bg-primary);
    border-radius: 30px;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.empty-icon {
    width: 120px;
    height: 120px;
    background: var(--bg-secondary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}
.empty-icon svg { width: 60px; height: 60px; color: var(--text-tertiary); }

.empty-cart h2 { font-size: 1.8rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; }
.empty-cart p  { color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 2.5rem; }

.btn-shop-now {
    display: inline-flex;
    align-items: center;
    gap: .75rem;
    background: var(--gradient-primary);
    color: white;
    padding: 1.2rem 3rem;
    border-radius: 15px;
    font-weight: 700;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all .3s;
}
.btn-shop-now:hover { transform: translateY(-3px); box-shadow: var(--shadow-blue); }
.btn-shop-now svg { width: 24px; height: 24px; }

/* ═══════ Layout ═══════ */
.cart-layout {
    display: grid;
    grid-template-columns: 1fr 420px;
    gap: 3rem;
    align-items: start;
}

/* ═══════ آیتم‌های سبد ═══════ */
.cart-items-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cart-item {
    display: grid;
    grid-template-columns: 100px 1fr auto auto auto;
    align-items: center;
    gap: 1.5rem;
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 1.5rem;
    border: 1px solid var(--border-color);
    box-shadow: var(--shadow-sm);
    transition: all .3s ease;
}

.cart-item:hover {
    box-shadow: var(--shadow-blue);
    transform: translateY(-3px);
}

.item-image {
    width: 100px;
    height: 100px;
    border-radius: 15px;
    overflow: hidden;
    flex-shrink: 0;
}
.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s;
}
.cart-item:hover .item-image img { transform: scale(1.08); }

.item-name {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: .5rem;
}
.item-name a { color: var(--text-primary); text-decoration: none; transition: color .3s; }
.item-name a:hover { color: var(--accent-primary); }

.item-unit-price {
    color: var(--text-secondary);
    font-size: .95rem;
}
.item-unit-price strong { color: var(--success); }

/* کنترل تعداد */
.item-quantity {
    display: flex;
    align-items: center;
    gap: .5rem;
    background: var(--bg-secondary);
    padding: .4rem;
    border-radius: 12px;
}

.qty-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .2s;
    background: var(--bg-primary);
    color: var(--text-primary);
}
.qty-btn:hover { background: var(--accent-primary); color: white; }
.qty-btn svg { width: 18px; height: 18px; }

.qty-input {
    width: 55px;
    padding: .5rem;
    text-align: center;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 700;
    background: var(--bg-primary);
    color: var(--text-primary);
    font-family: inherit;
    transition: all .3s;
}
.qty-input:focus { outline: none; border-color: var(--accent-primary); }

/* قیمت جزء */
.item-subtotal {
    text-align: center;
    min-width: 130px;
}
.subtotal-label { display: block; color: var(--text-secondary); font-size: .85rem; margin-bottom: .3rem; }
.subtotal-value { font-weight: 800; font-size: 1.1rem; color: var(--accent-primary); }

/* دکمه حذف */
.item-remove {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    border: none;
    background: rgba(239, 68, 68, .1);
    color: var(--error);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .3s;
    flex-shrink: 0;
}
.item-remove:hover { background: var(--error); color: white; transform: scale(1.1); }
.item-remove svg { width: 20px; height: 20px; }

/* ═══════ خلاصه سفارش ═══════ */
.order-summary { position: sticky; top: 100px; }

.summary-card {
    background: var(--bg-primary);
    border-radius: 25px;
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.summary-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: .75rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}
.summary-title svg { width: 28px; height: 28px; color: var(--accent-primary); }

.summary-rows { margin-bottom: 1.5rem; }

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-secondary);
    font-size: .95rem;
}
.summary-row:last-child { border-bottom: none; }

.badge-free {
    background: rgba(16, 185, 129, .15);
    color: var(--success);
    padding: .3rem .8rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: .9rem;
}

.summary-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--gradient-primary);
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 1.5rem;
    color: white;
    font-weight: 800;
    font-size: 1.2rem;
}

.free-ship-notice {
    display: flex;
    align-items: center;
    gap: .5rem;
    background: rgba(245, 158, 11, .1);
    color: var(--warning);
    padding: 1rem;
    border-radius: 12px;
    font-size: .9rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}
.free-ship-notice svg { width: 20px; height: 20px; flex-shrink: 0; }

.btn-checkout {
    width: 100%;
    background: var(--gradient-primary);
    color: white;
    padding: 1.3rem;
    border: none;
    border-radius: 15px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .75rem;
    transition: all .3s;
    font-family: inherit;
    margin-bottom: 1rem;
}
.btn-checkout:hover { transform: translateY(-3px); box-shadow: var(--shadow-blue); }
.btn-checkout svg { width: 22px; height: 22px; }

.btn-continue {
    width: 100%;
    background: transparent;
    color: var(--accent-primary);
    border: 2px solid var(--accent-primary);
    padding: 1rem;
    border-radius: 15px;
    font-size: 1rem;
    font-weight: 700;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .75rem;
    transition: all .3s;
    margin-bottom: 2rem;
}
.btn-continue:hover { background: var(--accent-primary); color: white; }
.btn-continue svg { width: 20px; height: 20px; }

.secure-badges {
    display: flex;
    justify-content: space-around;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
}
.secure-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .4rem;
    color: var(--text-secondary);
    font-size: .8rem;
}
.secure-item svg { width: 28px; height: 28px; color: var(--accent-primary); }

/* ═══════ Toast ═══════ */
.toast-cart {
    position: fixed;
    top: 100px;
    right: -400px;
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,.15);
    z-index: 9999;
    font-weight: 600;
    min-width: 280px;
    transition: right .4s cubic-bezier(.4,0,.2,1);
    border-right: 5px solid;
}
body.dark-mode .toast-cart { background: var(--bg-primary); box-shadow: 0 10px 40px rgba(0,0,0,.5); }
.toast-cart.show         { right: 2rem; }
.toast-cart.toast-success { border-color: var(--success); color: var(--success); }
.toast-cart.toast-error   { border-color: var(--error); color: var(--error); }

/* ═══════ Responsive ═══════ */
@media (max-width: 1100px) {
    .cart-layout { grid-template-columns: 1fr; }
    .order-summary { position: static; }
}

@media (max-width: 768px) {
    .cart-item {
        grid-template-columns: 80px 1fr;
        grid-template-rows: auto auto auto;
        gap: 1rem;
    }
    .item-image { width: 80px; height: 80px; }
    .item-quantity, .item-subtotal, .item-remove {
        grid-column: 1 / -1;
        justify-self: start;
    }
    .item-subtotal { justify-self: start; }
    .item-remove   { justify-self: end; margin-top: -2.5rem; }
}

@media (max-width: 480px) {
    .cart-title { font-size: 1.5rem; }
    .summary-card { padding: 1.5rem; }
    .toast-cart { right: 1rem; left: 1rem; min-width: auto; }
}

/* AOS */
[data-aos]            { opacity: 0; transition: all .6s ease; }
[data-aos].aos-animate { opacity: 1; }
[data-aos="fade-up"]            { transform: translateY(30px); }
[data-aos="fade-up"].aos-animate { transform: translateY(0); }
[data-aos="fade-down"]            { transform: translateY(-30px); }
[data-aos="fade-down"].aos-animate { transform: translateY(0); }
[data-aos="zoom-in"]            { transform: scale(.9); }
[data-aos="zoom-in"].aos-animate { transform: scale(1); }
</style>