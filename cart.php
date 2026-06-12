<?php
include 'config.php';
session_start();

// جيبي بيانات السلة مع بيانات المنتج
$query = "SELECT c.id as cart_id, c.quantity, p.id, p.name, p.price, p.image 
          FROM cart c 
          JOIN products p ON c.product_id = p.id";

$result = mysqli_query($conn, $query);
$total = 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سلة المشتريات</title>
    <link rel="stylesheet" href="style.css"> <!-- أهم سطر للتنسيق -->
</head>
<body>

    <nav>
        <div class="logo">متجر الملابس 🛍️</div>
        <div>
            <a href="products.php">المنتجات</a>
            <a href="cart.php">السلة</a>
        </div>
    </nav>

    <h1 class="title">سلة المشتريات</h1>
    
    <div class="products-container"> <!-- استخدمنا نفس الكلاس تبع المنتجات -->
    <?php while($row = mysqli_fetch_assoc($result)): 
        $subtotal = $row['price'] * $row['quantity'];
        $total += $subtotal;
    ?>
        <div class="card"> <!-- نفس كرت المنتجات -->
            <img src="img/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
            <h2><?php echo $row['name']; ?></h2>
            <p class="price"><?php echo $row['price']; ?> ل.س</p>
            <p>الكمية: <?php echo $row['quantity']; ?></p>
            <p class="price">المجموع: <?php echo $subtotal; ?> ل.س</p>
            <a href="remove_from_cart.php?id=<?php echo $row['cart_id']; ?>">
                <button class="delete-btn">حذف</button>
            </a>
        </div>
    <?php endwhile; ?>
    </div>

    <h2 class="final-total">الإجمالي الكلي: <?php echo $total; ?> ل.س</h2>

</body>
</html>