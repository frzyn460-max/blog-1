<?php
/**
 * صفحه مقالات
 * نمایش تمام مقالات با فیلتر و جستجو
 */

// فراخوانی هدر
require_once("./include/header.php");

// دریافت پارامترهای فیلتر
$category_id = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;

// ساخت کوئری پایه
$query = "SELECT * FROM posts";
$params = [];

// اضافه کردن فیلتر دسته‌بندی
if ($category_id) {
    $query .= " WHERE category_id = ?";
    $params[] = $category_id;
}

// اضافه کردن جستجو
if ($search) {
    if ($category_id) {
        $query .= " AND (title LIKE ? OR body LIKE ?)";
    } else {
        $query .= " WHERE (title LIKE ? OR body LIKE ?)";
    }
    $searchParam = "%{$search}%";
    $params[] = $searchParam;
    $params[] = $searchParam;
}

$query .= " ORDER BY id DESC";

// اجرای کوئری
$posts = fetchAll($db, $query, $params);

// تابع کمکی برای کوتاه کردن متن
function truncateText($text, $length = 150) {
    $text = strip_tags($text);
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}
?>

<!-- کانتینر اصلی -->
<div class="page-wrapper">
    
    <!-- محتوای اصلی -->
    <main class="main-content">
        
        <!-- بخش مقالات -->
        <section class="section posts-section">
            <div class="section-header" data-aos="fade-up">
                <div class="section-title-wrapper">
                    <svg class="section-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/>
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
                            تمام مقالات
                        <?php endif; ?>
                    </h2>
                </div>
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
                        <a href="posts.php" class="btn-back">بازگشت به همه مقالات</a>
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
        .posts-grid {
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

        .posts-grid {
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