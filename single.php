<?php
/**
 * صفحه نمایش مقاله - Midnight Blue Theme
 * طراحی مدرن و سازگار با سایت
 */

require_once("./include/header.php");

$post = null;
$comments = [];
$category_title = '';
$related_posts = [];

if (isset($_GET['post'])) {
    $post_id = filter_var($_GET['post'], FILTER_VALIDATE_INT);
    
    if ($post_id) {
        // دریافت مقاله
        $post = fetchOne($db, 'SELECT * FROM posts WHERE id = ?', [$post_id]);

        if ($post) {
            // دریافت دسته‌بندی
            $category = fetchOne($db, "SELECT title FROM categories WHERE id = ?", [$post['category_id']]);
            $category_title = $category ? escape($category['title']) : 'دسته‌بندی نامشخص';

            // دریافت نظرات - بدون فیلتر status تا همه نظرات نمایش داده شوند
            try {
                $comments = fetchAll($db, "SELECT name, comment, created_at FROM comments WHERE post_id = ? ORDER BY id DESC", [$post_id]);
            } catch (PDOException $e) {
                try {
                    $comments = fetchAll($db, "SELECT name, comment FROM comments WHERE post_id = ? ORDER BY id DESC", [$post_id]);
                } catch (PDOException $e2) {
                    $comments = [];
                }
            }
            
            // مقالات مرتبط
            $related_posts = fetchAll($db, "SELECT * FROM posts WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 3", [$post['category_id'], $post_id]);
        }
    }
}

// پردازش فرم نظر
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_comment']) && $post) {
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
                "INSERT INTO comments (name, comment, post_id, status, created_at) VALUES (?, ?, ?, 1, NOW())",
                "INSERT INTO comments (name, comment, post_id, status)             VALUES (?, ?, ?, 1)",
                "INSERT INTO comments (name, comment, post_id, created_at)         VALUES (?, ?, ?, NOW())",
                "INSERT INTO comments (name, comment, post_id)                     VALUES (?, ?, ?)",
            ];

            $done = false;
            foreach ($queries as $sql) {
                try {
                    $result = executeQuery($db, $sql, [$name, $comment, $post_id]);
                    if ($result) {
                        $done = true;
                        break;
                    }
                } catch (PDOException $e) {
                    continue;
                }
            }

            if ($done) {
                $success_msg = 'نظر شما با موفقیت ثبت شد!';
                // دوباره دریافت نظرات
                try {
                    $comments = fetchAll($db, "SELECT name, comment, created_at FROM comments WHERE post_id = ? ORDER BY id DESC", [$post_id]);
                } catch (PDOException $e) {
                    $comments = fetchAll($db, "SELECT name, comment FROM comments WHERE post_id = ? ORDER BY id DESC", [$post_id]);
                }
            } else {
                $error_msg = 'خطایی رخ داد. لطفاً با مدیر سایت تماس بگیرید.';
            }
        }
    }
}
?>

<link rel="stylesheet" href="./css/style.css">

<div class="post-page-wrapper">
    <div class="container-custom">
        
        <?php if ($post): ?>
            
            <!-- Breadcrumb -->
            <nav class="breadcrumb" data-aos="fade-down">
                <a href="index.php">خانه</a>
                <span class="separator">›</span>
                <a href="posts.php">مقالات</a>
                <span class="separator">›</span>
                <span class="current"><?= escape($post['title']) ?></span>
            </nav>

            <div class="post-layout">
                
                <!-- محتوای اصلی -->
                <main class="post-main">
                    
                    <!-- مقاله اصلی -->
                    <article class="post-article" data-aos="fade-up">
                        
                        <div class="post-featured-image">
                            <img src="./upload/posts/<?= escape($post['image']) ?>" 
                                 alt="<?= escape($post['title']) ?>">
                            <div class="image-overlay"></div>
                        </div>

                        <div class="post-content-wrapper">
                            
                            <header class="post-header">
                                <span class="post-category-badge"><?= $category_title ?></span>
                                <h1 class="post-title"><?= escape($post['title']) ?></h1>
                                
                                <div class="post-meta">
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                        </svg>
                                        <span><?= escape($post['author']) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z"/>
                                        </svg>
                                        <span><?= date('Y/m/d', strtotime($post['created_at'] ?? 'now')) ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5M12,17C9.24,17 7,14.76 7,12C7,9.24 9.24,7 12,7C14.76,7 17,9.24 17,12C17,14.76 14.76,17 12,17M12,9C10.34,9 9,10.34 9,12C9,13.66 10.34,15 12,15C13.66,15 15,13.66 15,12C15,10.34 13.66,9 12,9Z"/>
                                        </svg>
                                        <span><?= rand(500, 2000) ?> بازدید</span>
                                    </div>
                                    <div class="meta-item">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10Z"/>
                                        </svg>
                                        <span><?= count($comments) ?> نظر</span>
                                    </div>
                                </div>
                            </header>

                            <div class="post-body">
                                <?= nl2br(escape($post['body'])) ?>
                            </div>

                            <footer class="post-footer">
                                <div class="post-tags">
                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M5.5,7A1.5,1.5 0 0,1 4,5.5A1.5,1.5 0 0,1 5.5,4A1.5,1.5 0 0,1 7,5.5A1.5,1.5 0 0,1 5.5,7M21.41,11.58L12.41,2.58C12.05,2.22 11.55,2 11,2H4C2.89,2 2,2.89 2,4V11C2,11.55 2.22,12.05 2.59,12.41L11.58,21.41C11.95,21.77 12.45,22 13,22C13.55,22 14.05,21.77 14.41,21.41L21.41,14.41C21.78,14.05 22,13.55 22,13C22,12.45 21.77,11.95 21.41,11.58Z"/>
                                    </svg>
                                    <span>برچسب:</span>
                                    <a href="#" class="tag"><?= $category_title ?></a>
                                </div>

                                <div class="post-share">
                                    <span>اشتراک‌گذاری:</span>
                                    <a href="#" class="share-btn twitter" title="توییتر">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.70,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="share-btn telegram" title="تلگرام">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M9.78,18.65L10.06,14.42L17.74,7.5C18.08,7.19 17.67,7.04 17.22,7.31L7.74,13.3L3.64,12C2.76,11.75 2.75,11.14 3.84,10.7L19.81,4.54C20.54,4.21 21.24,4.72 20.96,5.84L18.24,18.65C18.05,19.56 17.5,19.78 16.74,19.36L12.6,16.3L10.61,18.23C10.38,18.46 10.19,18.65 9.78,18.65Z"/>
                                        </svg>
                                    </a>
                                    <a href="#" class="share-btn linkedin" title="لینکدین">
                                        <svg viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3H19M18.5,18.5V13.2A3.26,3.26 0 0,0 15.24,9.94C14.39,9.94 13.4,10.46 12.92,11.24V10.13H10.13V18.5H12.92V13.57C12.92,12.8 13.54,12.17 14.31,12.17A1.4,1.4 0 0,1 15.71,13.57V18.5H18.5M6.88,8.56A1.68,1.68 0 0,0 8.56,6.88C8.56,5.95 7.81,5.19 6.88,5.19A1.69,1.69 0 0,0 5.19,6.88C5.19,7.81 5.95,8.56 6.88,8.56M8.27,18.5V10.13H5.5V18.5H8.27Z"/>
                                        </svg>
                                    </a>
                                </div>
                            </footer>

                        </div>
                    </article>

                    <!-- نظرات -->
                    <section class="comments-section" data-aos="fade-up">
                        <div class="section-head">
                            <h2 class="section-title">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9M10,16V19.08L13.08,16H20V4H4V16H10M6,7H18V9H6V7M6,11H15V13H6V11Z"/>
                                </svg>
                                نظرات
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
                            
                            <button type="submit" name="post_comment" class="btn-submit">
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
                                            <p class="comment-text"><?= nl2br(escape($comment['comment'] ?? '')) ?></p>
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

                    <!-- مقالات مرتبط -->
                    <?php if (!empty($related_posts)): ?>
                        <section class="related-posts" data-aos="fade-up">
                            <h2 class="section-title">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19,5V7H15V5H19M9,5V11H5V5H9M19,13V19H15V13H19M9,17V19H5V17H9M21,3H13V9H21V3M11,3H3V13H11V3M21,11H13V21H21V11M11,15H3V21H11V15Z"/>
                                </svg>
                                مقالات مرتبط
                            </h2>
                            <div class="related-grid">
                                <?php foreach ($related_posts as $related): ?>
                                    <a href="single.php?post=<?= $related['id'] ?>" class="related-card">
                                        <img src="./upload/posts/<?= escape($related['image']) ?>" alt="<?= escape($related['title']) ?>">
                                        <div class="related-content">
                                            <h3><?= escape($related['title']) ?></h3>
                                            <p><?= mb_substr(strip_tags($related['body']), 0, 80) ?>...</p>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </section>
                    <?php endif; ?>

                </main>

                <!-- سایدبار -->
                <aside class="post-sidebar">
                    <?php require_once("./include/sidebar.php"); ?>
                </aside>

            </div>

        <?php else: ?>
            
            <div class="post-not-found">
                <div class="not-found-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M15,18V16H6V18H15M18,14V12H6V14H18Z"/>
                    </svg>
                </div>
                <h2>مقاله مورد نظر یافت نشد!</h2>
                <p>متأسفانه مقاله‌ای با این شناسه وجود ندارد.</p>
                <a href="posts.php" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20,11V13H8L13.5,18.5L12.08,19.92L4.16,12L12.08,4.08L13.5,5.5L8,11H20Z"/>
                    </svg>
                    بازگشت به مقالات
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<?php require_once("./include/footer.php"); ?>

<script>
// انیمیشن AOS
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
   📝 صفحه مقاله - Midnight Blue Theme
   ═══════════════════════════════════════════════════════════ */

.post-page-wrapper {
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
.post-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 3rem;
    align-items: start;
}

/* مقاله اصلی */
.post-article {
    background: var(--bg-primary);
    border-radius: 30px;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.post-featured-image {
    position: relative;
    width: 100%;
    height: 500px;
    overflow: hidden;
}

.post-featured-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
}

.post-content-wrapper {
    padding: 3rem;
}

.post-header {
    margin-bottom: 2rem;
}

.post-category-badge {
    display: inline-block;
    background: var(--accent-light);
    color: var(--accent-primary);
    padding: 0.5rem 1.2rem;
    border-radius: 12px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

body.dark-mode .post-category-badge {
    background: rgba(30, 58, 138, 0.2);
}

.post-title {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--text-primary);
    line-height: 1.3;
    margin-bottom: 1.5rem;
}

.post-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
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

.post-body {
    color: var(--text-secondary);
    font-size: 1.1rem;
    line-height: 2;
    text-align: justify;
    margin-bottom: 3rem;
}

.post-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    border-top: 2px solid var(--border-color);
    flex-wrap: wrap;
    gap: 2rem;
}

.post-tags {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.post-tags svg {
    width: 24px;
    height: 24px;
    color: var(--accent-primary);
}

.post-tags span {
    color: var(--text-secondary);
    font-weight: 600;
}

.tag {
    background: var(--bg-secondary);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.tag:hover {
    background: var(--accent-primary);
    color: white;
}

.post-share {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.post-share span {
    color: var(--text-secondary);
    font-weight: 600;
}

.share-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid var(--border-color);
}

.share-btn:hover {
    transform: translateY(-3px);
}

.share-btn svg {
    width: 22px;
    height: 22px;
}

.share-btn.twitter {
    color: #1da1f2;
}

.share-btn.twitter:hover {
    background: #1da1f2;
    color: white;
    border-color: #1da1f2;
}

.share-btn.telegram {
    color: #0088cc;
}

.share-btn.telegram:hover {
    background: #0088cc;
    color: white;
    border-color: #0088cc;
}

.share-btn.linkedin {
    color: #0077b5;
}

.share-btn.linkedin:hover {
    background: #0077b5;
    color: white;
    border-color: #0077b5;
}

/* نظرات */
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

/* مقالات مرتبط */
.related-posts {
    margin-top: 3rem;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
    aspect-ratio: 16/9;
    object-fit: cover;
}

.related-content {
    padding: 1.5rem;
}

.related-card h3 {
    color: var(--text-primary);
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.related-card p {
    color: var(--text-secondary);
    font-size: 0.95rem;
    line-height: 1.6;
}

/* مقاله یافت نشد */
.post-not-found {
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

.post-not-found h2 {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.post-not-found p {
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

/* Sidebar */
.post-sidebar {
    position: sticky;
    top: 100px;
}

/* Responsive */
@media (max-width: 1200px) {
    .post-layout {
        grid-template-columns: 1fr;
    }

    .post-sidebar {
        position: static;
    }
}

@media (max-width: 768px) {
    .post-article {
        border-radius: 20px;
    }

    .post-featured-image {
        height: 300px;
    }

    .post-content-wrapper {
        padding: 2rem;
    }

    .post-title {
        font-size: 1.8rem;
    }

    .post-body {
        font-size: 1rem;
    }

    .post-footer {
        flex-direction: column;
        align-items: flex-start;
    }

    .comments-section {
        padding: 2rem;
        border-radius: 20px;
    }

    .related-grid {
        grid-template-columns: 1fr;
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