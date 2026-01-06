<?php
include("./include/header.php");

$cart_items = $_SESSION['cart'] ?? [];
?>

<style>
* {
    font-family: 'Tanha', sans-serif;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

/* کارت کلی سبد خرید */
.cart-container {
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    border-radius: 12px;
    padding: 30px 40px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
}

/* تیتر */
.cart-container h1 {
    margin-bottom: 30px;
    color: #2c3e50;
    font-weight: 700;
    font-size: 2rem;
    text-align: center;
    letter-spacing: 1px;
}

/* جدول */
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 12px;
}

table thead tr th {
    background-color: #007bff;
    color: white;
    padding: 15px 12px;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 8px 8px 0 0;
    text-align: center;
    user-select: none;
}

table tbody tr {
    background: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.05);
    transition: background-color 0.3s ease;
}

table tbody tr:hover {
    background-color: #e6f0ff;
}

table td {
    padding: 14px 12px;
    text-align: center;
    vertical-align: middle;
    font-size: 1rem;
    color: #444;
    border: none;
}

/* نام محصول */
.product-name {
    text-align: left;
    padding-left: 15px;
    font-weight: 600;
    color: #34495e;
}

/* تصویر محصول */
table td img {
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: transform 0.3s ease;
}

table td img:hover {
    transform: scale(1.05);
}

/* دکمه حذف */
.remove-btn {
    background-color: #dc3545;
    border: none;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    transition: background-color 0.25s ease, box-shadow 0.25s ease;
    user-select: none;
}

.remove-btn:hover {
    background-color: #b02a37;
    box-shadow: 0 6px 15px rgba(176, 42, 55, 0.5);
}

/* ردیف جمع کل */
.total-row td {
    font-weight: 700;
    font-size: 1.3rem;
    color: #222;
    background: #f1f3f6;
    border-radius: 0 0 12px 12px;
    padding: 20px 15px;
}

/* متن سبد خالی */
.empty-cart {
    text-align: center;
    font-size: 1.4rem;
    color: #888;
    padding: 60px 0;
    font-style: italic;
}

/* دکمه ادامه خرید */
.checkout-btn-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 15px;
}

.checkout-btn {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 16px 36px;
    font-size: 1.2rem;
    border-radius: 12px;
    cursor: pointer;
    font-weight: 700;
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    user-select: none;

    display: block;      /* اضافه شده برای اینکه دکمه بلوک شود */
    margin: 20px auto 0; /* بالا 20px فاصله، راست و چپ auto برای وسط چین */
    float: none;         /* اگر float داشت حذف کن */
}


.checkout-btn:hover {
    background-color: #218838;
    box-shadow: 0 8px 25px rgba(33, 136, 56, 0.6);
    
}

/* input تعداد */
input.quantity-input {
    width: 70px;
    padding: 8px;
    text-align: center;
    border: 2px solid #ccc;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    color: #333;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

input.quantity-input:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
}
</style>

<div class="cart-container">
    <h1 style="text-align:center; padding-bottom: 22px;">سبد خرید شما</h1>

    <?php if (empty($cart_items)) { ?>
        <p class="empty-cart">سبد خرید شما خالی است.</p>
    <?php } else { 
        $placeholders = implode(',', array_fill(0, count($cart_items), '?'));
        $stmt = $db->prepare("SELECT * FROM product WHERE id IN ($placeholders)");
        $stmt->execute(array_keys($cart_items));
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_price = 0;
    ?>
        <table>
            <thead>
                <tr>
                    <th>تصویر</th>
                    <th>نام محصول</th>
                    <th>قیمت واحد (تومان)</th>
                    <th>تعداد</th>
                    <th>قیمت کل (تومان)</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) {
                    $pid = $product['id'];
                    $quantity = $cart_items[$pid];
                    $price = $product['new-price'] ?? $product['price'];
                    $subtotal = $price * $quantity;
                    $total_price += $subtotal;
                ?>
                <tr>
                    <td><img src="./upload/products/<?php echo htmlspecialchars($product['pic']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="70" style="border-radius: 8px;"></td>
                    <td class="product-name"><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo number_format($price); ?></td>
                    <td>
                        <input type="number" min="1" class="quantity-input" value="<?php echo $quantity; ?>" onchange="updateQuantity(<?php echo $pid; ?>, this)">
                    </td>
                    <td><?php echo number_format($subtotal); ?></td>
                    <td>
                        <button class="remove-btn" onclick="removeFromCart(event, <?php echo $pid; ?>)">حذف</button>
                    </td>
                </tr>
                <?php } ?>
                <tr class="total-row">
                    <td colspan="4">مجموع کل</td>
                    <td colspan="2"><?php echo number_format($total_price); ?> تومان</td>
                </tr>
            </tbody>
        </table>

        <div class="checkout-btn-container">
            <button class="checkout-btn" onclick="alert('برای ادامه خرید به صفحه پرداخت مراجعه کنید.')">پرداخت</button>
        </div>

    <?php } ?>
</div>

<script>
    function removeFromCart(event, productId) {
        if (confirm('آیا مطمئن هستید می‌خواهید این محصول را از سبد حذف کنید؟')) {
            fetch('remove_from_cart.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const row = event.target.closest('tr');
                        row.style.transition = 'opacity 0.5s ease';
                        row.style.opacity = 0;
                        setTimeout(() => location.reload(), 500);
                    } else {
                        alert('خطا در حذف محصول از سبد خرید');
                    }
                })
                .catch(() => alert('خطا در ارتباط با سرور'));
        }
    }

    function updateQuantity(productId, input) {
        let quantity = parseInt(input.value);
        if (isNaN(quantity) || quantity < 1) {
            quantity = 1;
            input.value = quantity;
        }
        fetch('update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                input.style.transition = 'transform 0.2s ease';
                input.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    input.style.transform = 'scale(1)';
                    location.reload();
                }, 200);
            } else {
                alert('خطا در بروزرسانی تعداد');
            }
        })
        .catch(() => alert('خطا در ارتباط با سرور'));
    }
</script>
