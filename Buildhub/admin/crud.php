<?php
/*add value in database*/
session_start();
require '../login/config.php';
if (isset($_POST['add'])) {
    $conn = mysqli_connect("localhost", "root", "", "user_db");

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Get form data safely
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $unit_price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Optional: you can compute line total
    $line_total = $unit_price * $quantity;

    // Handle file upload
    $file_name = $_FILES['image']['name'];
    $temp_name = $_FILES['image']['tmp_name'];
    $upload_dir = '../images/';
    $folder = $upload_dir . basename($file_name);

    if (!empty($file_name)) {
        if (move_uploaded_file($temp_name, $folder)) {

            // ✅ match your actual table column names
            $sql = "INSERT INTO order_items (product_name, description, unit_price, quantity, file, line_total)
                    VALUES ('$product_name', '$description', '$unit_price', '$quantity', '$file_name', '$line_total')";

            if (mysqli_query($conn, $sql)) {
                header('Location: ../admin/orders.php');
                exit();
            } else {
                echo "❌ Database Error: " . mysqli_error($conn);
            }
        } else {
            echo "❌ Failed to move uploaded file. Check folder permissions.";
        }
    } else {
        echo "⚠️ Please select an image file to upload.";
    }

    mysqli_close($conn);
}

/*showing value in database
    if (isset($_POST['show'])) {
$product_name = $_GET['product_name'] ?? '';
$response = ['success' => false];

if ($product_name !== '') {
    $stmt = $conn->prepare("SELECT price, quantity FROM products WHERE product_name = ?");
    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $response = [
            'success' => true,
            'price' => $row['price'],
            'quantity' => $row['quantity']
        ];
    }
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
}*/
/*updating value in database*/
if (isset($_POST['update'])) {
    $product_name = $_POST['product_name'];
    $weight = $_POST['weight'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE products SET weight='$weight', price='$price', quantity='$quantity' WHERE product_name='$product_name'";

    if ($conn->query($sql) === TRUE) {
        header('Location: ../admin/testing.php');
    } else {
        echo "Error updating record: " . $conn->error;
    }
    exit();
}

?>
