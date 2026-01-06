<?php
include("./include/header.php");

if (isset($_GET['category'])) {
  $category_id = $_GET['category'];
  $posts = $db->prepare(query: 'SELECT * FROM posts WHERE category_id = :id LIMIT 4');
  $posts->execute(params: ['id' => $category_id]);
  $products = $db->prepare('SELECT * FROM product WHERE category_id = :id LIMIT 3');
  $products->execute(['id' => $category_id]);
} else {
  $posts = $db->query("SELECT * FROM posts LIMIT 4");
  $products = $db->query("SELECT * FROM product LIMIT 6");
}

?>
<section class="hero">
  <div class="container">
    <div class="hero-container">
      <div class="hero-content">
        <h1 class="hero-title">
          با هم، برای<br>
          <span> کتاب نت , منبع انواع کتاب ها</span>
        </h1>
        <p>
           کتاب نت, مجموعه‌ای متنوع از کتاب های مختلف در اختیار شماست.
          با جستجو در سایت،
          <br> به انواع کتاب ها ها دسترسی پیدا کنید.
        </p>
      </div>

      <div class="hero-book">
        <img src="./img/25.jpg" alt="کتاب" class="hero-book-image">
      </div>
    </div>
  </div>
</section>

<div class="page-wrapper">
  <section class="main-content">
    <section class="products-section">
      <div class="container-fluid">
        <h1 class="section-title">محصولات</h1>
        <div class="products-grid">
          <?php
          if ($products->rowCount() > 0) {
            foreach ($products as $product) {
              $cat_id = $product['category_id'];
              $product_category = $db->query("SELECT * FROM categories WHERE id=$cat_id")->fetch();
              ?>
              <article class="product-card">
                <img src="./upload/products/<?php echo htmlspecialchars($product['pic']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
                <div class="product-body">
                  <header class="product-header">
                    <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <span class="product-category"><?php echo htmlspecialchars($product_category['title']); ?></span>
                  </header>
                  <p class="product-price">
                    قیمت:
                    <span class="old-price"><?php echo number_format($product['price']); ?> تومان</span>
                    <span class="new-price"><?php echo number_format($product['new-price']); ?> تومان</span>
                  </p>
                  <a href="single_product.php?product=<?php echo $product['id']; ?>" class="btn-view-post">خرید محصول</a>
                </div>
              </article>
            <?php
            }
          } else {
            echo "<div class='no-products-alert'>محصولی یافت نشد!</div>";
          }
          ?>
        </div>
      </div>
    </section>

    <section class="posts-section">
      <div class="container-fluid">
        <h1 class="section-title">مقالات</h1>
        <div class="posts-grid">
          <?php
          if ($posts->rowCount() > 0) {
            foreach ($posts as $post) {
              $category_id = $post['category_id'];
              $post_category = $db->query("SELECT * FROM categories WHERE id=$category_id")->fetch();
              ?>
              <article class="post-card">
                <img src="./upload/posts/<?php echo htmlspecialchars($post['image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="post-image" />
                <div class="post-body">
                  <header class="post-header">
                    <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                    <span class="post-category"><?php echo htmlspecialchars($post_category['title']); ?></span>
                  </header>
                  <p class="post-excerpt">
                    <?php echo mb_substr($post['body'], 0, 500) . "..."; ?>
                  </p>
                  <footer class="post-footer">
                    <a href="single.php?post=<?php echo $post['id']; ?>" class="btn-view-post">مشاهده</a>
                    <p class="post-author">نویسنده : <?php echo htmlspecialchars($post['author']); ?></p>
                  </footer>
                </div>
              </article>
            <?php
            }
          } else {
            ?>
            <div class='no-posts-alert'>مقاله مورد نظر پیدا نشد!</div>
          <?php } ?>
        </div>
      </div>
    </section>
  </section>

  <div class="sidebar">
    <?php include("./include/sidebar.php") ?>
  </div>
</div>

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

body.dark-mode .hero {
  background-color: #1e1e1e;
  box-shadow: 5px 5px 10px #0040b0;
}

body.dark-mode .section-title {
  color: #f0f0f0;
}

body.dark-mode .section-title::before,
body.dark-mode .section-title::after {
  background-color: #444;
}

body.dark-mode .product-card,
body.dark-mode .post-card {
  background-color: #1e1e1e;
  color: #e0e0e0;
  border: 1px solid #252525ff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
}

body.dark-mode .product-card:hover,
body.dark-mode .post-card:hover {
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.6);
}

body.dark-mode .product-name,
body.dark-mode .post-title {
  color: #f0f0f0;
}

body.dark-mode .product-price,
body.dark-mode .post-excerpt,
body.dark-mode .post-author {
  color: #bbb;
}

body.dark-mode .old-price {
  color: #888;
}

body.dark-mode .new-price {
  color: #ff6b6b;
}

body.dark-mode .btn-view-post {
  border-color: #4da6ff;
  color: #4da6ff;
}

body.dark-mode .btn-view-post:hover {
  background-color: #4da6ff;
  color: #fff;
}

body.dark-mode .product-category {
  background-color: #2e7d32;
}

body.dark-mode .post-category {
  background-color: #5a6268;
}

body.dark-mode .no-posts-alert,
body.dark-mode .no-products-alert {
  background-color: #3d2a2e;
  color: #f8d7da;
  border-color: #721c24;
}

/* Sidebar in dark mode */
body.dark-mode .sidebar {
  background-color: transparent; /* مهم: شفاف کردن والد */
}

body.dark-mode aside.sidebar {
  background-color: #1e1e1e;
  color: #e0e0e0;
  border: 1px solid #333;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
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
.hero {
  color: #fff;
  box-shadow: 5px 5px 10px #0059ff;
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  margin-top: 80px;
  padding-bottom: 50px;
  border-radius: 32px;
  background-color: #362f2f;
}

.hero-container {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
}

.hero-content {
  flex: 1 1 50%;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 15px;
  padding: 20px;
}

.hero-title {
  font-size: 36px;
  line-height: 1.4;
  margin-bottom: 10px;
  font-weight: bold;
}

.hero-title span {
  color: rgb(0, 140, 255);
}

.hero-book {
  flex: 1 1 40%;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}

.hero-book-image {
  max-width: 280px;
  width: 100%;
  height: auto;
  border-radius: 16px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
}

.page-wrapper {
  display: flex;
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px 10px;
  gap: 20px;
  margin-top: 80px;
  align-items: flex-start; /* مهم: جلوگیری از کشیده شدن سایدبار به پایین */
}

.sidebar {
  /* بدون padding یا margin اضافی */
  align-self: flex-start; /* مهم: سایدبار از بالا شروع شود و به پایین کشیده نشود */
}

aside.sidebar {
  flex: 0 0 280px;
  background-color: #f1f1f1;
  border-radius: 6px;
  padding: 20px;
  height: fit-content;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.section-title {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  color: #222;
  margin-bottom: 1rem;
  padding-right: 10px;
  padding-top: 50px;
  padding-bottom: 15px;
  text-align: center;
}

.section-title::before,
.section-title::after {
  content: "";
  flex: 1;
  height: 2px;
  background-color: #e2e2e2ff;
  margin: 0 12px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.product-card {
  background: #f8f8f8;
  border-radius: 8px;
  border: 1px solid #ecececff;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 100%;
  transition: box-shadow 0.3s ease;
}

.product-card:hover {
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.product-image {
  width: 100%;
  height: 70%;
  object-fit: cover;
  border-bottom: 1px solid #ddd;
}

.product-body {
  padding: 15px 20px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  height: 100%;
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

.btn-view-post {
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
  align-self: center;
}

.btn-view-post:hover {
  background-color: #007bff;
  color: white;
}

.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.post-card {
  background: #f8f8f8;
  border-radius: 8px;
  border: 1px solid #ecececff;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  height: 100%;
  transition: box-shadow 0.3s ease;
  position: relative;
}

.post-card:hover {
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.post-image {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-bottom: 1px solid #ddd;
}

.post-body {
  padding: 15px 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.post-title {
  font-size: 1.2rem;
  margin: 0;
  color: #222;
  flex: 1;
  line-height: 1.3;
}

.post-category {
  background-color: #6c757d;
  color: white;
  padding: 3px 10px;
  font-size: 0.8rem;
  border-radius: 12px;
  white-space: nowrap;
  margin-left: 10px;
}

.post-excerpt {
  flex-grow: 1;
  font-size: 0.95rem;
  color: #555;
  text-align: justify;
  margin-bottom: 15px;
}

.post-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.post-author {
  font-size: 0.9rem;
  color: #666;
  white-space: nowrap;
}

.no-posts-alert,
.no-products-alert {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  padding: 15px 20px;
  border-radius: 6px;
  width: 100%;
  text-align: center;
  font-weight: bold;
  font-size: 1.1rem;
}

@media (max-width: 991px) {
  .page-wrapper {
    flex-direction: column;
    padding: 10px 5px;
    margin-top: 80px;
  }

  .sidebar {
    align-self: auto;
  }

  aside.sidebar {
    flex: 1 0 auto;
    margin-bottom: 20px;
  }

  .main-content {
    width: 100%;
  }

  .products-grid,
  .posts-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }

  .hero-container {
    flex-direction: column;
    text-align: center;
  }

  .hero-book {
    margin-top: 20px;
  }
}
</style>