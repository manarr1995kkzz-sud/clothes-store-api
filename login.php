<?php
header("Content-Type: application/json");
include 'db.php'; 
include 'jwt.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Only POST allowed"]);
    exit;
}


$data = json_decode(file_get_contents("php://input"));
$username = $data->username ?? '';
$password = $data->password ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Username and password required"]);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
    exit;
}

mysqli_stmt_bind_result($stmt, $user_id, $hashed_password);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);


if (password_verify($password, $hashed_password)) {
    $token = createJWT($username); 

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "message" => "Login successful",
        "token" => $token,
        "user_id" => $user_id
    ]);
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
}
mysqli_close($conn);
?>