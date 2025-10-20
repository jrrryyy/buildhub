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
  $_SESSION['flash_error'] = 'Invalid order ID.';
  header('Location: myorders.php');
  exit;
}

/* 1) Verify the order belongs to this seller and can be cancelled */
$check = $conn->prepare("SELECT id, status, product_name FROM orders WHERE id = ? AND supplier_id = ? LIMIT 1");
$check->bind_param('ii', $orderId, $sellerId);
$check->execute();
$res = $check->get_result();
$order = $res->fetch_assoc();
$check->close();

if (!$order) {
  $_SESSION['flash_error'] = 'Order not found or you do not have permission to cancel it.';
  header('Location: myorders.php');
  exit;
}

$currentStatus = strtolower($order['status']);
if (!in_array($currentStatus, ['pending', 'accepted'])) {
  $_SESSION['flash_error'] = 'Only pending or accepted orders can be cancelled.';
  header('Location: myorders.php');
  exit;
}

/* 2) Update order status to cancelled */
$update = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND supplier_id = ?");
$update->bind_param('ii', $orderId, $sellerId);
$update->execute();
$affected = $update->affected_rows;
$update->close();

if ($affected > 0) {
  $_SESSION['flash_success'] = 'Order "' . htmlspecialchars($order['product_name']) . '" has been cancelled successfully.';
} else {
  $_SESSION['flash_error'] = 'Could not cancel order. Please try again.';
}

header('Location: myorders.php');
exit;
