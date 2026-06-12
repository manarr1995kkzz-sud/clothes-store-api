<?php
include 'config.php';

$id = (int)$_GET['id'];

if($id <= 0){
    die("ID غير صالح");
}

// 1. تأكدي المنتج موجود
$product_q = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
mysqli_stmt_bind_param($product_q, "i", $id);
mysqli_stmt_execute($product_q);
$result = mysqli_stmt_get_result($product_q);

if(mysqli_num_rows($result) == 0){
    die("المنتج غير موجود");
}

// 2. شيكي السلة - بدون user_id
$check_q = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE product_id = ?");
mysqli_stmt_bind_param($check_q, "i", $id);
mysqli_stmt_execute($check_q);
$check_result = mysqli_stmt_get_result($check_q);

if(mysqli_num_rows($check_result) > 0){
    $cart_row = mysqli_fetch_assoc($check_result);
    $update_q = mysqli_prepare($conn, "UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    mysqli_stmt_bind_param($update_q, "i", $cart_row['id']);
    mysqli_stmt_execute($update_q);
} else {
    $insert_q = mysqli_prepare($conn, "INSERT INTO cart (product_id, quantity) VALUES (?, 1)");
    mysqli_stmt_bind_param($insert_q, "i", $id);
    mysqli_stmt_execute($insert_q);
}

header("Location: products.php");
exit;
?>