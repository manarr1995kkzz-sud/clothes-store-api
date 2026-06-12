<?php
session_start();
include 'config.php';

if(isset($_POST['login'])){

$username=$_POST['username'];
$password=$_POST['password'];

$select=mysqli_query($conn,
"SELECT * FROM users
WHERE username='$username'
AND password='$password'");

if(mysqli_num_rows($select)>0){

$_SESSION['user']=$username;

mysqli_query($conn,"DELETE FROM cart");

header("Location: products.php");

}else{

$error="بيانات غير صحيحة";

}
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<title>تسجيل الدخول</title>

<link rel="stylesheet" href="style.css">

</head>

<body class="login-page">

<div class="overlay">

<div class="login-container">

<h1>
🛍️ متجر الملابس
</h1>

<p class="welcome">
أهلاً بك في متجر الأزياء العصري
</p>

<?php
if(isset($error)){
echo "<p class='error'>$error</p>";
}
?>

<form method="POST">

<input type="text"
name="username"
placeholder="اسم المستخدم"
required>

<input type="password"
name="password"
placeholder="كلمة المرور"
required>

<button type="submit"
name="login">

تسجيل الدخول

</button>

</form>

<p class="register-text">

ليس لديك حساب؟

<a href="register.php">
إنشاء حساب
</a>

</p>

</div>

</div>

</body>
</html>