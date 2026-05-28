<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user'])){
header("Location: login.php");
}

$count_query = mysqli_query($conn,
"SELECT SUM(quantity) AS total FROM cart");

$count_fetch = mysqli_fetch_assoc($count_query);

$count = $count_fetch['total'];
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

<h1 class="logo">
🛍️ متجر الملابس
</h1>

<div>

<a href="products.php">
المنتجات
</a>

<a href="cart.php">
السلة
(<?php echo $count ? $count : 0; ?>)
</a>

<a href="logout.php">
تسجيل الخروج
</a>

</div>

</nav>

<h2 class="title">
أحدث المنتجات
</h2>

<div class="products-container">

<?php

$products = mysqli_query($conn,
"SELECT * FROM products");

while($row=mysqli_fetch_assoc($products)){

?>

<div class="card">

<img src="img/<?php echo $row['image']; ?>">

<h2>
<?php echo $row['name']; ?>
</h2>

<p class="price">
$<?php echo $row['price']; ?>
</p>

<a href="add_to_cart.php?id=<?php echo $row['id']; ?>">

<button>

إضافة للسلة

</button>

</a>

</div>

<?php } ?>

</div>

</body>
</html>