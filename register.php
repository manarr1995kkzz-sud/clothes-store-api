<?php
include 'config.php';

if(isset($_POST['register'])){

$username=$_POST['username'];
$password=$_POST['password'];

$check=mysqli_query($conn,
"SELECT * FROM users
WHERE username='$username'");

if(mysqli_num_rows($check)>0){

$error="اسم المستخدم موجود مسبقاً";

}else{

mysqli_query($conn,
"INSERT INTO users(username,password)
VALUES('$username','$password')");

header("Location: login.php");

}
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

<meta charset="UTF-8">
<title>إنشاء حساب</title>

<link rel="stylesheet" href="style.css">

</head>

<body class="register-page">

<div class="overlay">

<div class="register-container">

<h1>
✨ إنشاء حساب جديد
</h1>

<p class="welcome">
انضم إلى متجر الأزياء العصري
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
name="register">

إنشاء حساب

</button>

</form>

<p class="register-text">

لديك حساب بالفعل؟

<a href="login.php">
تسجيل الدخول
</a>

</p>

</div>

</div>

</body>
</html>