<?php
session_start();

// Set timezone to Philippines for consistent date handling
date_default_timezone_set('Asia/Manila');

// Database connection
$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { 
  $conn = null; // Handle gracefully if DB is not available
}

$buyerId = (int)($_SESSION['user_id'] ?? 0);

// Helper functions
function h($v){ return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, 'UTF-8'); }

// Get user data
$userData = null;
if ($conn && $buyerId > 0) {
  $sql = "SELECT fname, lname, email, profile_picture FROM users WHERE id = ? LIMIT 1";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $buyerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $userData = $result->fetch_assoc();
    mysqli_stmt_close($stmt);
  }
}

$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture
  ? "../images/profiles/" . $profilePicture
  : "../images/default-icon.png"; // fallback image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dropdown.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .profile-picture {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-gray-50 overflow-y-auto">
    <div class="flex flex-col h-screen">
        <!-- Top Navbar -->
        <?php include '../nav/topbar_admin.php'; ?>

        <!-- Main Layout -->
        <div class="flex flex-1">
            <!-- Sidebar -->
            <?php include '../nav/sidebar_admin.php'; ?>

            <!-- Main Content -->
            <main class="ml-16 md:ml-64 p-6 flex-1 overflow-y-auto">
                <!-- Title Section -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-black">Profile</h1>
                    <p class="text-gray-600 mt-1">Manage your profile information and picture</p>
                </div>

                <!-- Profile Picture Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-xl font-semibold text-black mb-4">Profile Picture</h2>
                    
                    <div class="flex items-center space-x-6">
                        <!-- Current Profile Picture -->
                        <div class="relative">
                            <?php if ($profilePicturePath && file_exists($profilePicturePath)): ?>
                                <img src="<?php echo h($profilePicturePath); ?>" 
                                     alt="Profile Picture" 
                                     class="profile-picture rounded-full border-4 border-gray-200">
                            <?php else: ?>
                                <div class="profile-picture rounded-full border-4 border-gray-200 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Upload Button Overlay -->
                            <form id="profilePictureForm" method="POST" action="update_profile.php" enctype="multipart/form-data" class="absolute bottom-0 right-0">
                                <input type="file" 
                                       id="profilePictureInput" 
                                       name="profile_picture" 
                                       accept="image/*" 
                                       class="hidden"
                                       onchange="uploadProfilePicture()">
                                <button type="button" 
                                        onclick="document.getElementById('profilePictureInput').click()"
                                        class="bg-yellow-400 text-black p-2 rounded-full hover:bg-yellow-500 transition-colors duration-200 shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Profile Picture Info -->
                        <div>
                            <h3 class="font-medium text-gray-900"><?php echo h(($userData['fname'] ?? '') . ' ' . ($userData['lname'] ?? '')); ?></h3>
                            <p class="text-sm text-gray-500"><?php echo h($userData['email'] ?? ''); ?></p>
                            <p class="text-xs text-gray-400 mt-1">Click the camera icon to upload a new profile picture</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Information Card -->
                <form method="POST" action="update_profile.php" class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <?php if (isset($_SESSION['profile_success']) || isset($_SESSION['profile_error'])): ?>
                        <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
                            <?php 
                                if (isset($_SESSION['profile_success'])) { echo h($_SESSION['profile_success']); unset($_SESSION['profile_success']); }
                                elseif (isset($_SESSION['profile_error'])) { echo h($_SESSION['profile_error']); unset($_SESSION['profile_error']); }
                            ?>
                        </div>
                    <?php endif; ?>
                         <?php if (isset($_SESSION['password_success']) || isset($_SESSION['password_error'])): ?>
                        <div class="w-full bg-yellow-300 text-black font-medium p-3 mb-5 rounded-lg text-center">
                            <?php 
                                if (isset($_SESSION['password_success'])) { echo h($_SESSION['password_success']); unset($_SESSION['password_success']); }
                                elseif (isset($_SESSION['password_error'])) { echo h($_SESSION['password_error']); unset($_SESSION['password_error']); }
                            ?>
                        </div>
                    <?php endif; ?>
                    <h2 class="text-xl font-semibold text-black mb-2">Profile Information</h2>
                    <p class="text-gray-600 mb-6">Update your personal information</p>

                    <div class="space-y-4">
                        <input type="text" 
                               name="fname" 
                               placeholder="First Name"
                               value="<?php echo h($userData['fname'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        
                        <input type="text" 
                               name="lname" 
                               placeholder="Last Name"
                               value="<?php echo h($userData['lname'] ?? ''); ?>"
                               class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        
                        <input type="email" 
                               name="current_email" 
                               placeholder="Current Email"
                               value="<?php echo h($userData['email'] ?? ''); ?>"
                               readonly
                               class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-md text-gray-600">
                        
                        <input type="email" 
                               name="new_email" 
                               placeholder="New Email (optional)"
                               class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">

                        <button type="submit" 
                                name="update_profile"
                                class="bg-yellow-400 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-300 transition-colors duration-200">
                            Update Profile
                        </button>
                    </div>
                </form>

                <!-- Account Settings Section -->
                <form id="pwForm" method="POST" action="update_profile.php" autocomplete="off" class="bg-white rounded-lg shadow-sm p-6">

                    <h2 class="text-xl font-semibold text-black mb-2">Account Settings</h2>
                    <p class="text-gray-600 mb-6">Manage your account security</p>

                    <!-- Hidden real POST fields -->
                    <input type="hidden" name="current_password">
                    <input type="hidden" name="new_password">
                    <input type="hidden" name="update_password" value="1">

                    <div class="space-y-4">
                        <h3 class="font-medium text-black">Change Password</h3>

                        <!-- Visible inputs (no name) -->
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

    <script>
        // Profile picture upload functionality
        function uploadProfilePicture() {
            const form = document.getElementById('profilePictureForm');
            const formData = new FormData(form);
            
            // Show loading state
            const button = form.querySelector('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            button.disabled = true;

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show new profile picture
                    window.location.reload();
                } else {
                    alert('Error uploading profile picture: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error uploading profile picture');
            })
            .finally(() => {
                button.innerHTML = originalContent;
                button.disabled = false;
            });
        }

        // Password form sync functionality
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pwForm');
            const inputs = form.querySelectorAll('[data-sync]');
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const targetName = this.getAttribute('data-sync');
                    const targetInput = form.querySelector(`input[name="${targetName}"]`);
                    if (targetInput) {
                        targetInput.value = this.value;
                    }
                });
                
                // Remove readonly on focus
                input.addEventListener('focus', function() {
                    this.removeAttribute('readonly');
                });
            });
        });
    </script>
</body>
</html>
