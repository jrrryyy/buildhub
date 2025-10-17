<?php
session_start();
require '../login/config.php'; // should expose $conn (mysqli)

// If your config might not create $conn, keep this fallback:
if (!isset($conn) || !$conn instanceof mysqli) {
    $conn = mysqli_connect("localhost", "root", "", "user_db");
    if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
    mysqli_set_charset($conn, "utf8mb4");
}

// helpers
function has_text($v) { return (bool)preg_match('/\S/', $v ?? ''); }
function back_with_error($msg) {
    $_SESSION['crud_error'] = $msg;
    header('Location: ../admin/orders.php'); exit();
}
function back_with_success($msg) {
    $_SESSION['crud_success'] = $msg;
    header('Location: ../admin/orders.php'); exit();
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

    // product_name: required + no spaces-only
    if ($name_raw === '') back_with_error('Product name is required.');
    if (trim($name_raw) === '') back_with_error('Product name cannot be spaces only.');
    $product_name = trim($name_raw);

    // description: optional — spaces-only becomes empty (no error)
    $description = (trim($desc_raw) === '') ? '' : trim($desc_raw);

    // price: required + no spaces-only + numeric
    if ($price_raw === '') back_with_error('Price is required.');
    if (trim($price_raw) === '') back_with_error('Price cannot be spaces only.');
    if (!is_numeric($price_raw)) back_with_error('Price must be a number.');
    $unit_price = (float)$price_raw;
    if ($unit_price < 0) back_with_error('Price cannot be negative.');

    // quantity: required + no spaces-only + integer
    if ($qty_raw === '') back_with_error('Quantity is required.');
    if (trim($qty_raw) === '') back_with_error('Quantity cannot be spaces only.');
    if (!ctype_digit(trim($qty_raw))) back_with_error('Quantity must be a whole number.');
    $quantity = (int)$qty_raw;
    if ($quantity < 0) back_with_error('Quantity cannot be negative.');

    $line_total = $unit_price * $quantity;

    // ... (upload checks unchanged)
    // === Image upload (required) ===
// <form ... enctype="multipart/form-data"> and <input type="file" name="image">
if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    back_with_error('Please choose an image to upload.');
}

$upl = $_FILES['image'];
if ($upl['error'] !== UPLOAD_ERR_OK) back_with_error('Image upload failed. Code: '.(int)$upl['error']);

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($upl['tmp_name']) ?: '';
$allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
if (!isset($allowed[$mime])) back_with_error('Unsupported image type. Use JPG, PNG, GIF, or WEBP.');

$ext      = $allowed[$mime];
$base     = pathinfo($upl['name'], PATHINFO_FILENAME);
$safeBase = preg_replace('/[^A-Za-z0-9_\-]/', '_', $base);
$newName  = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

$upload_dir = __DIR__ . '/../images';
if (!is_dir($upload_dir) && !mkdir($upload_dir, 0775, true)) {
    back_with_error('Cannot create uploads directory.');
}

$dest = $upload_dir . DIRECTORY_SEPARATOR . $newName;
if (!move_uploaded_file($upl['tmp_name'], $dest)) {
    back_with_error('Failed to save uploaded image.');
}


    $sql = "INSERT INTO products
            (user_id, product_name, description, unit_price, quantity, `file`, line_total, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    if (!$stmt) back_with_error('Prepare failed: '.$conn->error);

    $stmt->bind_param(
        "issdisd",
        $sellerId, $product_name, $description, $unit_price, $quantity, $newName, $line_total
    );
    if (!$stmt->execute()) back_with_error('Execute failed: '.$stmt->error);
    $stmt->close();

    back_with_success('Product added.');
}

/* -------------------- UPDATE -------------------- */
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

  /* ----- optional image handling ----- */
  $fileSql   = '';     // <- default: do not change image
  $fileParam = null;   // <- default: no file param

  if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    // Validate basic image constraints
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','image/webp'=>'webp'];
    $mime = mime_content_type($_FILES['image']['tmp_name']) ?: '';
    if (!isset($allowed[$mime])) {
      back_with_error('Invalid image type. Allowed: JPG, PNG, GIF, WEBP.');
    }

    // Make a safe filename
    $ext = $allowed[$mime];
    $newName = date('Ymd_His') . '_' . bin2hex(random_bytes(5)) . '.' . $ext;

    // Set your upload directory; adjust if your project uses a different folder
    // This saves the file under admin/../images
    $uploadDir = realpath(__DIR__ . '/../images'); // adjust if needed
    if ($uploadDir === false) {
      // fallback: create if not exists
      $uploadDir = __DIR__ . '/../images';
      if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0775, true);
      }
    }

    $dest = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
      back_with_error('Failed to store uploaded image.');
    }

    // We store only the filename in DB (your listings page prepends ../images/)
    $fileParam = $newName;
    $fileSql   = ', file = ?';
  }

  /* ----- build and bind query ----- */
  $sql = "UPDATE products
          SET product_name = ?, description = ?, unit_price = ?, quantity = ?, line_total = ?, updated_at = NOW() $fileSql
          WHERE id = ? AND user_id = ?";

  $stmt = $conn->prepare($sql);
  if (!$stmt) back_with_error('Prepare failed: '.$conn->error);

  if ($fileParam !== null) {
    // s s d i d s i i
    if (!$stmt->bind_param(
      "ssdidsii",
      $product_name, $description, $unit_price, $quantity, $line_total, $fileParam, $id, $sellerId
    )) {
      back_with_error('Bind failed: '.$stmt->error);
    }
  } else {
    // s s d i d i i
    if (!$stmt->bind_param(
      "ssdidii",
      $product_name, $description, $unit_price, $quantity, $line_total, $id, $sellerId
    )) {
      back_with_error('Bind failed: '.$stmt->error);
    }
  }

  if (!$stmt->execute()) {
    $err = $stmt->error;
    $stmt->close();
    back_with_error('Execute failed: '.$err);
  }
  $stmt->close();

  back_with_success('Product updated.');
}

/* ============ other handlers (add/delete/etc) go here ============ */

// If no recognized action:
back_with_error('No action.');


/* -------------------- DELETE -------------------- */
if (isset($_POST['delete'])) {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) back_with_error('No valid ID to delete.');

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
    if (!$stmt) back_with_error('Prepare failed: '.$conn->error);

    $stmt->bind_param("ii", $id, $sellerId);
    if (!$stmt->execute()) back_with_error('Execute failed: '.$stmt->error);
    $stmt->close();

    back_with_success('Product deleted.');
}

// Fallback
header('Location: ../admin/orders.php'); exit();
