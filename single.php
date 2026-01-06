<?php
include("./include/header.php");

if (isset($_GET['post'])) {
    $post_id = (int)$_GET['post']; // ✅ امن‌سازی

    $post = $db->prepare('SELECT * FROM posts WHERE id = :id');
    $post->execute(['id' => $post_id]);
    $post = $post->fetch();
}

if (isset($_POST['post_comment'])) {
    if (trim($_POST['name']) !== "" && trim($_POST['comment']) !== "") {
        $name = $_POST['name'];
        $comment = $_POST['comment'];

        $comment_insert = $db->prepare("INSERT INTO comments (name, comment, post_id) VALUES (:name, :comment, :post_id)");
        $comment_insert->execute(['name' => $name, 'comment' => $comment, 'post_id' => $post_id]);

        header("Location: single.php?post=$post_id");
        exit();
    } else {
        echo "<div class='error-message'>فیلدها نباید خالی باشند</div>";
    }
}
?>

<section class="post-section">
    <div class="container-fluid">
        <div class="row">
            <main class="main-post-content">
                <div class="post-container">
                    <?php if ($post): 
                        $category_id = (int)$post['category_id'];
                        $post_category = $db->query("SELECT * FROM categories WHERE id = $category_id")->fetch();
                        $comments = $db->prepare("SELECT * FROM comments WHERE post_id = :id AND status = '1'");
                        $comments->execute(['id' => $post['id']]);
                    ?>
                        <article class="post-detail">
                            <img src="./upload/posts/<?php echo htmlspecialchars($post['image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="post-image-full" />

                            <div class="post-text-container">
                                <header class="post-header">
                                    <h1 class="post-title"><?php echo htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                                    <span class="post-category">
                                        <?php echo $post_category ? htmlspecialchars($post_category['title'], ENT_QUOTES, 'UTF-8') : 'دسته‌بندی نامشخص'; ?>
                                    </span>
                                </header>

                                <section class="post-body-text">
                                    <p><?php echo nl2br(htmlspecialchars($post['body'], ENT_QUOTES, 'UTF-8')); ?></p>
                                </section>

                                <p class="post-author">نویسنده: <?php echo htmlspecialchars($post['author'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </article>

                        <hr class="divider">

                        <section class="comment-form-section">
                            <form method="post" class="comment-form">
                                <div class="form-group">
                                    <label for="name">نام</label>
                                    <input type="text" name="name" id="name" class="input-text" placeholder="نام خود را وارد کنید" required>
                                </div>

                                <div class="form-group">
                                    <label for="comment">متن کامنت</label>
                                    <textarea name="comment" id="comment" rows="5" class="textarea-text" placeholder="کامنت خود را بنویسید" required></textarea>
                                </div>

                                <button type="submit" name="post_comment" class="btn-view-post">ارسال</button>
                            </form>
                        </section>

                        <hr class="divider">

                        <section class="comments-list-section">
                            <p class="comments-count">تعداد کامنت: <?php echo $comments->rowCount(); ?></p>

                            <?php if ($comments->rowCount() > 0): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <div class="comment-card">
                                        <div class="comment-header">
                                            <img src="./img/boy.svg" alt="کاربر" class="comment-avatar" />
                                            <h5 class="comment-author"><?php echo htmlspecialchars($comment['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                        </div>
                                        <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8')); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </section>

                    <?php else: ?>
                        <div class="no-posts-alert">مقاله مورد نظر پیدا نشد!</div>
                    <?php endif; ?>
                </div>
            </main>

            <aside class="sidebar">
                <?php include("./include/sidebar.php") ?>
            </aside>
        </div>
    </div>
</section>

<?php include("./include/footer.php") ?>

<style>
* {
    font-family: tanha;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    direction: rtl;
}

body {
    background-color: #f3f3f3ff;
    color: #222;
    transition: background-color 0.3s, color 0.3s;
}

/* ====== DARK MODE ====== */
body.dark-mode {
    background-color: #121212;
    color: #e0e0e0;
}

body.dark-mode .post-section {
    background-color: #121212;
}

body.dark-mode .post-container,
body.dark-mode .sidebar,
body.dark-mode .comment-form {
    background-color: #1e1e1e;
    color: #e0e0e0;
    border: 1px solid #252525;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

body.dark-mode .post-title {
    color: #f0f0f0;
}

body.dark-mode .post-body-text p,
body.dark-mode .post-author,
body.dark-mode .comments-count,
body.dark-mode .form-group label,
body.dark-mode .comment-author,
body.dark-mode .comment-text {
    color: #bbb;
}

body.dark-mode .post-category {
    background-color: #5a6268;
}

body.dark-mode .divider {
    border-top-color: #333;
}

body.dark-mode .input-text,
body.dark-mode .textarea-text {
    background-color: #2a2a2a;
    border-color: #444;
    color: #e0e0e0;
}

body.dark-mode .input-text:focus,
body.dark-mode .textarea-text:focus {
    border-color: #4da6ff;
}

body.dark-mode .btn-view-post {
    border-color: #4da6ff;
    color: #4da6ff;
}

body.dark-mode .btn-view-post:hover {
    background-color: #4da6ff;
    color: #fff;
}

body.dark-mode .comment-card {
    background-color: #252525;
    border: 1px solid #333;
}

body.dark-mode .error-message,
body.dark-mode .no-posts-alert {
    background-color: #3d2a2e;
    color: #f8d7da;
    border-color: #721c24;
}

/* Sidebar in dark mode */
body.dark-mode aside.sidebar {
    background-color: #1e1e1e;
    color: #e0e0e0;
    border: 1px solid #333;
}

body.dark-mode .sidebar a,
body.dark-mode .sidebar h3,
body.dark-mode .sidebar ul li {
    color: #ccc;
}

body.dark-mode .sidebar a:hover {
    color: #4da6ff;
}

/* ====== LIGHT MODE (DEFAULT) ====== */
.post-section {
    padding: 1.5rem 0;
    background-color: #f3f3f3ff;
}

.container-fluid {
    max-width: 1100px;
    margin: 0 auto;
    width: 95%;
}

.row {
    display: flex;
    flex-wrap: nowrap;
    gap: 20px;
    margin-top: 80px;
}

.main-post-content {
    flex: 2 1 0;
    max-width: 66.6666%;
    min-width: 0;
}

.sidebar {
    flex: 1 1 0;
    max-width: 33.3333%;
    min-width: 0;
    background: #f1f1f1;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.post-container {
    background: #f8f8f8;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.post-image-full {
    width: 100%;
    max-height: 450px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 20px;
}

.post-header {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.post-title {
    font-size: 2rem;
    margin: 0;
    color: #222;
    flex-grow: 1;
}

.post-category {
    background-color: #6c757d;
    color: white;
    padding: 5px 12px;
    font-size: 0.9rem;
    border-radius: 12px;
    white-space: nowrap;
}

.post-body-text p {
    font-size: 1rem;
    line-height: 1.7;
    color: #444;
    text-align: justify;
    margin-bottom: 20px;
}

.post-author {
    font-size: 0.95rem;
    color: #555;
    font-weight: 600;
    margin-bottom: 30px;
}

.divider {
    border: none;
    border-top: 1px solid #ddd;
    margin: 30px 0;
}

.comment-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
    color: #333;
}

.input-text,
.textarea-text {
    width: 100%;
    padding: 10px 12px;
    border: 1.8px solid #ccc;
    border-radius: 6px;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.3s ease;
}

.input-text:focus,
.textarea-text:focus {
    border-color: #007bff;
    outline: none;
}

.btn-view-post {
    background-color: transparent;
    border: 2px solid #007bff;
    color: #007bff;
    padding: 10px 22px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    user-select: none;
    display: inline-block;
    text-align: center;
}

.btn-view-post:hover {
    background-color: #007bff;
    color: white;
}

.comments-count {
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 1.1rem;
    color: #444;
}

.comment-card {
    background: #f0f0f0;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 18px;
    box-shadow: 0 1px 6px rgba(0,0,0,0.08);
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 12px;
}

.comment-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-author {
    margin: 0;
    font-weight: 700;
    color: #333;
}

.comment-text {
    font-size: 0.95rem;
    color: #555;
    line-height: 1.5;
    white-space: pre-wrap;
}

.error-message,
.no-posts-alert {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    padding: 15px 20px;
    border-radius: 6px;
    text-align: center;
    font-weight: bold;
    font-size: 1.1rem;
    margin: 10px 0;
}

/* Responsive */
@media (max-width: 991px) {
    .row {
        flex-wrap: wrap;
        margin-top: 80px;
    }

    .main-post-content,
    .sidebar {
        flex: 0 0 100%;
        max-width: 100%;
    }

    .sidebar {
        margin-top: 20px;
    }
}
</style>