<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "user_db");

if (!$conn) {
  $_SESSION['message'] = "Database connection failed.";
  header("Location: index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
  $userId = (int)$_POST['user_id'];

  if (isset($_POST['delete'])) {
    // Delete user
    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $_SESSION['message'] = "User deleted successfully.";
  }

  if (isset($_POST['violation'])) {
    // Record violation (example — you can change this to insert into violations table)
    $_SESSION['message'] = "Violation reported for user ID: $userId.";
  }

  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit;
}
?>
