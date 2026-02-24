<?php
/**
 * search.php — صفحه نتایج جستجو
 * نمایش محصولات و مقالات یافت‌شده
 */
require_once("./include/header.php");

$q    = isset($_GET['q']) ? trim($_GET['q']) : '';
$like = '%' . $q . '%';

$products = [];
$posts    = [];

if ($q !== '') {
    $products = fetchAll($db,
        "SELECT id, name, `new-price` AS new_price, price AS old_price, pic, description
         FROM product
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY id DESC",
        [$like, $like]
    );

    $posts = fetchAll($db,
        "SELECT p.id, p.title, p.image, p.author, p.body, c.title AS cat_title
         FROM posts p
         LEFT JOIN categories c ON c.id = p.category_id
         WHERE p.title LIKE ? OR p.body LIKE ?
         ORDER BY p.id DESC",
        [$like, $like]
    );
}

$total = count($products) + count($posts);

function calcDiscPct($old, $new) {
    return $old > 0 ? round(($old - $new) / $old * 100) : 0;
}
function truncate($text, $len = 120) {
    $text = strip_tags($text);
    return mb_strlen($text) > $len ? mb_substr($text, 0, $len) . '...' : $text;
}
?>

<style>
/* ══════════════════════════════════════════
   search.php — صفحه نتایج جستجو
   ══════════════════════════════════════════ */
.srp-wrap {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2.5rem 1.5rem 5rem;
}

/* هدر صفحه */
.srp-head {
    margin-bottom: 2.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color, #e2e8f0);
}
.srp-head-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}
.srp-back {
    display: inline-flex; align-items: center; gap: .45rem;
    color: var(--text-secondary, #64748b);
    font-size: .88rem; font-weight: 600;
    text-decoration: none;
    padding: .5rem 1rem; border-radius: 10px;
    background: var(--bg-secondary, #f1f5f9);
    border: 1px solid var(--border-color, #e2e8f0);
    transition: all .2s;
    flex-shrink: 0;
}
.srp-back:hover {
    background: var(--accent-primary, #2563eb);
    color: #fff; border-color: transparent;
}
.srp-back svg { width: 16px; height: 16px; }

.srp-title-block { flex: 1; }
.srp-title {
    font-size: clamp(1.4rem, 3vw, 2rem);
    font-weight: 900;
    color: var(--text-primary, #0f172a);
    line-height: 1.2;
}
.srp-title span {
    color: var(--accent-primary, #2563eb);
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
    padding: .1rem .6rem; border-radius: 8px;
    font-style: italic;
}
.srp-count {
    font-size: .88rem;
    color: var(--text-secondary, #64748b);
    margin-top: .4rem;
}
.srp-count strong { color: var(--accent-primary, #2563eb); }

/* فرم سرچ در صفحه */
.srp-search-form {
    display: flex;
    gap: .75rem;
    margin-top: 1.5rem;
    max-width: 600px;
}
.srp-search-input {
    flex: 1;
    padding: .85rem 1.2rem;
    border: 1.5px solid var(--border-color, #e2e8f0);
    border-radius: 14px;
    font-size: .97rem;
    font-family: inherit;
    background: var(--bg-primary, #fff);
    color: var(--text-primary, #0f172a);
    outline: none;
    transition: all .3s;
    direction: rtl;
}
.srp-search-input:focus {
    border-color: var(--accent-primary, #2563eb);
    box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}
.srp-search-btn {
    padding: .85rem 1.8rem;
    border-radius: 14px;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff; font-weight: 700; font-size: .92rem;
    border: none; cursor: pointer; font-family: inherit;
    display: flex; align-items: center; gap: .5rem;
    transition: all .3s; white-space: nowrap;
}
.srp-search-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(37,99,235,.35); }
.srp-search-btn svg { width: 17px; height: 17px; }

/* حالت بدون نتیجه */
.srp-empty {
    text-align: center;
    padding: 5rem 2rem;
}
.srp-empty-icon { font-size: 5rem; margin-bottom: 1.5rem; display: block; }
.srp-empty h2 { font-size: 1.6rem; font-weight: 800; color: var(--text-primary, #0f172a); margin-bottom: .6rem; }
.srp-empty p  { color: var(--text-secondary, #64748b); font-size: .95rem; line-height: 1.75; }
.srp-empty-tips {
    display: flex; flex-wrap: wrap; gap: .5rem;
    justify-content: center; margin-top: 1.5rem;
}
.srp-empty-tip {
    padding: .45rem 1.1rem; border-radius: 50px;
    background: var(--bg-secondary, #f1f5f9);
    border: 1.5px solid var(--border-color, #e2e8f0);
    color: var(--text-secondary, #475569);
    font-size: .83rem; font-weight: 600;
    cursor: pointer; transition: all .2s;
    text-decoration: none;
}
.srp-empty-tip:hover { background: var(--accent-primary, #2563eb); color: #fff; border-color: transparent; }

/* بخش‌ها */
.srp-section { margin-bottom: 3rem; }
.srp-section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem;
    padding-bottom: .8rem;
    border-bottom: 2px solid var(--border-color, #e2e8f0);
}
.srp-section-title {
    display: flex; align-items: center; gap: .6rem;
    font-size: 1.2rem; font-weight: 800;
    color: var(--text-primary, #0f172a);
}
.srp-section-badge {
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff;
    padding: .2rem .7rem; border-radius: 50px;
    font-size: .75rem; font-weight: 700;
}

/* گرید محصولات */
.srp-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.5rem;
}
.srp-pcard {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 18px; overflow: hidden;
    transition: all .35s cubic-bezier(.4,0,.2,1);
    display: flex; flex-direction: column;
}
.srp-pcard:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,.12);
    border-color: var(--accent-primary, #2563eb);
}
.srp-pcard-img-wrap { position: relative; padding-top: 100%; overflow: hidden; }
.srp-pcard-img {
    position: absolute; top: 0; left: 0;
    width: 100%; height: 100%; object-fit: cover;
    transition: transform .5s ease;
}
.srp-pcard:hover .srp-pcard-img { transform: scale(1.07); }
.srp-pcard-disc {
    position: absolute; top: 12px; right: 12px;
    background: linear-gradient(135deg, #ef4444, #f97316);
    color: #fff; padding: .3rem .7rem; border-radius: 8px;
    font-size: .75rem; font-weight: 800;
    box-shadow: 0 4px 12px rgba(239,68,68,.35);
}
.srp-pcard-body { padding: 1.2rem; flex: 1; display: flex; flex-direction: column; }
.srp-pcard-name {
    font-size: 1rem; font-weight: 700;
    color: var(--text-primary, #0f172a);
    margin-bottom: .9rem; flex: 1; line-height: 1.4;
    text-decoration: none; display: block;
    transition: color .2s;
}
.srp-pcard-name:hover { color: var(--accent-primary, #2563eb); }
/* هایلایت کلمه سرچ */
.srp-pcard-name mark, .srp-post-title mark {
    background: #fde68a; color: #92400e;
    padding: 1px 3px; border-radius: 4px; font-weight: 700;
}
.srp-pcard-prices {
    display: flex; align-items: center; gap: .7rem;
    margin-bottom: 1rem; flex-wrap: wrap;
}
.srp-pcard-new { font-size: 1.1rem; font-weight: 800; color: #10b981; }
.srp-pcard-old { font-size: .82rem; color: var(--text-secondary, #94a3b8); text-decoration: line-through; }
.srp-pcard-btn {
    display: flex; align-items: center; justify-content: center; gap: .45rem;
    padding: .8rem;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff; border: none; border-radius: 12px;
    font-weight: 700; font-size: .88rem;
    cursor: pointer; font-family: inherit;
    text-decoration: none; transition: all .3s;
}
.srp-pcard-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(37,99,235,.35); }
.srp-pcard-btn svg { width: 17px; height: 17px; }

/* لیست مقالات */
.srp-posts-list { display: flex; flex-direction: column; gap: 1.2rem; }
.srp-post {
    background: var(--bg-primary, #fff);
    border: 1px solid var(--border-color, #e2e8f0);
    border-radius: 18px; overflow: hidden;
    display: flex; gap: 0;
    transition: all .35s cubic-bezier(.4,0,.2,1);
}
.srp-post:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(0,0,0,.1);
    border-color: var(--accent-primary, #2563eb);
}
.srp-post-img-wrap { width: 160px; flex-shrink: 0; overflow: hidden; }
.srp-post-img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s; }
.srp-post:hover .srp-post-img { transform: scale(1.07); }
.srp-post-body { padding: 1.4rem; flex: 1; display: flex; flex-direction: column; }
.srp-post-cat {
    display: inline-flex; align-items: center;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
    color: var(--accent-primary, #2563eb);
    padding: .2rem .75rem; border-radius: 6px;
    font-size: .72rem; font-weight: 700; margin-bottom: .7rem;
    width: fit-content;
}
.srp-post-title {
    font-size: 1.05rem; font-weight: 800;
    color: var(--text-primary, #0f172a);
    margin-bottom: .6rem; line-height: 1.4;
    text-decoration: none; display: block; transition: color .2s;
}
.srp-post-title:hover { color: var(--accent-primary, #2563eb); }
.srp-post-excerpt {
    font-size: .88rem; color: var(--text-secondary, #64748b);
    line-height: 1.75; flex: 1; margin-bottom: 1rem;
}
.srp-post-foot {
    display: flex; align-items: center; justify-content: space-between;
    padding-top: .8rem; border-top: 1px solid var(--border-color, #f1f5f9);
}
.srp-post-author { display: flex; align-items: center; gap: .5rem; color: var(--text-secondary, #64748b); font-size: .82rem; }
.srp-post-av {
    width: 28px; height: 28px; border-radius: 50%;
    background: linear-gradient(135deg, #1e3a8a, #3b82f6);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .78rem; flex-shrink: 0;
}
.srp-post-read {
    display: flex; align-items: center; gap: .3rem;
    color: var(--accent-primary, #2563eb);
    font-weight: 700; font-size: .83rem;
    text-decoration: none; transition: gap .25s;
}
.srp-post-read:hover { gap: .6rem; }
.srp-post-read svg { width: 14px; height: 14px; }

/* responsive */
@media (max-width: 768px) {
    .srp-products-grid { grid-template-columns: repeat(2, 1fr); gap: 1rem; }
    .srp-post { flex-direction: column; }
    .srp-post-img-wrap { width: 100%; height: 180px; }
    .srp-search-form { flex-direction: column; }
}
@media (max-width: 480px) {
    .srp-products-grid { grid-template-columns: 1fr; }
    .srp-wrap { padding: 1.5rem 1rem 4rem; }
}
</style>

<div class="srp-wrap">

    <!-- هدر صفحه -->
    <div class="srp-head">
        <div class="srp-head-row">
            <a href="javascript:history.back()" class="srp-back">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/></svg>
                بازگشت
            </a>
            <div class="srp-title-block">
                <?php if ($q): ?>
                    <h1 class="srp-title">نتایج جستجو برای «<span><?= escape($q) ?></span>»</h1>
                    <p class="srp-count">
                        <strong><?= $total ?></strong> نتیجه یافت شد
                        (<?= count($products) ?> محصول · <?= count($posts) ?> مقاله)
                    </p>
                <?php else: ?>
                    <h1 class="srp-title">🔍 جستجو</h1>
                    <p class="srp-count">عبارت مورد نظر را جستجو کنید</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- فرم سرچ -->
        <form action="search.php" method="GET" class="srp-search-form">
            <input class="srp-search-input"
                   type="text" name="q"
                   value="<?= escape($q) ?>"
                   placeholder="نام کتاب، نویسنده یا موضوع..."
                   autofocus>
            <button type="submit" class="srp-search-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                جستجو
            </button>
        </form>
    </div>

    <?php if ($q === ''): ?>
        <!-- بدون کوئری -->
        <div class="srp-empty">
            <span class="srp-empty-icon">🔍</span>
            <h2>دنبال چه کتابی هستید؟</h2>
            <p>نام کتاب، نویسنده یا موضوع مورد نظر را در کادر بالا وارد کنید.</p>
            <div class="srp-empty-tips">
                <?php foreach ($categories as $cat): ?>
                    <a href="search.php?q=<?= urlencode($cat['title']) ?>" class="srp-empty-tip">
                        <?= escape($cat['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php elseif ($total === 0): ?>
        <!-- بدون نتیجه -->
        <div class="srp-empty">
            <span class="srp-empty-icon">😕</span>
            <h2>نتیجه‌ای یافت نشد</h2>
            <p>
                متأسفانه برای «<strong><?= escape($q) ?></strong>» چیزی پیدا نکردیم.<br>
                کلمه دیگری امتحان کنید یا از دسته‌بندی‌ها استفاده کنید.
            </p>
            <div class="srp-empty-tips">
                <?php foreach ($categories as $cat): ?>
                    <a href="products.php?category=<?= $cat['id'] ?>" class="srp-empty-tip">
                        <?= escape($cat['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: ?>

        <!-- ══ محصولات ══ -->
        <?php if (!empty($products)): ?>
        <div class="srp-section">
            <div class="srp-section-head">
                <div class="srp-section-title">
                    📦 محصولات
                    <span class="srp-section-badge"><?= count($products) ?></span>
                </div>
                <a href="products.php?search=<?= urlencode($q) ?>" style="font-size:.85rem;color:var(--accent-primary);font-weight:700;text-decoration:none">
                    مشاهده همه ←
                </a>
            </div>
            <div class="srp-products-grid">
                <?php foreach ($products as $p):
                    $newP = (int)$p['new_price'];
                    $oldP = (int)$p['old_price'];
                    $disc = calcDiscPct($oldP, $newP);
                    // هایلایت نام
                    $nameHL = preg_replace('/(' . preg_quote(escape($q), '/') . ')/ui',
                        '<mark>$1</mark>', escape($p['name']));
                ?>
                <div class="srp-pcard">
                    <div class="srp-pcard-img-wrap">
                        <img class="srp-pcard-img"
                             src="./upload/products/<?= escape($p['pic']) ?>"
                             alt="<?= escape($p['name']) ?>"
                             loading="lazy">
                        <?php if ($disc > 0): ?>
                            <span class="srp-pcard-disc"><?= $disc ?>% تخفیف</span>
                        <?php endif; ?>
                    </div>
                    <div class="srp-pcard-body">
                        <a class="srp-pcard-name" href="single_product.php?product=<?= $p['id'] ?>">
                            <?= $nameHL ?>
                        </a>
                        <div class="srp-pcard-prices">
                            <span class="srp-pcard-new"><?= number_format($newP) ?> تومان</span>
                            <?php if ($oldP !== $newP): ?>
                                <span class="srp-pcard-old"><?= number_format($oldP) ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="single_product.php?product=<?= $p['id'] ?>" class="srp-pcard-btn">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17,18A2,2 0 0,1 19,20A2,2 0 0,1 17,22C15.89,22 15,21.1 15,20C15,18.89 15.89,18 17,18M1,2H4.27L5.21,4H20A1,1 0 0,1 21,5L17.3,11.97C16.96,12.58 16.3,13 15.55,13H8.1L7.2,14.63L7.17,14.75A0.25,0.25 0 0,0 7.42,15H19V17H7C5.89,17 5,16.1 5,15L6.6,11.59L3,4H1V2Z"/>
                            </svg>
                            مشاهده محصول
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- ══ مقالات ══ -->
        <?php if (!empty($posts)): ?>
        <div class="srp-section">
            <div class="srp-section-head">
                <div class="srp-section-title">
                    📝 مقالات
                    <span class="srp-section-badge"><?= count($posts) ?></span>
                </div>
                <a href="posts.php?search=<?= urlencode($q) ?>" style="font-size:.85rem;color:var(--accent-primary);font-weight:700;text-decoration:none">
                    مشاهده همه ←
                </a>
            </div>
            <div class="srp-posts-list">
                <?php foreach ($posts as $post):
                    $titleHL = preg_replace('/(' . preg_quote(escape($q), '/') . ')/ui',
                        '<mark>$1</mark>', escape($post['title']));
                ?>
                <article class="srp-post">
                    <div class="srp-post-img-wrap">
                        <img class="srp-post-img"
                             src="./upload/posts/<?= escape($post['image']) ?>"
                             alt="<?= escape($post['title']) ?>"
                             loading="lazy">
                    </div>
                    <div class="srp-post-body">
                        <?php if (!empty($post['cat_title'])): ?>
                            <span class="srp-post-cat"><?= escape($post['cat_title']) ?></span>
                        <?php endif; ?>
                        <a class="srp-post-title" href="single.php?post=<?= $post['id'] ?>">
                            <?= $titleHL ?>
                        </a>
                        <p class="srp-post-excerpt"><?= escape(truncate($post['body'], 150)) ?></p>
                        <div class="srp-post-foot">
                            <div class="srp-post-author">
                                <div class="srp-post-av"><?= mb_substr($post['author'], 0, 1) ?></div>
                                <?= escape($post['author']) ?>
                            </div>
                            <a class="srp-post-read" href="single.php?post=<?= $post['id'] ?>">
                                ادامه مطلب
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once("./include/footer.php"); ?>