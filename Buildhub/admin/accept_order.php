<?php
session_start();
$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { http_response_code(500); exit('DB error'); }

$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($sellerId <= 0) { header('Location: ../login/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: myorders.php'); exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
if ($orderId <= 0) { header('Location: myorders.php?err=bad_order'); exit; }

/* Only allow accepting orders that belong to THIS seller and are still pending */
$sql = "UPDATE orders
        SET status = 'accepted'
        WHERE id = ? AND supplier_id = ? AND status = 'pending'";
if ($stmt = mysqli_prepare($conn, $sql)) {
  mysqli_stmt_bind_param($stmt, 'ii', $orderId, $sellerId);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

header('Location: myorders.php?accepted=1'); // adjust path if needed
exit;

