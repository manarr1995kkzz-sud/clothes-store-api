<?php
header("Content-Type: application/json");
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// === GET - عرض المنتجات === 
if ($method === 'GET') {
    
   
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"]);
        }
    
    } else {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        $types = "";
        
        if(isset($_GET['name']) && $_GET['name'] !== '') {
            $sql .= " AND name LIKE ?";
            $params[] = "%" . $_GET['name'] . "%";
            $types .= "s";
        }
        
        if(isset($_GET['price']) && $_GET['price'] !== '') {
            $sql .= " AND price = ?";
            $params[] = floatval($_GET['price']);
            $types .= "d";
        }
        
        if(isset($_GET['min_price']) && $_GET['min_price'] !== '') {
            $sql .= " AND price >= ?";
            $params[] = floatval($_GET['min_price']);
            $types .= "d";
        }
        if(isset($_GET['max_price']) && $_GET['max_price'] !== '') {
            $sql .= " AND price <= ?";
            $params[] = floatval($_GET['max_price']);
            $types .= "d";
        }
        
        $stmt = $conn->prepare($sql);
        
        if(!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = [];
        while($row = $result->fetch_assoc()){
            $products[] = $row;
        }
        echo json_encode($products);
    }


} else if ($method === 'POST') {
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->name) || !isset($data->price)) {
        http_response_code(400);
        echo json_encode(["error" => "Name and price are required"]);
        exit;
    }
    
    $name = $data->name;
    $price = floatval($data->price);
    $image = $data->image ?? null;
    $image_url = $data->image_url ?? null;
    
    $stmt = $conn->prepare("INSERT INTO products (name, price, image, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $image, $image_url);
    
    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            "message" => "Product added successfully",
            "id" => $conn->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to add product: " . $conn->error]);
    }


} else if ($method === 'PUT') {
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Product ID is required"]);
        exit;
    }
    
    $id = intval($_GET['id']);
    $data = json_decode(file_get_contents("php://input"));
    
   
    if (!isset($data->name) || !isset($data->price)) {
        http_response_code(400);
        echo json_encode(["error" => "Name and price are required for PUT"]);
        exit;
    }
    
    $name = $data->name;
    $price = floatval($data->price);
    $image = $data->image ?? null;
    $image_url = $data->image_url ?? null;
    
    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, image = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("sdssi", $name, $price, $image, $image_url, $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Product updated successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Product not found or no changes made"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update product"]);
    }


} else if ($method === 'PATCH') {
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Product ID is required"]);
        exit;
    }
    
    $id = intval($_GET['id']);
    $data = json_decode(file_get_contents("php://input"));
    
    $fields = [];
    $params = [];
    $types = "";
    
   
    if (isset($data->name)) {
        $fields[] = "name = ?";
        $params[] = $data->name;
        $types .= "s";
    }
    if (isset($data->price)) {
        $fields[] = "price = ?";
        $params[] = floatval($data->price);
        $types .= "d";
    }
    if (isset($data->image)) {
        $fields[] = "image = ?";
        $params[] = $data->image;
        $types .= "s";
    }
    if (isset($data->image_url)) {
        $fields[] = "image_url = ?";
        $params[] = $data->image_url;
        $types .= "s";
    }
    
    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(["error" => "No fields to update"]);
        exit;
    }
    
    $sql = "UPDATE products SET " . implode(", ", $fields) . " WHERE id = ?";
    $params[] = $id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(["message" => "Product updated successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Product not found or no changes made"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update product"]);
    }


} else if ($method === 'DELETE') {
    
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Product ID is required"]);
        exit;
    }
    
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200); 
            echo json_encode(["message" => "Product deleted successfully"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Product not found"]);
        }
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete product"]);
    }


} else {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}

mysqli_close($conn);
?>