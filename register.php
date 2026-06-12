<?php
header("Content-Type: application/json");
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}


$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';


if (empty($username)) {
    $data = json_decode(file_get_contents("php://input"));
    $username = $data->username ?? '';
    $password = $data->password ?? '';
}

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Username and password required"]);
    exit;
}


$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($check, "s", $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    http_response_code(409);
    echo json_encode(["status" => "error", "message" => "Username already exists"]);
    exit;
}
mysqli_stmt_close($check);


$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    http_response_code(201);
    echo json_encode([
        "status" => "success",
        "message" => "User registered successfully",
        "user_id" => mysqli_insert_id($conn)
    ]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Registration failed"]);
}
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>