<?php
session_start();

/* read flash, then clear just that key */
$flash_success = $_SESSION['success'] ?? '';
unset($_SESSION['success']); // keep other session data like 'email'

function displayError($msg) {
    return !empty($msg) ? '<p class="error-message">'.$msg.'</p>' : '';
}
?>
<?php if (isset($_SESSION['success'])): ?>
   
        <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']); // clear after showing
        ?>
    
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>

        <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']); // clear after showing
        ?>

<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dropdown.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="../js/dropdown.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 overflow-y-auto">
    <div class="flex flex-col h-screen">
        <!-- Top Navbar -->
        <?php include '../nav/topbar_seller.php'; ?>


        <!-- Main Layout -->

        <div class="flex flex-1">
            <!-- Sidebar -->
            <?php include '../nav/sidebar_seller.php'; ?>

            <!-- Main Content -->
            <main class="ml-16 md:ml-64 p-6 flex-1 overflow-y-auto">
                <!-- Title Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-black">Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your account settings</p>
                </div>

                <!-- Profile Information Card -->
<form method="POST" action="update.php">
    <!-- ✅ PERSONAL INFORMATION SECTION -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <!-- 🔹 Personal Info Message -->
        <?php if (isset($_SESSION['profile_success']) || isset($_SESSION['profile_error'])): ?>
    <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
        <?php 
            if (isset($_SESSION['profile_success'])) {
                echo htmlspecialchars($_SESSION['profile_success']);
                unset($_SESSION['profile_success']);
            } elseif (isset($_SESSION['profile_error'])) {
                echo htmlspecialchars($_SESSION['profile_error']);
                unset($_SESSION['profile_error']);
            }
        ?>
    </div>
<?php endif; ?>
        <?php if (isset($_SESSION['password_success']) || isset($_SESSION['password_error'])): ?>
    <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
        <?php 
            if (isset($_SESSION['password_success'])) {
                echo htmlspecialchars($_SESSION['password_success']);
                unset($_SESSION['password_success']);
            } elseif (isset($_SESSION['password_error'])) {
                echo htmlspecialchars($_SESSION['password_error']);
                unset($_SESSION['password_error']);
            }
        ?>
    </div>
<?php endif; ?>

        <h2 class="text-xl font-semibold text-black mb-2">Profile Information</h2>
        <p class="text-gray-600 mb-6">Update your personal information</p>
        <div class="space-y-4">
            <input type="text" name="fname" placeholder="First Name" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
            <input type="text" name="lname" placeholder="Last Name" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
            <input type="email" name="current_email" placeholder="Current Email" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
            <input type="email" name="new_email" placeholder="New Email" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
            <button name="update_profile" 
                    class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-300 transition-colors duration-200">
                Confirm
            </button>
        </div>
    </div>

    <!-- ✅ ACCOUNT SETTINGS SECTION -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <!-- 🔹 Password Message -->



        <h2 class="text-xl font-semibold text-black mb-2">Account Settings</h2>
        <p class="text-gray-600 mb-6">Manage your account</p>
            <div class="space-y-4">
                <h3 class="font-medium text-black">Change Password</h3>
                <input type="password" name="current_password" placeholder="Current Password" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                <input type="password" name="new_password" placeholder="New Password" 
                   class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-400">
                <button name="update_password" oncli
                    class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-300 transition-colors duration-200">
                Change Password
                </button>
            </div>
        </div>
    </form>

        </main>
    </div>
</div>
</body>
</html>
