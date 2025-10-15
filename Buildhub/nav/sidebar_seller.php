<aside class="bg-white border-r border-gray-200 w-16 md:w-64 flex flex-col transition-all duration-300 fixed top-0 left-0 h-screen z-[20] overflow-y-auto">
  <!-- Logo -->
  <div class="p-4 border-b border-gray-200 flex-shrink-0">
    <div class="bg-yellow-400 text-black font-bold px-4 py-2 rounded-xl text-sm md:text-base hidden md:block">BuildHub</div>
  </div>

  <!-- Navigation Items -->
  <nav class="flex-1 p-2 md:p-4 space-y-1 md:space-y-2 mt-4">
    <a href="../admin/index.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium active:bg-yellow-100 active:text-yellow-700">
      <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
      </svg>
      <span class="sidebar-label hidden md:inline">Dashboard</span>
    </a>

    <a href="../admin/orders.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
      <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
      </svg>
      <span class="sidebar-label hidden md:inline">Browse</span>
    </a>

    <!-- ✅ My Orders (restored) -->
    <a href="../admin/orders.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
      <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
      </svg>
      <span class="sidebar-label hidden md:inline">My Orders</span>
    </a>

    <a href="../admin/settings.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
      <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
      </svg>
      <span class="sidebar-label hidden md:inline">Settings</span>
    </a>
  </nav>
</aside>
