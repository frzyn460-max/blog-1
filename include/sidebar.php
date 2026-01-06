<?php
$query_categories = "SELECT * FROM categories";
$categories = $db->query($query_categories);
?>
<div class="sidebar-col">

    <div class="card search-card">
        <div class="card-body">
            <h5 class="card-title">جستجو در وبلاگ</h5>
            <form action="search.php" method="get" class="search-form">
                <div class="input-group">
                    <input name="search" type="text" class="search-input" placeholder="جستجو ...">
                    <button type="submit" class="search-button" aria-label="جستجو">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            viewBox="0 0 24 24">
                            <path
                                d="M10 2a8 8 0 015.292 13.708l4.999 5-1.414 1.414-5-4.999A8 8 0 1110 2zm0 2a6 6 0 100 12 6 6 0 000-12z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="list-group">
        <div class="list-group-header">دسته بندی ها</div>
        <?php
        if ($categories->rowCount() > 0) {
            foreach ($categories as $category) {
                ?>
                <a href="index.php?category=<?php echo $category['id'] ?>" class="list-group-item">
                    <?php echo htmlspecialchars($category['title']); ?>
                </a>
                <?php
            }
        }
        ?>
    </div>

    <div class="card subscribe-card">
        <div class="card-body">
            <?php
            if (isset($_POST['subscribe'])) {
                if (trim($_POST['name']) !== "" && trim($_POST['email']) !== "") {
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $subscribe_insert = $db->prepare("INSERT INTO subscribers (name, email) VALUES (:name, :email)");
                    $subscribe_insert->execute(['name' => $name, 'email' => $email]);
                    echo '<p class="success-message">اشتراک با موفقیت ثبت شد.</p>';
                } else {
                    echo '<p class="error-message">فیلد ها نباید خالی باشند</p>';
                }
            }
            ?>
            <form method="post" class="subscribe-form">
                <div class="form-group">
                    <label for="name">نام</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="نام خود را وارد کنید.">
                </div>
                <div class="form-group">
                    <label for="email">ایمیل</label>
                    <input type="email" name="email" id="email" class="form-control"
                        placeholder="ایمیل خود را وارد کنید.">
                </div>
                <button type="submit" name="subscribe" class="btn-submit">ارسال</button>
            </form>
        </div>
    </div>

    <div class="card about-card">
        <div class="card-body">
            <h3>درباره ما</h3>
            <p class="about-text">
                لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از طراحان گرافیک است.
            </p>
        </div>
    </div>

</div>

<style>
    /* کانتینر کلی ستون — بدون margin/padding اضافی */
    .sidebar-col {
        width: 100%;
        max-width: 350px;
        font-family: tanha;
        margin: 0;
        padding: 0;
    }

    /* ====== LIGHT MODE (DEFAULT) ====== */
    .card {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card-body {
        padding: 1rem;
    }

    .card-title {
        font-size: 1.2rem;
        margin-bottom: 1rem;
        color: #333;
    }

    .search-form .input-group {
        display: flex;
        direction: rtl;
    }

    .search-input {
        flex: 1;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 0 4px 4px 0;
        outline: none;
        transition: border-color 0.3s ease;
        background-color: white;
        color: #333;
    }

    .search-input:focus {
        border-color: #007bff;
    }

    .search-button {
        background: none;
        border: 1px solid #007bff;
        color: #007bff;
        padding: 0 1rem;
        cursor: pointer;
        border-radius: 4px 0 0 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .search-button:hover {
        background-color: #007bff;
        color: white;
    }

    .list-group {
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .list-group-header {
        background-color: #007bff;
        color: white;
        font-weight: bold;
        padding: 0.75rem 1rem;
        text-align: center;
    }

    .list-group-item {
        display: block;
        padding: 0.75rem 1rem;
        color: #333;
        text-decoration: none;
        border-top: 1px solid #ddd;
        transition: background-color 0.3s ease;
        direction: rtl;
    }

    .list-group-item:first-child {
        border-top: none;
    }

    .list-group-item:hover {
        background-color: #e9f2ff;
        color: #007bff;
    }

    .subscribe-form .form-group {
        margin-bottom: 1rem;
    }

    .subscribe-form label {
        display: block;
        margin-bottom: 0.25rem;
        color: #555;
    }

    .subscribe-form .form-control {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
        background-color: white;
        color: #333;
    }

    .subscribe-form .form-control:focus {
        border-color: #007bff;
    }

    .btn-submit {
        width: 100%;
        padding: 0.5rem;
        font-size: 1.1rem;
        background-color: #007bff;
        border: none;
        border-radius: 4px;
        color: white;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-submit:hover {
        background-color: #0056b3;
    }

    .success-message {
        color: green;
        margin-bottom: 1rem;
        font-weight: bold;
        text-align: center;
    }

    .error-message {
        color: red;
        margin-bottom: 1rem;
        font-weight: bold;
        text-align: center;
    }

    .about-card h3 {
        margin-top: 0;
        color: #333;
        margin-bottom: 0.75rem;
    }

    .about-text {
        text-align: justify;
        color: #555;
        line-height: 1.5;
    }

    /* ====== DARK MODE ====== */
    body.dark-mode .sidebar-col {
        color: #e0e0e0;
    }

    body.dark-mode .card {
        background-color: #1e1e1e;
        border-color: #444;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        color: #e0e0e0;
    }

    body.dark-mode .card-title,
    body.dark-mode .about-card h3 {
        color: #f0f0f0;
    }

    body.dark-mode .search-input,
    body.dark-mode .form-control {
        background-color: #2a2a2a;
        border-color: #555;
        color: #e0e0e0;
    }

    body.dark-mode .search-input:focus,
    body.dark-mode .form-control:focus {
        border-color: #4da6ff;
    }

    body.dark-mode .search-button {
        border-color: #4da6ff;
        color: #4da6ff;
    }

    body.dark-mode .search-button:hover {
        background-color: #4da6ff;
        color: white;
    }

    body.dark-mode .list-group {
        border-color: #444;
    }

    body.dark-mode .list-group-header {
        background-color: #0d6efd;
        color: white;
    }

    body.dark-mode .list-group-item {
        color: #ccc;
        border-top-color: #444;
    }

    body.dark-mode .list-group-item:hover {
        background-color: #2a2a2a;
        color: #4da6ff;
    }

    body.dark-mode .btn-submit {
        background-color: #0d6efd;
        color: white;
    }

    body.dark-mode .btn-submit:hover {
        background-color: #0b5ed7;
    }

    body.dark-mode .success-message {
        color: #66bb6a;
    }

    body.dark-mode .error-message {
        color: #ef5350;
    }

    body.dark-mode .about-text {
        color: #bbb;
    }

    /* جلوگیری از پس‌زمینه سفید در ورودی‌ها */
    body.dark-mode input,
    body.dark-mode textarea {
        background-color: #2a2a2a !important;
        color: #e0e0e0 !important;
        border-color: #555 !important;
    }
</style>