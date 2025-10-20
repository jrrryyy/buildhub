<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { http_response_code(500); exit('DB error'); }

$buyerId = (int)($_SESSION['user_id'] ?? 0);
if ($buyerId <= 0) {
  $_SESSION['flash_error'] = 'Not authorized.';
  header('Location: myorders.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: myorders.php');
  exit;
}

$orderId = (int)($_POST['item_id'] ?? 0);
if ($orderId <= 0) {
  $_SESSION['flash_error'] = 'Invalid order ID.';
  header('Location: myorders.php');
  exit;
}

/* 1) Verify the order belongs to this buyer and is cancelled */
$check = $conn->prepare("SELECT id, status, product_name FROM orders WHERE id = ? AND buyer_id = ? LIMIT 1");
$check->bind_param('ii', $orderId, $buyerId);
$check->execute();
$res = $check->get_result();
$order = $res->fetch_assoc();
$check->close();

if (!$order) {
  $_SESSION['flash_error'] = 'Order not found or you do not have permission to delete it.';
  header('Location: myorders.php');
  exit;
}

$currentStatus = strtolower($order['status']);
if ($currentStatus !== 'cancelled') {
  $_SESSION['flash_error'] = 'Only cancelled orders can be deleted.';
  header('Location: myorders.php');
  exit;
}

/* 2) Delete the order permanently */
$delete = $conn->prepare("DELETE FROM orders WHERE id = ? AND buyer_id = ? AND status = 'cancelled'");
$delete->bind_param('ii', $orderId, $buyerId);
$delete->execute();
$affected = $delete->affected_rows;
$delete->close();

if ($affected > 0) {
  $_SESSION['flash_success'] = 'Order "' . htmlspecialchars($order['product_name']) . '" has been permanently deleted.';
} else {
  $_SESSION['flash_error'] = 'Could not delete order. Please try again.';
}

header('Location: myorders.php');
exit;
