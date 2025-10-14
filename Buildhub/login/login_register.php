<?php
/*add value in database*/
session_start();
require 'config.php';
if (isset($_POST['register'])) {
    $fname = $_POST['first_name'];
    $lname = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role'];

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = "Email is already exists.";
        $_SESSION['active_form'] = "register";
        header('Location: register.php');
        exit();
      

        } else {
        $conn->query("INSERT INTO users (fname, lname, email, password, role) VALUES ('$fname', '$lname', '$email', '$password', '$role')");
        }

    header('Location: index.php');
    exit();

    
}
/*getting value in database*/
        require_once __DIR__ . '/../nav/init.php';
  // makes $conn (mysqli)

if (isset($_POST['login'])) {
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // Prepared statement (prevents SQL injection)
  $stmt = $conn->prepare("SELECT id, fname, lname, email, password, role FROM users WHERE email = ? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $user   = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    // Set session once
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['fname']   = $user['fname'];
    $_SESSION['lname']   = $user['lname'];      // make sure your column is 'lname'
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['is_seller'] = ($user['role'] === 'seller');

    session_regenerate_id(true);

    // Redirect based on role (NO earlier exit before this)
    if ($user['role'] === 'seller') {
      header('Location: ../admin/index.php');
    } else {
      header('Location: ../buyer/index.php');   // or /buyer/orders.php if you prefer
    }
    exit;
  }

  // Invalid login
  $_SESSION['login_error'] = 'Invalid email or password.';
  header('Location: index.php');
  exit;
    }
?>