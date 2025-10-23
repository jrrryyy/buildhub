<?php
session_start();

// Get logged-in user's ID
$sellerId = $_SESSION['user_id'] ?? 0;

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) { 
  $conn = null; // Handle gracefully if DB is not available
}

$userData = null;

// Fetch user data if logged in
if ($conn && $sellerId > 0) {
  $sql = "SELECT fname, lname, email, profile_picture FROM users WHERE id = ? LIMIT 1";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $sellerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = $result->fetch_assoc();
    mysqli_stmt_close($stmt);
  }
}

// Prepare profile picture path
$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture ? "../images/profiles/" . $profilePicture : "../images/default-profile.png"; // fallback image

/* read flash, then clear just that key */
$flash_success = $_SESSION['success'] ?? '';
unset($_SESSION['success']); // keep other session data like 'email'

function displayError($msg) {
    return !empty($msg) ? '<p class="error-message">'.$msg.'</p>' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dropdown.js" defer></script>
    <script src="../js/remove_autofill.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 overflow-y-auto">
    <div class="flex flex-col h-screen">
        <!-- Top Navbar -->
        <?php include '../nav/topbar_buyer.php'; ?>


        <!-- Main Layout -->

        <div class="flex flex-1">
            <!-- Sidebar -->
            <?php include '../nav/sidebar_buyer.php'; ?>

            <!-- Main Content -->
            <main class="ml-16 md:ml-64 p-6 flex-1 overflow-y-auto">
                <!-- Title Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-black">Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your account settings</p>
                </div>

                <!-- Profile Information Card -->
<form method="POST" action="update.php" class="bg-white rounded-lg shadow-sm p-6 mb-6">
  <?php if (isset($_SESSION['profile_success']) || isset($_SESSION['profile_error'])): ?>
    <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
      <?php 
        if (isset($_SESSION['profile_success'])) { echo htmlspecialchars($_SESSION['profile_success']); unset($_SESSION['profile_success']); }
        elseif (isset($_SESSION['profile_error'])) { echo htmlspecialchars($_SESSION['profile_error']); unset($_SESSION['profile_error']); }
      ?>
    </div>
  <?php endif; ?>
    <?php if (isset($_SESSION['password_success']) || isset($_SESSION['password_error'])): ?>
    <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
      <?php 
        if (isset($_SESSION['password_success'])) { echo htmlspecialchars($_SESSION['password_success']); unset($_SESSION['password_success']); }
        elseif (isset($_SESSION['password_error'])) { echo htmlspecialchars($_SESSION['password_error']); unset($_SESSION['password_error']); }
      ?>
    </div>
  <?php endif; ?>
  <h2 class="text-xl font-semibold text-black mb-2">Profile Information</h2>
  <p class="text-gray-600 mb-6">Update your personal information</p>

  <div class="space-y-4">
    <input type="text" name="fname" placeholder="First Name"
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
    <input type="text" name="lname" placeholder="Last Name"
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
    <input type="email" name="current_email" placeholder="Current Email"
           value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>"
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
    <input type="email" name="new_email" placeholder="New Email"
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">

    <button type="submit" name="update_profile"
            class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-300 transition-colors duration-200">
      Confirm
    </button>
  </div>
</form>

    <!-- ✅ ACCOUNT SETTINGS SECTION -->
    
      <form id="pwForm" method="POST" action="update.php" autocomplete="off" class="bg-white rounded-lg shadow-sm p-6">
  <h2 class="text-xl font-semibold text-black mb-2">Account Settings</h2>
  <p class="text-gray-600 mb-6">Manage your account</p>

  <!-- decoys (first in DOM so browsers waste autofill here) -->
  <input type="text" autocomplete="username" class="hidden" tabindex="-1" aria-hidden="true">
  <input type="password" autocomplete="current-password" class="hidden" tabindex="-1" aria-hidden="true">

  <!-- hidden real POST fields -->
  <input type="hidden" name="current_password">
  <input type="hidden" name="new_password">
  <input type="hidden" name="update_password" value="1">

  <div class="space-y-4">
    <h3 class="font-medium text-black">Change Password</h3>

    <!-- Visible inputs (no name), -->
    <input
      data-sync="current_password"
      type="password"
      placeholder="Current Password"
      autocomplete="off"
      readonly
      class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
    >

    <input
      data-sync="new_password"
      type="password"
      placeholder="New Password"
      autocomplete="off"
      readonly
      class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
    >

    <button type="submit"
            class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-300 transition-colors duration-200">
      Change Password
    </button>
  </div>
</form>

        </main>
    </div>
</div>
</body>
</html>
