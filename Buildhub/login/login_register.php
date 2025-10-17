<?php
/*add value in database*/
session_start();
require 'config.php';
if (isset($_POST['register'])) {
    $fname    = trim($_POST['first_name'] ?? '');
    $lname    = trim($_POST['last_name']  ?? '');
    $email    = trim($_POST['email']      ?? '');
    $password = $_POST['password']        ?? ''; // don't trim passwords
    $role     = trim($_POST['role']       ?? '');

    $errors = [];
    if ($fname === '') $errors[] = 'First name is required.';
    if ($lname === '') $errors[] = 'Last name is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    // ✅ password must contain at least one non-space character
    if ($password === '' || !preg_match('/\S/', $password)) {
        $errors[] = 'Password cannot be empty or spaces only.';
    }

    if ($errors) {
        $_SESSION['register_error'] = implode(' ', $errors);
        $_SESSION['active_form'] = 'register';
        header('Location: register.php');
        exit();
    }

    // duplicate email check + insert (prepared)
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = "register";
        header('Location: register.php');
        exit();
    }
    $stmt->close();

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $ins = $conn->prepare("INSERT INTO users (fname, lname, email, password, role) VALUES (?,?,?,?,?)");
    $ins->bind_param('sssss', $fname, $lname, $email, $hash, $role);
    $ins->execute();
    $ins->close();

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