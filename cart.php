<?php
header("Content-Type: application/json");
include 'db.php';
include 'jwt.php';


$headers = getallheaders();
$authHeader = $headers['Authorization']?? '';
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Token required"]);
    exit;
}
$token = $matches[1];


$payload = validateJWT($token);
if (!$payload) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Invalid or expired token"]);
    exit;
}
$username = $payload->user;


$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username =?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
$user_id = $user['id']?? null;
mysqli_stmt_close($stmt);

if (!$user_id) {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}


$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'POST': 
        $data = json_decode(file_get_contents("php://input"));
        $product_id = $data->product_id?? 0;
        $quantity = $data->quantity?? 1;

        if ($product_id <= 0 || $quantity <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Valid product_id and quantity required"]);
            exit;
        }

       
        $check_product = mysqli_prepare($conn, "SELECT id FROM products WHERE id =?");
        mysqli_stmt_bind_param($check_product, "i", $product_id);
        mysqli_stmt_execute($check_product);
        mysqli_stmt_store_result($check_product);
        if (mysqli_stmt_num_rows($check_product) == 0) {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Product not found"]);
            exit;
        }
        mysqli_stmt_close($check_product);

       
        $check_cart = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id =? AND product_id =?");
        mysqli_stmt_bind_param($check_cart, "ii", $user_id, $product_id);
        mysqli_stmt_execute($check_cart);
        $result = mysqli_stmt_get_result($check_cart);

        if ($row = mysqli_fetch_assoc($result)) {
          
            $new_quantity = $row['quantity'] + $quantity;
            $update = mysqli_prepare($conn, "UPDATE cart SET quantity =? WHERE id =?");
            mysqli_stmt_bind_param($update, "ii", $new_quantity, $row['id']);
            mysqli_stmt_execute($update);
            mysqli_stmt_close($update);

            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Quantity updated in cart", "new_quantity" => $new_quantity]);
        } else {
          
            $stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)");
            mysqli_stmt_bind_param($stmt, "iii", $user_id, $product_id, $quantity);

            if (mysqli_stmt_execute($stmt)) {
                http_response_code(201);
                echo json_encode(["status" => "success", "message" => "Product added to cart", "cart_id" => mysqli_insert_id($conn)]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to add product"]);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_cart);
        break;

    case 'GET': 
        $stmt = mysqli_prepare($conn, "
            SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, p.image_url, c.quantity, (p.price * c.quantity) as total_price
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id =?
        ");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $cart= mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        $grand_total = 0;
        foreach ($cart as $item) {
            $grand_total += $item['total_price'];
        }

        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "cart" => $cart,
            "grand_total" => $grand_total,
            "items_count" => count($cart)
        ]);
        break;

    case 'PUT': 
        $data = json_decode(file_get_contents("php://input"));
        $product_id = $data->product_id?? 0;
        $quantity = $data->quantity?? 0;

        if ($product_id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "product_id required"]);
            exit;
        }

        if ($quantity <= 0) {
           
            $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id =? AND product_id =?");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Product removed from cart"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Product not found in cart"]);
            }
        } else {
          
            $stmt = mysqli_prepare($conn, "UPDATE cart SET quantity =? WHERE user_id =? AND product_id =?");
            mysqli_stmt_bind_param($stmt, "iii", $quantity, $user_id, $product_id);

            if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
                http_response_code(200);
                echo json_encode(["status" => "success", "message" => "Quantity updated"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Product not found in cart"]);
            }
        }
        mysqli_stmt_close($stmt);
        break;

    case 'DELETE': 
        $data = json_decode(file_get_contents("php://input"));
        $product_id = $data->product_id?? 0;

        if ($product_id <= 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "product_id required"]);
            exit;
        }

        $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id =? AND product_id =?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

        if (mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0) {
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Product removed from cart"]);
        } else {
            http_response_code(404);
            echo json_encode(["status" => "error", "message" => "Product not found in cart"]);
        }
        mysqli_stmt_close($stmt);
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        break;
}

mysqli_close($conn);
?>