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

$buyerId = (int)($_SESSION['user_id'] ?? 0);
if ($buyerId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
    exit;
}

// 🖼 Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $uploadDir = '../images/profiles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $file = $_FILES['profile_picture'];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit;
        }

        // Rename file safely
        $newName = 'buyer_' . $buyerId . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $newName;

        // Move file to destination
        if (move_uploaded_file($file['tmp_name'], $destPath)) {
            // Save new filename to DB
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param('si', $newName, $buyerId);
            $stmt->execute();
            $stmt->close();

            echo json_encode(['success' => true, 'message' => 'Profile picture updated.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded file.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Upload error.']);
        exit;
    }
}

// 🧍 Handle profile info update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $newEmail = trim($_POST['new_email'] ?? '');
    $currentEmail = $_POST['current_email'] ?? '';

    if (empty($fname) || empty($lname)) {
        $_SESSION['profile_error'] = 'First name and last name are required.';
        header('Location: profile.php');
        exit;
    }

    if (!empty($newEmail) && $newEmail !== $currentEmail) {
        $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $checkEmail->bind_param('si', $newEmail, $buyerId);
        $checkEmail->execute();
        $result = $checkEmail->get_result();
        $checkEmail->close();

        if ($result->num_rows > 0) {
            $_SESSION['profile_error'] = 'Email already exists.';
            header('Location: profile.php');
            exit;
        }

        $update = $conn->prepare("UPDATE users SET fname = ?, lname = ?, email = ? WHERE id = ?");
        $update->bind_param('sssi', $fname, $lname, $newEmail, $buyerId);
        $update->execute();
        $update->close();

        $_SESSION['fname'] = $fname;
        $_SESSION['lname'] = $lname;
        $_SESSION['email'] = $newEmail;
    } else {
        $update = $conn->prepare("UPDATE users SET fname = ?, lname = ? WHERE id = ?");
        $update->bind_param('ssi', $fname, $lname, $buyerId);
        $update->execute();
        $update->close();

        $_SESSION['fname'] = $fname;
        $_SESSION['lname'] = $lname;
    }

    $_SESSION['profile_success'] = 'Profile updated successfully.';
    header('Location: profile.php');
    exit;
}

// 🔐 Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $buyerId = (int)($_SESSION['user_id'] ?? 0);
    if ($buyerId <= 0) {
        $_SESSION['password_error'] = 'Not authorized.';
        header('Location: profile.php');
        exit;
    }

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword)) {
        $_SESSION['password_error'] = 'Current password and new password are required.';
        header('Location: profile.php');
        exit;
    }

    if (strlen($newPassword) < 6) {
        $_SESSION['password_error'] = 'New password must be at least 6 characters long.';
        header('Location: profile.php');
        exit;
    }

    // Get current password
    $getUser = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $getUser->bind_param('i', $buyerId);
    $getUser->execute();
    $result = $getUser->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['password_error'] = 'User not found.';
        header('Location: profile.php');
        exit;
    }

    $user = $result->fetch_assoc();
    $getUser->close();

    // Check password (supports both hashed and plain text)
    if (!$user || (!password_verify($currentPassword, $user['password']) && $currentPassword !== $user['password'])) {
        $_SESSION['password_error'] = 'Current password is incorrect.';
        header('Location: profile.php');
        exit;
    }

    // Hash and update new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $update->bind_param('si', $hashedPassword, $buyerId);
    $update->execute();
    $update->close();

    $_SESSION['password_success'] = 'Password updated successfully.';
    header('Location: profile.php');
    exit;
}
