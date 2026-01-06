<?php
include("./include/header.php");

$keyword = $_GET['search'] ?? '';
$keyword = trim($keyword);

if ($keyword !== '') {
    // جستجو در جدول محصولات
    $products = $db->prepare('SELECT * FROM product WHERE name LIKE :keyword');
    $products->execute(['keyword' => "%$keyword%"]);

    // جستجو در جدول مقالات
    $posts = $db->prepare('SELECT * FROM posts WHERE title LIKE :keyword');
    $posts->execute(['keyword' => "%$keyword%"]);
}
?>

<section class="search-results-section">
    <div class="container-fluid">
        <div class="row">

            <main class="main-content">

                <div class="search-alert">
                    نتایج جستجو برای: [ <?php echo htmlspecialchars($keyword); ?> ]
                </div>

                <!-- محصولات -->
                <h2 class="section-title">محصولات</h2>
                <div class="products-grid">
                    <?php
                    if (isset($products) && $products->rowCount() > 0) {
                        foreach ($products as $product) {
                            $category_id = $product['category_id'];
                            $stmt_category = $db->prepare("SELECT * FROM categories WHERE id = :id");
                            $stmt_category->execute(['id' => $category_id]);
                            $category = $stmt_category->fetch();
                    ?>
                            <article class="product-card">
                                <img src="./upload/products/<?php echo htmlspecialchars($product['pic']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
                                <div class="product-body">
                                    <header class="product-header">
                                        <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                                        <?php if ($category) : ?>
                                            <span class="product-category"><?php echo htmlspecialchars($category['title']); ?></span>
                                        <?php endif; ?>
                                    </header>
                                    <p class="product-price">
                                        قیمت: <span class="new-price"><?php echo number_format($product['new-price']); ?> تومان</span>
                                    </p>
                                    <footer class="product-footer">
                                        <a href="single_product.php?product=<?php echo $product['id']; ?>" class="btn-view-product">مشاهده محصول</a>
                                    </footer>
                                </div>
                            </article>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="no-products-alert">محصولی با این نام یافت نشد.</div>
                    <?php
                    }
                    ?>
                </div>

                <!-- مقالات -->
                <h2 class="section-title">مقالات</h2>
                <div class="posts-grid">
                    <?php
                    if (isset($posts) && $posts->rowCount() > 0) {
                        foreach ($posts as $post) {
                            $category_id = $post['category_id'];
                            $stmt_category = $db->prepare("SELECT * FROM categories WHERE id = :id");
                            $stmt_category->execute(['id' => $category_id]);
                            $category = $stmt_category->fetch();
                    ?>
                            <article class="post-card">
                                <img src="./upload/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image" />
                                <div class="post-body">
                                    <header class="post-header">
                                        <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                                        <?php if ($category) : ?>
                                            <span class="post-category"><?php echo htmlspecialchars($category['title']); ?></span>
                                        <?php endif; ?>
                                    </header>
                                    <p class="post-excerpt">
                                        <?php echo mb_substr($post['body'], 0, 200, 'UTF-8') . '...'; ?>
                                    </p>
                                    <footer class="post-footer">
                                        <a href="single.php?post=<?php echo $post['id']; ?>" class="btn-view-post">مشاهده مقاله</a>
                                        <p class="post-author">نویسنده: <?php echo htmlspecialchars($post['author']); ?></p>
                                    </footer>
                                </div>
                            </article>
                        <?php
                        }
                    } else {
                        ?>
                        <div class="no-posts-alert">مقاله‌ای با این عنوان یافت نشد.</div>
                    <?php
                    }
                    ?>
                </div>

            </main>

            <?php include("./include/sidebar.php") ?>

        </div>
    </div>
</section>

<style>
/* فونت و راست چین */
* {
    font-family: tanha, Tahoma, Arial, sans-serif;
    direction: rtl;
    box-sizing: border-box;
}

/* ساختار کلی */
.search-results-section {
    padding: 1.5rem 0;
}

.container-fluid {
    max-width: 1200px;
    width: 95%;
    margin: 0 auto;
}

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.main-content {
    flex: 0 0 66.6666%;
    max-width: 66.6666%;
}

/* پیام اطلاع‌رسانی */
.search-alert {
    background-color: #cce5ff;
    color: #004085;
    border: 1px solid #b8daff;
    padding: 12px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-weight: bold;
    font-size: 1.1rem;
    text-align: center;
}

/* تیتر بخش‌ها */
.section-title {
    font-size: 1.8rem;
    font-weight: bold;
    color: #222;
    margin: 30px 0 15px 0;
    position: relative;
    text-align: center;
}

.section-title::before,
.section-title::after {
    content: "";
    position: absolute;
    top: 50%;
    width: 30%;
    height: 2px;
    background-color: #e2e2e2;
}

.section-title::before {
    left: 0;
}

.section-title::after {
    right: 0;
}

/* گرید محصولات */
.products-grid,
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.product-card {
    background: #fff;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    width: 100%;
    height: 100%;
    transition: box-shadow 0.3s ease;
}

.product-card:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.product-image {
    width: 100%;
    height: 180px; /* ارتفاع ثابت */
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}

.product-body {
    padding: 15px 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    flex-grow: 1;
}

.product-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.product-name {
    font-size: 1.1rem;
    color: #333;
    flex: 1;
    line-height: 1.2;
    margin: 0;
}

.product-category {
    background-color: #28a745;
    color: white;
    padding: 3px 10px;
    font-size: 0.8rem;
    border-radius: 12px;
    white-space: nowrap;
    margin-left: 10px;
}

.product-price {
    font-size: 1rem;
    color: #555;
    margin-bottom: 10px;
}

.old-price {
    text-decoration: line-through;
    color: #999;
    margin-left: 10px;
}

.new-price {
    color: #e63946;
    font-weight: bold;
}

.btn-view-product {
    background-color: transparent;
    border: 2px solid #007bff;
    color: #007bff;
    padding: 6px 18px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 5px;
    transition: all 0.3s ease;
    cursor: pointer;
    user-select: none;
    display: inline-block;
    text-align: center;
    align-self: flex-start;
}

.btn-view-product:hover {
    background-color: #007bff;
    color: white;
}

/* واکنشگرایی */
@media (max-width: 991px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

</style>
