<?php
session_start();
require '../login/config.php';

$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8mb4");

// Require login
$sellerId   = (int)($_SESSION['user_id'] ?? 0);
$sellerRole = $_SESSION['role'] ?? '';
if ($sellerId <= 0 || $sellerRole !== 'seller') {
    http_response_code(403);
    die("Not authorized");
}

/* -------------------- ADD -------------------- */
if (isset($_POST['add'])) {
    $product_name = trim($_POST['product_name'] ?? '');
    $unit_price   = (int)($_POST['price'] ?? 0);
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $description  = trim($_POST['description'] ?? '');
    $line_total   = $unit_price * $quantity;

    // upload
    $file_name  = $_FILES['image']['name'] ?? '';
    $temp_name  = $_FILES['image']['tmp_name'] ?? '';
    $upload_dir = '../images/';
    $target     = $upload_dir . basename($file_name);

    if (!$file_name) { die("Please select an image file to upload."); }
    if (!move_uploaded_file($temp_name, $target)) { die("Failed to move uploaded file."); }

    $sql = "INSERT INTO order_items
            (user_id, product_id, product_name, description, unit_price, quantity, file, line_total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $productId  = 0;
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) die("Prepare failed: " . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt, "iissiisi",
        $sellerId, $productId, $product_name, $description, $unit_price, $quantity, $file_name, $line_total
    );

    if (!mysqli_stmt_execute($stmt)) die("Execute failed: " . mysqli_error($conn));
    mysqli_stmt_close($stmt);

    header('Location: ../admin/orders.php'); exit();
}

/* -------------------- UPDATE (optional) -------------------- */
if (isset($_POST['update'])) {
    $id          = (int)($_POST['id'] ?? 0);
    $product_name= trim($_POST['product_name'] ?? '');
    $unit_price  = (int)($_POST['price'] ?? 0);
    $quantity    = (int)($_POST['quantity'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $line_total  = $unit_price * $quantity;

    // keep old file unless new uploaded
    $fileSql = "";
    $fileParams = [];
    if (!empty($_FILES['image']['name'])) {
        $file_name  = $_FILES['image']['name'];
        $temp_name  = $_FILES['image']['tmp_name'];
        $upload_dir = '../images/';
        $target     = $upload_dir . basename($file_name);
        if (!move_uploaded_file($temp_name, $target)) { die("Failed to move uploaded file."); }
        $fileSql = ", file = ?";
        $fileParams[] = $file_name;
    }

    $sql = "UPDATE order_items
            SET product_name=?, description=?, unit_price=?, quantity=?, line_total=?, updated_at=NOW() $fileSql
            WHERE id=? AND user_id=?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) die("Prepare failed: " . mysqli_error($conn));

    if ($fileSql) {
        mysqli_stmt_bind_param($stmt, "ssiiisii",
            $product_name, $description, $unit_price, $quantity, $line_total, $fileParams[0], $id, $sellerId
        );
    } else {
        mysqli_stmt_bind_param($stmt, "ssiii ii",
            $product_name, $description, $unit_price, $quantity, $line_total, $id, $sellerId
        );
    }

    if (!mysqli_stmt_execute($stmt)) die("Execute failed: " . mysqli_error($conn));
    mysqli_stmt_close($stmt);

    header('Location: ../admin/orders.php'); exit();
}

/* -------------------- DELETE -------------------- */
if (isset($_POST['delete'])) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) { die("No valid ID to delete."); }

    // Only allow deleting your own rows
    $stmt = mysqli_prepare($conn, "DELETE FROM order_items WHERE id = ? AND user_id = ?");
    if (!$stmt) die("Prepare failed: " . mysqli_error($conn));

    mysqli_stmt_bind_param($stmt, "ii", $id, $sellerId);
    if (!mysqli_stmt_execute($stmt)) die("Execute failed: " . mysqli_error($conn));
    mysqli_stmt_close($stmt);

    header('Location: ../admin/orders.php'); exit();
}

// Fallback
header('Location: ../admin/orders.php'); exit();
