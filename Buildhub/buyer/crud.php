<?php
session_start();
require __DIR__ . '/../login/config.php'; // $conn


// 1) Read POST
$recipient_name = trim($_POST['recipient_name'] ?? '');
$address_line   = trim($_POST['address_line'] ?? '');
$province       = ($_POST['province'] ?? null) ?: null;
$phone          = trim($_POST['phone'] ?? '');
$schedule_date  = trim($_POST['schedule_date'] ?? '');
$product_id     = (int)($_POST['product_id'] ?? 0);
$grand          = (float)($_POST['grand'] ?? 0);
$total_amount   = (int)round($grand);

$buyer_id = (int)($_SESSION['user_id'] ?? 0);
// Set timezone to Philippines and get current timestamp
date_default_timezone_set('Asia/Manila');
$ordered_at = date('Y-m-d H:i:s'); // Current timestamp when order is placed
$status = 'pending';

// 2) Basic checks
if ($buyer_id <= 0) { header('Location: ../login/index.php'); exit; }
if ($recipient_name==='' || $address_line==='' || $phone==='' || $schedule_date==='' ||
    $product_id<=0 || $total_amount<=0) {
  header('Location: ./checkout.php?error=invalid'); exit;
}

// 3) Date check (today or future)
$tz = new DateTimeZone('Asia/Manila');
$today = new DateTimeImmutable('today', $tz);
$dt = DateTimeImmutable::createFromFormat('Y-m-d', $schedule_date, $tz);
$errs = DateTime::getLastErrors();
$okFmt = $dt && $errs['warning_count']==0 && $errs['error_count']==0 && $dt->format('Y-m-d')===$schedule_date;
if (!$okFmt) { header('Location: ./checkout.php?error=bad_date_format'); exit; }
if ($dt < $today) { header('Location: ./checkout.php?error=past_date'); exit; }

// 4) >>> PLACE THIS SUPPLIER LOOKUP RIGHT HERE <<<
/* ---- Look up supplier (product owner) BEFORE insert ---- */
$stmt = $conn->prepare("
  SELECT u.id AS supplier_id, p.product_name
  FROM products p
  JOIN users u ON u.id = p.user_id      -- guarantees the user exists
  WHERE p.id = ?
  LIMIT 1
");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$stmt->bind_result($supplier_id, $product_name_db);
if (!$stmt->fetch()) {
  $stmt->close();
  header('Location: ./checkout.php?error=supplier_missing'); // product has no valid owner
  exit;
}
$stmt->close();

$supplier_id = (int)$supplier_id;      // should be > 0 here
$product_name = $product_name_db;      // trust DB name


//auto date

// 5) Insert
$sql = "INSERT INTO orders
  (buyer_id, supplier_id, recipient_name, address_line, province, phone, schedule_date,
   product_name, total_amount, ordered_at, status)
  VALUES (?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
  http_response_code(500);
  exit('DB prepare error: '.$conn->error);
}

$stmt->bind_param(
  'iissssssdss',
  $buyer_id, $supplier_id, $recipient_name, $address_line, $province,
  $phone, $schedule_date, $product_name, $total_amount, $ordered_at, $status
);

if (!$stmt->execute()) {
  http_response_code(500);
  exit('DB execute error: '.$stmt->error);
}
$stmt->close();

header('Location: ./myorders.php?placed=1');
exit;
