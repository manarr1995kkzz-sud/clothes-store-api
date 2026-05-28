<?php
include 'config.php';

$count_query = mysqli_query($conn,
"SELECT SUM(quantity) AS total FROM cart");

$count_fetch = mysqli_fetch_assoc($count_query);

$count = $count_fetch['total'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<title>السلة</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<nav>

<h1 class="logo">
🛒 سلة المشتريات
</h1>

<div>

<a href="products.php">
المنتجات
</a>

<a href="cart.php">
السلة
(<?php echo $count ? $count : 0; ?>)
</a>

</div>

</nav>

<div class="products-container">

<?php

$select = mysqli_query($conn,
"SELECT * FROM cart");

$total_all = 0;

while($row=mysqli_fetch_assoc($select)){

$total = $row['price'] * $row['quantity'];

$total_all += $total;

?>

<div class="card">

<img src="img/<?php echo $row['image']; ?>">

<h2>
<?php echo $row['product_name']; ?>
</h2>

<p>
السعر:
$<?php echo $row['price']; ?>
</p>

<p>
العدد:
<?php echo $row['quantity']; ?>
</p>

<p class="price">
المجموع:
$<?php echo $total; ?>
</p>

<a href="remove_from_cart.php?id=<?php echo $row['product_id']; ?>">

<button class="delete-btn">

حذف

</button>

</a>

</div>

<?php } ?>

</div>

<h2 class="final-total">

المجموع النهائي:
$<?php echo $total_all; ?>

</h2>

</body>
</html>