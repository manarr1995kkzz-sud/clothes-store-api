<?php
include 'db.php';

$select = mysqli_query($conn,
"SELECT * FROM cart");

$total_all = 0;

while($row = mysqli_fetch_assoc($select)){

$total = $row['price'] * $row['quantity'];

$total_all += $total;
?>

<div>

<img src="img/<?php echo $row['image']; ?>" width="200">

<h2><?php echo $row['product_name']; ?></h2>

<p>
السعر:
<?php echo $row['price']; ?>$
</p>

<p>
العدد:
<?php echo $row['quantity']; ?>
</p>

<p>
المجموع:
<?php echo $total; ?>$
</p>

<a href="remove_from_cart.php?id=<?php echo $row['product_id']; ?>">
حذف
</a>

</div>

<hr>

<?php } ?>

<h2>
المجموع الكلي:
<?php echo $total_all; ?>$
</h2>