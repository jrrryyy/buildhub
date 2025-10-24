<?php
session_start();
require '../login/config.php'; // must expose $conn (mysqli)

/* =========================
   Update Password
   ========================= */
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $email            = $_SESSION['email'] ?? '';

    // Basic guards
    if ($email === '' || !preg_match('/\S/', $current_password)) {
        $_SESSION['password_error'] = 'Please enter your current password.';
        header("Location: settings.php"); exit();
    }
    // New password must have at least one non-space character
    if ($new_password === '' || !preg_match('/\S/', $new_password)) {
        $_SESSION['password_error'] = 'New password cannot be empty or spaces only.';
        header("Location: settings.php"); exit();
    }
    // (Optional) Minimum length requirement
    if (strlen($new_password) < 8) {
        $_SESSION['password_error'] = 'New password must be at least 8 characters.';
        header("Location: settings.php"); exit();
    }

    // Fetch current hash
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res  = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($current_password, $user['password'])) {
        $_SESSION['password_error'] = 'Current password is incorrect.';
        header("Location: settings.php"); exit();
    }

    // Update to new hash
    $hashed_new = password_hash($new_password, PASSWORD_BCRYPT);
    $upd = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $upd->bind_param('ss', $hashed_new, $email);

    if ($upd->execute()) {
        $_SESSION['password_success'] = "Password updated successfully.";
    } else {
        $_SESSION['password_error'] = "Error updating password.";
    }
    $upd->close();

    header("Location: settings.php"); exit();
}

/* =========================
   Update Personal Info
   ========================= */
/* =========================
   Update Personal Info
   ========================= */
if (isset($_POST['update_profile'])) {
    $current_email = trim($_POST['current_email'] ?? '');
    $new_email     = $_POST['new_email'] ?? ''; // don't trim yet (check spaces)
    $fname         = $_POST['fname'] ?? '';
    $lname         = $_POST['lname'] ?? '';
    $session_email = $_SESSION['email'] ?? '';

    $errors = [];

    // ✅ Must be logged in
    if ($session_email === '') $errors[] = 'Not logged in.';
    if ($current_email === '' || !filter_var($current_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Current email is invalid.';
    }

    // ✅ Detect and block fields that are spaces-only
    if ($fname !== '' && !preg_match('/\S/', $fname)) $errors[] = 'First name cannot be spaces only.';
    if ($lname !== '' && !preg_match('/\S/', $lname)) $errors[] = 'Last name cannot be spaces only.';
    if ($new_email !== '' && !preg_match('/\S/', $new_email)) $errors[] = 'New email cannot be spaces only.';

    // ✅ Trim valid inputs after checking spaces-only
    $fname = trim($fname);
    $lname = trim($lname);
    $new_email = trim($new_email);

    if ($errors) {
        $_SESSION['profile_error'] = implode(' ', $errors);
        header("Location: settings.php"); exit();
    }

    // ✅ Fetch current user by session email
    $stmt = $conn->prepare("SELECT fname, lname, email FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $session_email);
    $stmt->execute();
    $res  = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user || $current_email !== $user['email']) {
        $_SESSION['profile_error'] = "Current email is incorrect.";
        header("Location: settings.php"); exit();
    }

    // ✅ Keep old values for blank fields
    $final_fname  = $fname !== '' ? $fname : $user['fname'];
    $final_lname  = $lname !== '' ? $lname : $user['lname'];
    $final_email  = $new_email !== '' ? $new_email : $user['email'];

    // ✅ Check if new email is used by another user
    if (strcasecmp($final_email, $user['email']) !== 0) {
        $chk = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $chk->bind_param('s', $final_email);
        $chk->execute();
        $chk->store_result();
        if ($chk->num_rows > 0) {
            $chk->close();
            $_SESSION['profile_error'] = "New email is already taken.";
            header("Location: settings.php"); exit();
        }
        $chk->close();
    }

    // ✅ Perform update
    $upd = $conn->prepare("UPDATE users SET fname = ?, lname = ?, email = ? WHERE email = ?");
    $upd->bind_param('ssss', $final_fname, $final_lname, $final_email, $session_email);

    if ($upd->execute()) {
        // ✅ Refresh session values
        $_SESSION['fname'] = $final_fname;
        $_SESSION['lname'] = $final_lname;
        $_SESSION['email'] = $final_email;
        $_SESSION['profile_success'] = "Profile updated successfully.";
    } else {
        $_SESSION['profile_error'] = "Error updating profile.";
    }
    $upd->close();

    header("Location: settings.php"); exit();
}

