<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/24/outline/index.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        /* Heroicons are SVGs, so we'll inline them for simplicity */
        .heroicon { display: inline-block; width: 1.25rem; height: 1.25rem; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <aside class="bg-white border-r border-gray-200 w-16 md:w-64 flex flex-col transition-all duration-300 fixed md:relative h-full z-10 overflow-hidden">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-200 flex-shrink-0">
                <div class="bg-yellow-400 text-black font-bold px-4 py-2 rounded-xl text-sm md:text-base hidden md:block">BuildHub</div>
                <div class="bg-yellow-400 text-black font-bold w-8 h-8 rounded flex items-center justify-center md:hidden">BH</div>
            </div>
            
            <!-- Navigation Items -->
              <nav classn="flex-1 p-2 md:p-4 space-y-1 md:space-y-2 mt-4">
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
                <a href="../admin/testing.php" class="sidebar-item w-full flex items-center space-x-3 px-3 py-2 rounded-xl text-gray-700 hover:bg-gray-100 transition-all duration-200 font-medium">
                    <svg class="heroicon w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span class="sidebar-label hidden md:inline">testing</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-16 md:ml-64 p-6 overflow-auto">
            <!-- Top Header -->
            <header class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                 <div class="bg-black text-white font-medium text-sm rounded-[10px] px-4 py-2 inline-block break-words max-w-full">
                <?php echo $_SESSION['email']; ?>
            </div>
            </header>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Deliveries This Week</h2>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Completed This Month</h2>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                </div>
            </div>

            <!-- Main Grid: Calendar left, Orders right -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Calendar Section -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <h2 class="text-xl font-semibold text-gray-700">Calendar</h2>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <button class="text-gray-500 hover:text-gray-700 text-xl px-2 py-1 rounded hover:bg-gray-100 transition-colors" onclick="prevMonth()">‹</button>
                        <h3 class="text-lg font-semibold text-gray-700">October 2023</h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl px-2 py-1 rounded hover:bg-gray-100 transition-colors" onclick="nextMonth()">›</button>
                    </div>
                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                        <!-- Days Header -->
                        <div class="font-semibold text-gray-500 py-2">S</div>
                        <div class="font-semibold text-gray-500 py-2">M</div>
                        <div class="font-semibold text-gray-500 py-2">T</div>
                        <div class="font-semibold text-gray-500 py-2">W</div>
                        <div class="font-semibold text-gray-500 py-2">T</div>
                        <div class="font-semibold text-gray-500 py-2">F</div>
                        <div class="font-semibold text-gray-500 py-2">S</div>
                        <!-- Days (October 2023 starts on Sunday) -->
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(1)">1</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(2)">2</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(3)">3</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(4)">4</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(5)">5</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(6)">6</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(7)">7</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(8)">8</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(9)">9</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(10)">10</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(11)">11</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(12)">12</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(13)">13</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(14)">14</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(15)">15</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(16)">16</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(17)">17</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(18)">18</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(19)">19</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(20)">20</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(21)">21</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(22)">22</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(23)">23</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(24)">24</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(25)">25</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(26)">26</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(27)">27</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(28)">28</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(29)">29</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(30)">30</button>
                        <button class="p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors" onclick="selectDay(31)">31</button>
                        <!-- Empty cells -->
                        <div class="p-2"></div><div class="p-2"></div><div class="p-2"></div><div class="p-2"></div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <p class="text-gray-600">Deliveries this month: <span class="font-semibold">0</span></p>
                    </div>
                </div>

                <!-- Order Summary Section -->
                <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        <h2 class="text-xl font-semibold text-gray-700">Order Summary</h2>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded-xl hover:shadow-sm transition-shadow cursor-pointer" title="View Details">
                            <span class="text-gray-700 font-medium">Concrete Order</span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                        </li>
                        <li class="flex justify-between items-center p-3 bg-white border border-gray-200 rounded