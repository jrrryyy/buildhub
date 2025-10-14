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
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 overflow-y-auto">
    <div class="flex flex-col h-screen">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex justify-end items-center relative">
  <!-- Profile Dropdown -->
  <div class="relative">
    <!-- Profile Button -->
    <button id="profileButton" class="flex items-center space-x-3 focus:outline-none">
        <!-- User Image and Info -->
      <img src="../images/korina.png" alt="user-image" class="w-10 h-10 rounded-full border border-gray-300" />
      <div class="hidden md:block text-left">
        <h4 class="text-sm font-medium text-gray-800">Administrator</h4>
        <span class="text-xs text-gray-500"></span>
      </div>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-60 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
      <div class="px-5 py-4 border-b bg-yellow-400 rounded-t-lg">
        <div class="flex items-center">
            <!-- User Image and Info -->
          <img src="../images/korina.png" alt="user-image" class="w-10 h-10 rounded-full border border-white" />
          <div class="ml-3">
            <h4 class="text-sm font-semibold text-black">Administrator</h4>
            <span class="text-xs text-gray-800"> <?php echo $_SESSION['email']; ?></span>
          </div>
        </div>
      </div>
      <div class="py-3">
        <a href="order.php" class="block px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">👤 My Profile</a>
        <a href="../admin/settings.php" class="block px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">🔒 Change Password</a>
        <button onclick="showSupportInfo()" class="w-full text-left px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">🛠 Support</button>
        <div class="border-t my-2"></div>
        <a href="../admin/logout.php" class="block px-5 py-2 text-sm font-medium text-red-600 hover:bg-red-50">🚪 Log Out</a>
      </div>
    </div>
  </div>
</header>


        <!-- Main Layout -->

        <div class="flex flex-1">
            <!-- Sidebar -->
            <aside class="bg-white border-r border-gray-200 w-16 md:w-64 flex flex-col transition-all duration-300 fixed top-0 left-0 h-screen z-10 overflow-y-auto">

            <!-- Logo -->
            <div class="p-4 border-b border-gray-200 flex-shrink-0">
                <div class="bg-yellow-400 text-black font-bold px-4 py-2 rounded-xl text-sm md:text-base hidden md:block">BuildHub</div>
                
            </div>
            
            <!-- Navigation Items -->
            <nav class="flex-1 p-2 md:p-4 space-y-1 md:space-y-2 mt-4">
                <a href="../admin/index.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium active:bg-yellow-100 active:text-yellow-700">
                    <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="sidebar-label hidden md:inline">Dashboard</span>
                </a>
                <a href="../admin/orders.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
                    <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="sidebar-label hidden md:inline">Orders</span>
                </a>
                <a href="../admin/inventory.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
                    <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <span class="sidebar-label hidden md:inline">Inventory</span>
                </a>
                <a href="../admin/settings.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
                    <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="sidebar-label hidden md:inline">Settings</span>
                </a>
            </nav>
        </aside>

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
