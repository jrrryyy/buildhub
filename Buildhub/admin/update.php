<?php
session_start();
require '../login/config.php';

/* Update Password */
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $email = $_SESSION['email'];

    $result = mysqli_query($conn, "SELECT password FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if (password_verify($current_password, $user['password'])) {
        $hashed_new = password_hash($new_password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE users SET password='$hashed_new' WHERE email='$email'";
        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['password_success'] = "Password updated successfully.";
        } else {
            $_SESSION['password_error'] = "Error updating password.";
        }
    } else {
        $_SESSION['password_error'] = "enetr password is incorrect.";
    }

    header("Location: settings.php");
    exit();
}

/* Update Personal Info */
if (isset($_POST['update_profile'])) {
    // Get data from form
    $current_email = $_POST['current_email'];  
    $new_email = $_POST['new_email'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    // Session email (logged-in user)
    $email = $_SESSION['email'];

    // Step 1: Fetch current user info
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && $current_email === $user['email']) {
        // Step 2: Check if new email already exists
        $check_new = mysqli_query($conn, "SELECT email FROM users WHERE email='$new_email'");
        if (mysqli_num_rows($check_new) > 0 && $new_email !== $email) {
            $_SESSION['profile_error'] = "New email is already taken.";
        } else {
            // Step 3: Update user info
            $update_sql = "UPDATE users 
                           SET fname='$fname', lname='$lname', email='$new_email' 
                           WHERE email='$email'";
            
            if (mysqli_query($conn, $update_sql)) {
                // Step 4: Update session values
                $_SESSION['fname'] = $fname;
                $_SESSION['lname'] = $lname;
                $_SESSION['email'] = $new_email;
                $_SESSION['profile_success'] = "Profile updated successfully.";
            } else {
                $_SESSION['profile_error'] = "Error updating profile: " . mysqli_error($conn);
            }
        }
    } else {
        $_SESSION['profile_error'] = "Current email is incorrect.";
    }

    header("Location: settings.php");
    exit();
}
?>
