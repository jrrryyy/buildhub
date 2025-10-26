<?php
session_start();
date_default_timezone_set('Asia/Manila');

// Database connection
$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
    exit;
}

$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($sellerId <= 0) {
    $_SESSION['profile_error'] = 'Not authorized.';
    header('Location: profile.php');
    exit;
}

/* -----------------------------
   ✅ PROFILE PICTURE UPLOAD
------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File upload error']);
        exit;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File too large.']);
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
        exit;
    }

    $uploadDir = '../images/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $sellerId . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $update = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $update->bind_param('si', $filename, $sellerId);
        $update->execute();
        $update->close();

        echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
    }
    exit;
}

/* -----------------------------
   ✅ PROFILE INFO UPDATE
------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $newEmail = trim($_POST['new_email'] ?? '');
    $currentEmail = trim($_POST['current_email'] ?? '');

    if (empty($fname) || empty($lname)) {
        $_SESSION['profile_error'] = 'First name and last name are required.';
        header('Location: profile.php');
        exit;
    }

    if (!empty($newEmail) && $newEmail !== $currentEmail) {
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->bind_param('si', $newEmail, $sellerId);
        $checkEmail->execute();
        $result = $checkEmail->get_result();
        $checkEmail->close();

        if ($result->num_rows > 0) {
            $_SESSION['profile_error'] = 'Email already exists.';
            header('Location: profile.php');
            exit;
        }

        $update = $conn->prepare("UPDATE users SET fname = ?, lname = ?, email = ? WHERE id = ?");
        $update->bind_param('sssi', $fname, $lname, $newEmail, $sellerId);
        $update->execute();
        $update->close();

        $_SESSION['email'] = $newEmail;
    } else {
        $update = $conn->prepare("UPDATE users SET fname = ?, lname = ? WHERE id = ?");
        $update->bind_param('ssi', $fname, $lname, $sellerId);
        $update->execute();
        $update->close();
    }

    $_SESSION['fname'] = $fname;
    $_SESSION['lname'] = $lname;
    $_SESSION['profile_success'] = 'Profile updated successfully.';
    header('Location: profile.php');
    exit;
}

/* -----------------------------
   ✅ PASSWORD UPDATE
------------------------------ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        $_SESSION['password_error'] = 'Both fields are required.';
        header('Location: profile.php');
        exit;
    }

    if (strlen($newPassword) < 6) {
        $_SESSION['password_error'] = 'New password must be at least 6 characters long.';
        header('Location: profile.php');
        exit;
    }

    $getUser = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $getUser->bind_param('i', $sellerId);
    $getUser->execute();
    $result = $getUser->get_result();
    $user = $result->fetch_assoc();
    $getUser->close();

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        $_SESSION['password_error'] = 'Current password is incorrect.';
        header('Location: profile.php');
        exit;
    }

    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param('si', $hashedPassword, $sellerId);
    $update->execute();
    $update->close();

    $_SESSION['password_success'] = 'Password updated successfully.';
    header('Location: profile.php');
    exit;
}

/* -----------------------------
   DEFAULT REDIRECT
------------------------------ */
header('Location: profile.php');
exit;
?>
