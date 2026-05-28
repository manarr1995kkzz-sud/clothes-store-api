<?php
include 'config.php';

$id = $_GET['id'];

$product = mysqli_query($conn,
"SELECT * FROM products WHERE id='$id'");

$row = mysqli_fetch_assoc($product);

$check = mysqli_query($conn,
"SELECT * FROM cart WHERE product_id='$id'");

if(mysqli_num_rows($check) > 0){

    $cart_row = mysqli_fetch_assoc($check);

    $new_quantity = $cart_row['quantity'] + 1;

    mysqli_query($conn,
    "UPDATE cart SET quantity='$new_quantity'
    WHERE product_id='$id'");

}else{

    $name = $row['name'];
    $price = $row['price'];
    $image = $row['image'];

    mysqli_query($conn,
    "INSERT INTO cart
    (product_id,product_name,price,image,quantity)

    VALUES

    ('$id','$name','$price','$image',1)");
}

header("Location: products.php");
?>