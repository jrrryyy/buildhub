<?php
// Simple forgot password form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    // Here you would normally send a reset link to the email if it exists in your database
    $message = "If this email is registered, a password reset link will be sent.";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="form-box active">
            <form method="post">
                <h2>Forgot Password</h2>
                <?php if (!empty($message)) echo "<p>$message</p>"; ?>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
                <p><a href="../login/index.php">Back to Login</a></p>
            </form>
        </div>
    </div>
</body>
</html>