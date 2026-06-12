<?php
include 'config.php';
session_start();

$cart_count = 0;
$result_count = mysqli_query($conn, "SELECT SUM(quantity) as total FROM cart");
if($row_count = mysqli_fetch_assoc($result_count)){
    $cart_count = $row_count['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>متجر الملابس</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav>
        <div class="logo">متجر الملابس 🛍️</div>
        <div>
            <a href="products.php">المنتجات</a>
            <a href="cart.php">السلة (<?php echo $cart_count; ?>)</a>
            <a href="login.php">تسجيل الدخول</a>
        </div>
    </nav>

    <h1 class="title">أحدث المنتجات</h1>
    
    <div class="products-container">
    <?php
    $result = mysqli_query($conn, "SELECT * FROM products");
    while($row = mysqli_fetch_assoc($result)):
    ?>
        <div class="card">
            <img src="img/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h2><?php echo $row['name']; ?></h2>
            <p class="price">$<?php echo $row['price']; ?></p>
            <a href="add_to_cart.php?id=<?php echo $row['id']; ?>">
                <button>إضافة للسلة</button>
            </a>
        </div>
    <?php endwhile; ?>
    </div>

</body>
</html>