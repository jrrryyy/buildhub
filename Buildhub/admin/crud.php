<?php
session_start();
require '../login/config.php'; // should expose $conn (mysqli)

// Fallback if config didn't set $conn
if (!isset($conn) || !$conn instanceof mysqli) {
    $conn = mysqli_connect("localhost", "root", "", "user_db");
    if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
    mysqli_set_charset($conn, "utf8mb4");
}

// helpers
function has_text($v) { return (bool)preg_match('/\S/', $v ?? ''); }
function redirect_back() {
    $to = $_SERVER['HTTP_REFERER'] ?? '../seller/listings.php';
    header("Location: $to"); exit();
}
function back_with_error($msg) {
    $_SESSION['crud_error'] = $msg;
    redirect_back();
}
function back_with_success($msg) {
    $_SESSION['crud_success'] = $msg;
    redirect_back();
}

// Require login + seller role
$sellerId   = (int)($_SESSION['user_id'] ?? 0);
$sellerRole = $_SESSION['role'] ?? '';
if ($sellerId <= 0 || $sellerRole !== 'seller') {
    http_response_code(403);
    die("Not authorized");
}

/* -------------------- ADD -------------------- */
if (isset($_POST['add'])) {
    // raw inputs
    $name_raw = $_POST['product_name'] ?? '';
    $desc_raw = $_POST['description']  ?? '';
    $price_raw= $_POST['price']        ?? '';
    $qty_raw  = $_POST['quantity']     ?? '';

    // product_name
    if ($name_raw === '' || trim($name_raw) === '') { $_SESSION['open_add_modal']=1; back_with_error('Product name is required.'); }
    $product_name = trim($name_raw);

    // description (optional)
    $description = (trim($desc_raw) === '') ? '' : trim($desc_raw);

    // price
    if ($price_raw === '' || trim($price_raw) === '' || !is_numeric($price_raw)) { $_SESSION['open_add_modal']=1; back_with_error('Price must be a number.'); }
    $unit_price = (float)$price_raw;
    if ($unit_price < 0) { $_SESSION['open_add_modal']=1; back_with_error('Price cannot be negative.'); }

    // quantity
    if ($qty_raw === '' || trim($qty_raw) === '' || !ctype_digit(trim($qty_raw))) { $_SESSION['open_add_modal']=1; back_with_error('Quantity must be a whole number.'); }
    $quantity = (int)$qty_raw;
    if ($quantity < 0) { $_SESSION['open_add_modal']=1; back_with_error('Quantity cannot be negative.'); }

    $line_total = $unit_price * $quantity;

    // Image (required for add)
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['open_add_modal']=1; back_with_error('Please choose an image to upload.');
    }
    $upl = $_FILES['image'];
    if ($upl['error'] !== UPLOAD_ERR_OK) { $_SESSION['open_add_modal']=1; back_with_error('Image upload failed. Code: '.(int)$upl['error']); }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($upl['tmp_name']) ?: '';
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
    if (!isset($allowed[$mime])) { $_SESSION['open_add_modal']=1; back_with_error('Unsupported image type. Use JPG, PNG, GIF, or WEBP.'); }

    $ext      = $allowed[$mime];
    $base     = pathinfo($upl['name'], PATHINFO_FILENAME);
    $safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
    $newName  = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

    $upload_dir = __DIR__ . '/../images';
    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
        $_SESSION['open_add_modal']=1; back_with_error('Cannot create uploads directory.');
    }
    $dest = $upload_dir . DIRECTORY_SEPARATOR . $newName;
    if (!move_uploaded_file($upl['tmp_name'], $dest)) {
        $_SESSION['open_add_modal']=1; back_with_error('Failed to save uploaded image.');
    }

    $sql = "INSERT INTO products
            (user_id, product_name, description, unit_price, quantity, `file`, line_total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) { $_SESSION['open_add_modal']=1; back_with_error('Prepare failed: '.$conn->error); }

    $stmt->bind_param("issdisd", $sellerId, $product_name, $description, $unit_price, $quantity, $newName, $line_total);
    if (!$stmt->execute()) { $_SESSION['open_add_modal']=1; back_with_error('Execute failed: '.$stmt->error); }
    $stmt->close();

    unset($_SESSION['open_add_modal']);
    back_with_success('Product added.');
}

/* -------------------- UPDATE -------------------- */
if (isset($_POST['update'])) {
    $id           = (int)($_POST['id'] ?? 0);
    $product_name = trim((string)($_POST['product_name'] ?? ''));
    $desc_raw     = (string)($_POST['description'] ?? '');
    $description  = preg_match('/\S/', $desc_raw) ? trim($desc_raw) : '';
    $unit_price   = $_POST['price'] ?? '';
    $quantity     = $_POST['quantity'] ?? '';

    if ($id <= 0) back_with_error('Invalid product ID.');
    if (!preg_match('/\S/', $product_name)) back_with_error('Product name is required.');
    if (!is_numeric($unit_price)) back_with_error('Price must be a number.');
    if (!is_numeric($quantity))   back_with_error('Quantity must be a number.');
    $unit_price = (float)$unit_price;
    $quantity   = (int)$quantity;
    if ($unit_price < 0) back_with_error('Price cannot be negative.');
    if ($quantity   < 0) back_with_error('Quantity cannot be negative.');

    $line_total = $unit_price * $quantity;

    // optional image
    $fileSql   = '';
    $fileParam = null;
    if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
        $mime = mime_content_type($_FILES['image']['tmp_name']) ?: '';
        if (!isset($allowed[$mime])) back_with_error('Invalid image type. Allowed: JPG, PNG, GIF, WEBP.');
        $ext = $allowed[$mime];
        $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

        $uploadDir = realpath(__DIR__ . '/../images');
        if ($uploadDir === false) {
            $uploadDir = __DIR__ . '/../images';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
        }
        $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) back_with_error('Failed to store uploaded image.');

        $fileParam = $newName;
        $fileSql   = ', file = ?';
    }

    $sql = "UPDATE products
            SET product_name = ?, description = ?, unit_price = ?, quantity = ?, line_total = ?, updated_at = NOW() $fileSql
            WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) back_with_error('Prepare failed: '.$conn->error);

    if ($fileParam !== null) {
        // s s d i d s i i
        if (!$stmt->bind_param("ssdidsii", $product_name, $description, $unit_price, $quantity, $line_total, $fileParam, $id, $sellerId)) {
            back_with_error('Bind failed: '.$stmt->error);
        }
    } else {
        // s s d i d i i
        if (!$stmt->bind_param("ssdidii", $product_name, $description, $unit_price, $quantity, $line_total, $id, $sellerId)) {
            back_with_error('Bind failed: '.$stmt->error);
        }
    }

    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        back_with_error('Execute failed: '.$err);
    }
    $stmt->close();

    unset($_SESSION['open_add_modal']);
    back_with_success('Product updated.');
}

/* -------------------- DELETE -------------------- */
if (isset($_POST['delete'])) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) back_with_error('Invalid product ID.');

    // 1) Get product name first (for a nicer confirmation message)
    $name = null;
    if ($stmt = $conn->prepare("SELECT product_name FROM products WHERE id = ? AND user_id = ?")) {
        $stmt->bind_param("ii", $id, $sellerId);
        if ($stmt->execute()) {
            $stmt->bind_result($name);
            $stmt->fetch();
        }
        $stmt->close();
    }

    // 2) Proceed to delete
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    if (!$stmt) back_with_error('Prepare failed: '.$conn->error);
    if (!$stmt->bind_param("ii", $id, $sellerId)) back_with_error('Bind failed: '.$stmt->error);
    if (!$stmt->execute()) {
        $err = $stmt->error;
        $stmt->close();
        back_with_error('Execute failed: '.$err);
    }
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected > 0) {
        unset($_SESSION['open_add_modal']);
        // Compose a friendlier confirmation text
        $label = $name ? "‘{$name}’ (ID {$id})" : "ID {$id}";
        back_with_success("Product deleted: {$label}.");
    } else {
        back_with_error('Product not found or not owned by you.');
    }
}
/* -------------------- FALLBACK -------------------- */
back_with_error('No action.');
