<?php

include 'db.php';
include 'jwt.php';

$headers=getallheaders();

if(!isset($headers['Authorization'])){

http_response_code(401);

echo json_encode([
"message"=>"Unauthorized"
]);

exit();

}

$data=json_decode(file_get_contents("php://input"));

$name=$data->name;
$price=$data->price;
$image=$data->image;

mysqli_query($conn,

"INSERT INTO products(name,price,image)

VALUES

('$name','$price','$image')");

echo json_encode([

"message"=>"added"

]);

?>