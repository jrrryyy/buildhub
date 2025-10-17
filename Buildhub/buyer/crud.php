<?php
session_start();
require __DIR__ . '/../login/config.php'; // must set $conn = new mysqli(..., dbname)

/* ---- Get posted fields ---- */
$recipient_name = trim($_POST['recipient_name'] ?? '');
$address_line   = trim($_POST['address_line'] ?? '');
$province_raw   = $_POST['province'] ?? null;
$province       = ($province_raw === '' ? null : $province_raw); // store NULL if left blank
$phone          = trim($_POST['phone'] ?? '');
$schedule_date  = trim($_POST['schedule_date'] ?? '');   // YYYY-MM-DD

$product_name   = trim($_POST['product_name'] ?? '');
$grand          = (float)($_POST['grand'] ?? 0);         // from form (total)
$total_amount   = (int)round($grand);                    // column is INT(11)

$ordered_at     = date('Y-m-d H:i:s');                   // DATETIME
$status         = strtolower(trim($_POST['status'] ?? 'pending')); // enum: pending|accepted|completed

/* ---- Provide required FKs (table says NOT NULL) ---- */
$buyer_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

/* If no logged-in user, don't attempt insert (will violate FK) */
if ($buyer_id <= 0) {
  header('Location: ../login/index.php?next=buyer/checkout.php');
  exit;
}

/* If you don't have a supplier yet, keep 0; make the column nullable later if needed */
$supplier_id = 0;

/* ---- Basic checks ---- */
if ($recipient_name === '' || $address_line === '' || $phone === '' || $schedule_date === '' ||
    $product_name === '' || $total_amount <= 0) {
  header('Location: ./checkout.php?error=invalid');
  exit;
}

/* ---- Insert (columns match your table) ---- */
$sql = "INSERT INTO orders
  (buyer_id, supplier_id, recipient_name, address_line, province, phone, schedule_date,
   product_name, total_amount, ordered_at, status)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  exit('DB prepare error: ' . $conn->error);
}

/* i i s s s s s s i s s  -> 11 params */
$stmt->bind_param(
  'iisssssssis',
  $buyer_id,
  $supplier_id,
  $recipient_name,
  $address_line,
  $province,       // can be NULL
  $phone,
  $schedule_date,  // 'YYYY-MM-DD'
  $product_name,
  $total_amount,   // INT
  $ordered_at,     // 'YYYY-MM-DD HH:MM:SS'
  $status
);

if (!$stmt->execute()) {
  http_response_code(500);
  exit('DB execute error: ' . $stmt->error);
}

$stmt->close();
header('Location: ./myorders.php?placed=1');
exit;
?>