<?php
include("./include/header.php");

// گرفتن تمام محصولات به ترتیب جدیدترین‌ها
$query_products = "SELECT * FROM product ORDER BY id DESC";
$products = $db->query($query_products);

// حذف محصول در صورت درخواست
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $db->prepare('DELETE FROM product WHERE id = :id');
    $query->execute(['id' => $id]);

    header("Location: product.php");
    exit();
}
?>

<div class="container-fluid">
    <div class="row">

        <?php include('./include/sidebar.php'); ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div class="d-flex justify-content-between align-items-center mt-5 mb-3">
                <h3>محصولات</h3>
                <a href="new_product.php" class="btn btn-outline-primary">ایجاد محصول</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-sm text-center">
                    <thead class="thead-light">
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
                                // گرفتن عنوان دسته بندی مربوط به محصول
                                $cat_stmt = $db->prepare("SELECT title FROM categories WHERE id = :id");
                                $cat_stmt->execute(['id' => $product['category_id']]);
                                $category = $cat_stmt->fetch();
                        ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo $category ? htmlspecialchars($category['title']) : 'ندارد'; ?></td>
                                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['new-price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['number']); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-info btn-sm">ویرایش</a>
                                        <a href="product.php?action=delete&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-outline-danger btn-sm" 
                                           onclick="return confirm('آیا مطمئن هستید که می‌خواهید این محصول را حذف کنید؟');">
                                           حذف
                                        </a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">محصولی برای نمایش وجود ندارد!!!</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </main>

    </div>
</div>
