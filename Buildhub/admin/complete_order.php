<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { http_response_code(500); exit('DB error'); }

$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($sellerId <= 0) {
  $_SESSION['flash_error'] = 'Not authorized.';
  header('Location: myorders.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: myorders.php');
  exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) {
  $_SESSION['flash_error'] = 'Bad order.';
  header('Location: myorders.php');
  exit;
}

/* 1) Verify the order belongs to this seller and is currently accepted */
$check = $conn->prepare("SELECT status, supplier_id FROM orders WHERE id = ? LIMIT 1");
$check->bind_param('i', $orderId);
$check->execute();
$res = $check->get_result();
$row = $res->fetch_assoc();
$check->close();

if (!$row) {
  $_SESSION['flash_error'] = 'Order not found.';
  header('Location: myorders.php'); exit;
}
if ((int)$row['supplier_id'] !== $sellerId) {
  $_SESSION['flash_error'] = 'You do not own this order.';
  header('Location: myorders.php'); exit;
}
if (strtolower($row['status']) !== 'accepted') {
  $_SESSION['flash_error'] = 'Only accepted orders can be completed.';
  header('Location: myorders.php'); exit;
}

/* 2) Update to completed */
$upd = $conn->prepare("
  UPDATE orders
  SET status = 'completed'
  WHERE id = ? AND supplier_id = ? AND status = 'accepted'
");
$upd->bind_param('ii', $orderId, $sellerId);
$upd->execute();
$affected = $upd->affected_rows;
$upd->close();

if ($affected > 0) {
  $_SESSION['flash_success'] = 'Order marked as completed.';
} else {
  $_SESSION['flash_error'] = 'Could not complete order (already updated or ownership mismatch).';
}

header('Location: myorders.php');
exit;
