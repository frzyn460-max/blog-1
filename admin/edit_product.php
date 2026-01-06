<?php 
include("./include/header.php");

// گرفتن دسته بندی‌ها برای نمایش در فرم
$query_categories = "SELECT * FROM categories";
$categories = $db->query($query_categories);

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $product_stmt = $db->prepare('SELECT * FROM product WHERE id = :id');
    $product_stmt->execute(['id' => $product_id]);
    $product = $product_stmt->fetch();

    if (!$product) {
        // اگر محصول پیدا نشد به صفحه محصولات برگرد
        header("Location: product.php");
        exit();
    }
}

if (isset($_POST['edit_product'])) {

    if (
        trim($_POST['name']) != "" &&
        trim($_POST['category_id']) != "" &&
        trim($_POST['price']) != "" &&
        trim($_POST['new_price']) != "" &&
        trim($_POST['number']) != "" &&
        trim($_POST['description']) != ""
    ) {

        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $new_price = $_POST['new_price'];
        $number = $_POST['number'];
        $description = $_POST['description'];

        // اگر تصویر جدید آپلود شده بود
        if (isset($_FILES['image']) && trim($_FILES['image']['name']) != "") {

            $name_image = $_FILES['image']['name'];
            $tmp_name = $_FILES['image']['tmp_name'];

            if (move_uploaded_file($tmp_name, "../upload/products/$name_image")) {
                // آپلود موفقیت آمیز
                $update_stmt = $db->prepare("UPDATE product SET 
                    name = :name, 
                    category_id = :category_id, 
                    price = :price, 
                    `new-price` = :new_price, 
                    number = :number, 
                    pic = :pic, 
                    description = :description 
                    WHERE id = :id");
                $update_stmt->execute([
                    'name' => $name,
                    'category_id' => $category_id,
                    'price' => $price,
                    'new_price' => $new_price,
                    'number' => $number,
                    'pic' => $name_image,
                    'description' => $description,
                    'id' => $product_id
                ]);

            } else {
                // خطا در آپلود تصویر
                header("Location: edit_product.php?id=$product_id&err_msg=خطا در آپلود تصویر");
                exit();
            }

        } else {
            // اگر تصویر آپلود نشده، فقط بقیه فیلدها رو آپدیت کن
            $update_stmt = $db->prepare("UPDATE product SET 
                name = :name, 
                category_id = :category_id, 
                price = :price, 
                `new-price` = :new_price, 
                number = :number, 
                description = :description 
                WHERE id = :id");
            $update_stmt->execute([
                'name' => $name,
                'category_id' => $category_id,
                'price' => $price,
                'new_price' => $new_price,
                'number' => $number,
                'description' => $description,
                'id' => $product_id
            ]);
        }

        header("Location: product.php");
        exit();

    } else {
        header("Location: edit_product.php?id=$product_id&err_msg=تمام فیلدها الزامی هستند");
        exit();
    }
}
?>

<div class="container-fluid">
    <div class="row">

        <?php include('./include/sidebar.php') ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div class="d-flex justify-content-between mt-5">
                <h3>ویرایش محصول</h3>
            </div>

            <hr>
            <?php if (isset($_GET['err_msg'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['err_msg']); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="mb-5" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">نام محصول :</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id">دسته بندی :</label>
                    <select class="form-control" name="category_id" id="category_id" required>
                        <?php
                        if ($categories->rowCount() > 0) {
                            foreach ($categories as $category) {
                                $selected = ($category['id'] == $product['category_id']) ? "selected" : "";
                                echo "<option value='" . $category['id'] . "' $selected>" . htmlspecialchars($category['title']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">قیمت :</label>
                    <input type="number" step="0.01" class="form-control" name="price" id="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="new_price">قیمت جدید :</label>
                    <input type="number" step="0.01" class="form-control" name="new_price" id="new_price" value="<?php echo htmlspecialchars($product['new-price']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="number">تعداد :</label>
                    <input type="number" class="form-control" name="number" id="number" min="0" value="<?php echo htmlspecialchars($product['number']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">توضیحات :</label>
                    <textarea class="form-control" name="description" id="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>تصویر فعلی :</label><br>
                    <img class="img-fluid mb-3" src="../upload/products/<?php echo htmlspecialchars($product['pic']); ?>" alt="تصویر محصول" style="max-width:200px;">
                </div>

                <div class="form-group">
                    <label for="image">تصویر جدید (اختیاری):</label>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*">
                    <small class="form-text text-muted">در صورت تمایل تصویر جدید انتخاب کنید.</small>
                </div>

                <button type="submit" name="edit_product" class="btn btn-outline-primary">ویرایش</button>
            </form>

        </main>

    </div>
</div>
