<?php
include("./include/header.php");

// گرفتن دسته بندی‌ها برای نمایش در فرم
$query_categories = "SELECT * FROM categories";
$categories = $db->query($query_categories);

if (isset($_POST['add_product'])) {
    if (
        trim($_POST['name']) != "" &&
        trim($_POST['category_id']) != "" &&
        trim($_POST['price']) != "" &&
        trim($_POST['new_price']) != "" &
        trim($_POST['number']) != "" &&
        trim($_FILES['image']['name']) != "" &&
        trim($_POST['description']) != ""
    ) {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $new_price = $_POST['new_price'];
        $number = $_POST['number'];
        $description = $_POST['description'];

        $name_image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];

        if (move_uploaded_file($tmp_name, "../upload/products/$name_image")) {
            // آپلود موفق
        } else {
            echo "خطا در آپلود تصویر";
            exit();
        }

        $stmt = $db->prepare("INSERT INTO product (name, category_id, price, `new-price`, number, pic, description) 
                              VALUES (:name, :category_id, :price, :new_price, :number, :pic, :description)");

        $stmt->execute([
            'name' => $name,
            'category_id' => $category_id,
            'price' => $price,
            'new_price' => $new_price,
            'number' => $number,
            'pic' => $name_image,
            'description' => $description
        ]);

        header("Location:product.php");
        exit();
    } else {
        header("Location:new_product.php?err_msg=تمام فیلدها الزامی هستند");
        exit();
    }
}
?>

<div class="container-fluid">
    <div class="row">

        <?php include('./include/sidebar.php') ?>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

            <div class="d-flex justify-content-between mt-5">
                <h3>ایجاد محصول</h3>
            </div>

            <hr>
            <?php if (isset($_GET['err_msg'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['err_msg']); ?>
                </div>
            <?php } ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">نام محصول :</label>
                    <input type="text" class="form-control" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label for="category_id">دسته بندی :</label>
                    <select class="form-control" name="category_id" id="category_id" required>
                        <?php
                        if ($categories->rowCount() > 0) {
                            foreach ($categories as $category) {
                                echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['title']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">قیمت :</label>
                    <input type="number" class="form-control" name="price" id="price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="new_price">قیمت جدید :</label>
                    <input type="number" class="form-control" name="new_price" id="new_price" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="number">تعداد :</label>
                    <input type="number" class="form-control" name="number" id="number" min="0" required>
                </div>

                <div class="form-group">
                    <label for="image">تصویر :</label>
                    <input type="file" class="form-control" name="image" id="image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label for="description">توضیحات :</label>
                    <textarea class="form-control" name="description" id="description" rows="5" required></textarea>
                </div>

                <button type="submit" name="add_product" class="btn btn-outline-primary">ایجاد</button>
            </form>

        </main>

    </div>
</div>
