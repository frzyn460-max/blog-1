<?php
include("./include/header.php");

// حذف یا تایید بر اساس entity و action
if (isset($_GET['entity']) && isset($_GET['action']) && isset($_GET['id'])) {

    $entity = $_GET['entity'];
    $action = $_GET['action'];
    $id = $_GET['id'];

    if ($action == "delete") {

        if ($entity == "post") {
            $query = $db->prepare('DELETE FROM posts WHERE id = :id');
        } elseif ($entity == "category") {
            $query = $db->prepare('DELETE FROM categories WHERE id = :id');
        } elseif ($entity == "product") {
            $query = $db->prepare('DELETE FROM product WHERE id = :id');
        } else {
            $query = $db->prepare('DELETE FROM comments WHERE id = :id');
        }
        
        $query->execute(['id' => $id]);
    } else {
        // برای کامنت‌ها تایید (status=1)
        $query = $db->prepare("UPDATE comments SET status='1' WHERE id = :id");
        $query->execute(['id' => $id]);
    }
}

// دریافت داده‌ها
$query_posts = "SELECT * FROM posts ORDER BY id DESC LIMIT 5"; // محدود کردن به 5 مقاله اخیر
$posts = $db->query($query_posts);

$query_comments = "SELECT * FROM comments WHERE status='0' ORDER BY id DESC LIMIT 5"; // محدود به 5 کامنت اخیر
$comments = $db->query($query_comments);

$query_categories = "SELECT * FROM categories ORDER BY id DESC";
$categories = $db->query($query_categories);

$query_products = "SELECT * FROM product ORDER BY id DESC LIMIT 5"; // محدود به 5 محصول اخیر
$products = $db->query($query_products);

?>

<div class="container-fluid">
    <div class="row">

        <?php include('./include/sidebar.php') ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">داشبورد</h1>
            </div>

            <!-- مقالات -->
            <h3>مقالات اخیر</h3>
            <div class="mb-3">
                <a href="post.php" class="btn btn-primary btn-sm">مشاهده همه مقالات</a>
                <a href="new_post.php" class="btn btn-success btn-sm">افزودن مقاله جدید</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>نویسنده</th>
                            <th>تنظیمات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($posts->rowCount() > 0) {
                            foreach ($posts as $post) {
                                ?>
                                <tr>
                                    <td> <?php echo $post['id'] ?> </td>
                                    <td> <?php echo htmlspecialchars($post['title']) ?> </td>
                                    <td> <?php echo htmlspecialchars($post['author']) ?> </td>
                                    <td>
                                        <a href="edit_post.php?id=<?php echo $post['id'] ?>" class="btn btn-outline-info btn-sm">ویرایش</a>
                                        <a href="index.php?entity=post&action=delete&id=<?php echo $post['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr><td colspan="4" class="text-center">مقاله‌ای برای نمایش وجود ندارد!!!</td></tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- کامنت‌ها -->
            <h3>کامنت های اخیر</h3>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام</th>
                            <th>کامنت</th>
                            <th>تنظیمات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($comments->rowCount() > 0) {
                            foreach ($comments as $comment) {
                                ?>
                                <tr>
                                    <td> <?php echo $comment['id'] ?> </td>
                                    <td> <?php echo htmlspecialchars($comment['name']) ?> </td>
                                    <td> <?php echo htmlspecialchars($comment['comment']) ?> </td>
                                    <td>
                                        <a href="index.php?entity=comment&action=approve&id=<?php echo $comment['id'] ?>" class="btn btn-outline-success btn-sm">تایید</a>
                                        <a href="index.php?entity=comment&action=delete&id=<?php echo $comment['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr><td colspan="4" class="text-center">کامنتی برای نمایش وجود ندارد!!!</td></tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- دسته بندی ها -->
            <h3>دسته بندی ها</h3>
            <div class="table-responsive mb-4">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>تنظیمات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($categories->rowCount() > 0) {
                            foreach ($categories as $category) {
                                ?>
                                <tr>
                                    <td> <?php echo $category['id'] ?> </td>
                                    <td> <?php echo htmlspecialchars($category['title']) ?> </td>
                                    <td>
                                        <a href="edit_category.php?id=<?php echo $category['id'] ?>" class="btn btn-outline-info btn-sm">ویرایش</a>
                                        <a href="index.php?entity=category&action=delete&id=<?php echo $category['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                                    </td>
                                </tr>
                            <?php
                                }
                            } else {
                                ?>
                            <tr><td colspan="3" class="text-center">دسته‌ای برای نمایش وجود ندارد!!!</td></tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- محصولات -->
            <h3>محصولات اخیر</h3>
            <div class="mb-3">
                <a href="product.php" class="btn btn-primary btn-sm">مشاهده همه محصولات</a>
                <a href="new_product.php" class="btn btn-success btn-sm">افزودن محصول جدید</a>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>نام محصول</th>
                            <th>دسته بندی</th>
                            <th>قیمت</th>
                            <th>قیمت جدید</th>
                            <th>تعداد</th>
                            <th>تنظیمات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($products->rowCount() > 0) {
                            foreach ($products as $product) {
                                // گرفتن عنوان دسته بندی محصول
                                $cat_stmt = $db->prepare("SELECT title FROM categories WHERE id = :id");
                                $cat_stmt->execute(['id' => $product['category_id']]);
                                $category = $cat_stmt->fetch();
                                ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo $category ? htmlspecialchars($category['title']) : 'ندارد'; ?></td>
                                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['new-price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['number']); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-info btn-sm">ویرایش</a>
                                        <a href="index.php?entity=product&action=delete&id=<?php echo $product['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('آیا مطمئن هستید؟')">حذف</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="7" class="text-center">محصولی برای نمایش وجود ندارد!!!</td></tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>
</div>
