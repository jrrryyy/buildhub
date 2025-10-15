<div class="sticky top-0 z-[10] bg-white border-b border-gray-200 px-6 py-4 flex justify-end items-center transition-all duration-300 lg:ml-640">

  <div class="relative">
    <button id="profileButton" class="flex items-center space-x-3 focus:outline-none">
      <img src="../images/korina.png" alt="user-image" class="w-10 h-10 rounded-full border border-gray-300" />
      <div class="hidden md:block text-left">
        <h4 class="text-sm font-medium text-gray-800"><?= htmlspecialchars($_SESSION['fname'] ?? 'User') ?></h4>
        <span class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
      </div>
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-60 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
      <div class="px-5 py-4 border-b bg-yellow-400 rounded-t-lg">
        <div class="flex items-center">
          <img src="../images/korina.png" alt="user-image" class="w-10 h-10 rounded-full border border-white" />
          <div class="ml-3">
            <h4 class="text-sm font-semibold text-black"><?= htmlspecialchars($_SESSION['fname'] ?? 'User') ?></h4>
            <span class="text-xs text-gray-800"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
          </div>
        </div>
      </div>

      <div class="py-3">
        <a href="order.php" class="block px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">👤 My Profile</a>
        <a href="../admin/settings.php" class="block px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">🔒 Change Password</a>
        <button onclick="showSupportInfo()" class="w-full text-left px-5 py-2 text-sm text-gray-700 hover:bg-gray-100">🛠 Support</button>
        <div class="border-t my-2"></div>
        <a href="../nav/logout.php" class="block px-5 py-2 text-sm font-medium text-red-600 hover:bg-red-50">🚪 Log Out</a>
      </div>
    </div>
  </div>
</div>
