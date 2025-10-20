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
function peso($n){ return "₱" . number_format((float)$n, 2); }
function fmtDate($s){ if(!$s) return "—"; $t=strtotime($s); return $t?date("M d, Y",$t):"—"; }

// Get dashboard statistics
$stats = [
  'deliveries_this_week' => 0,
  'completed_this_month' => 0,
  'total_orders' => 0,
  'pending_orders' => 0
];

if ($conn && $buyerId > 0) {
  // Deliveries this week (orders scheduled for this week)
  $weekStart = date('Y-m-d', strtotime('monday this week'));
  $weekEnd = date('Y-m-d', strtotime('sunday this week'));
  $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ? AND schedule_date BETWEEN ? AND ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'iss', $buyerId, $weekStart, $weekEnd);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    $stats['deliveries_this_week'] = $row['count'] ?? 0;
    mysqli_stmt_close($stmt);
  }

  // Completed this month
  $monthStart = date('Y-m-01');
  $monthEnd = date('Y-m-t');
  $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ? AND status = 'completed' AND schedule_date BETWEEN ? AND ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'iss', $buyerId, $monthStart, $monthEnd);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    $stats['completed_this_month'] = $row['count'] ?? 0;
    mysqli_stmt_close($stmt);
  }

  // Total orders
  $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ?";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $buyerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    $stats['total_orders'] = $row['count'] ?? 0;
    mysqli_stmt_close($stmt);
  }

  // Pending orders
  $sql = "SELECT COUNT(*) as count FROM orders WHERE buyer_id = ? AND status = 'pending'";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $buyerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result->fetch_assoc();
    $stats['pending_orders'] = $row['count'] ?? 0;
    mysqli_stmt_close($stmt);
  }
}

// Get recent orders for the order summary
$recentOrders = [];
if ($conn && $buyerId > 0) {
  $sql = "SELECT id, product_name, status, schedule_date, total_amount, ordered_at 
          FROM orders 
          WHERE buyer_id = ? 
          ORDER BY ordered_at DESC 
          LIMIT 5";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $buyerId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = $result->fetch_assoc()) {
      $recentOrders[] = $row;
    }
    mysqli_stmt_close($stmt);
  }
}

// Get orders for calendar (current month)
$calendarOrders = [];
$currentMonth = date('Y-m');
if ($conn && $buyerId > 0) {
  $sql = "SELECT DAY(schedule_date) as day, COUNT(*) as count, GROUP_CONCAT(product_name) as products
          FROM orders 
          WHERE buyer_id = ? AND DATE_FORMAT(schedule_date, '%Y-%m') = ?
          GROUP BY DAY(schedule_date)";
  if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'is', $buyerId, $currentMonth);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = $result->fetch_assoc()) {
      $calendarOrders[$row['day']] = [
        'count' => $row['count'],
        'products' => explode(',', $row['products'])
      ];
    }
    mysqli_stmt_close($stmt);
  }
}

// Get current month info
$currentDate = new DateTime();
$currentMonthName = $currentDate->format('F Y');
$currentMonthNum = $currentDate->format('n');
$currentYear = $currentDate->format('Y');
$daysInMonth = $currentDate->format('t');
$firstDayOfWeek = $currentDate->format('w'); // 0 = Sunday, 1 = Monday, etc.

// Adjust first day to start from Monday (1) instead of Sunday (0)
$firstDayOfWeek = ($firstDayOfWeek == 0) ? 6 : $firstDayOfWeek - 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dropdown.js" defer></script>
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
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <?php include '../nav/sidebar_buyer.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 ml-16 md:ml-64 p-6 overflow-auto">
            <!-- Top Header -->
            <?php include '../nav/topbar_buyer.php'; ?>

            <!-- Welcome Message -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-2">Welcome back! Here's an overview of your orders and deliveries.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Deliveries This Week</h3>
                            <p class="text-2xl font-bold text-gray-900"><?php echo h($stats['deliveries_this_week']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Completed This Month</h3>
                            <p class="text-2xl font-bold text-gray-900"><?php echo h($stats['completed_this_month']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Total Orders</h3>
                            <p class="text-2xl font-bold text-gray-900"><?php echo h($stats['total_orders']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500">Pending Orders</h3>
                            <p class="text-2xl font-bold text-gray-900"><?php echo h($stats['pending_orders']); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Grid: Calendar left, Orders right -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Calendar Section -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-700">Delivery Calendar</h2>
                    </div>
                    
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-700"><?php echo h($currentMonthName); ?></h3>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                        <!-- Days Header -->
                        <div class="font-semibold text-gray-500 py-2">M</div>
                        <div class="font-semibold text-gray-500 py-2">T</div>
                        <div class="font-semibold text-gray-500 py-2">W</div>
                        <div class="font-semibold text-gray-500 py-2">T</div>
                        <div class="font-semibold text-gray-500 py-2">F</div>
                        <div class="font-semibold text-gray-500 py-2">S</div>
                        <div class="font-semibold text-gray-500 py-2">S</div>
                        
                        <!-- Empty cells for days before the first day of the month -->
                        <?php for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
                            <div class="p-2"></div>
                        <?php endfor; ?>
                        
                        <!-- Days of the month -->
                        <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
                            <?php 
                            $hasOrders = isset($calendarOrders[$day]);
                            $orderCount = $hasOrders ? $calendarOrders[$day]['count'] : 0;
                            $isToday = $day == date('j');
                            ?>
                            <div class="relative">
                                <button class="w-full p-2 hover:bg-gray-100 rounded text-gray-700 border border-transparent hover:border-gray-300 transition-colors <?php echo $isToday ? 'bg-blue-100 text-blue-700 font-semibold' : ''; ?>" 
                                        onclick="showDayDetails(<?php echo $day; ?>, <?php echo $orderCount; ?>)">
                                    <?php echo $day; ?>
                                </button>
                                <?php if ($hasOrders): ?>
                                    <div class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full"></div>
                                <?php endif; ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Deliveries this month:</span>
                            <span class="font-semibold text-gray-900"><?php echo array_sum(array_column($calendarOrders, 'count')); ?></span>
                        </div>
                        <div class="flex items-center mt-2 text-xs text-gray-500">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                            <span>Days with scheduled deliveries</span>
                        </div>
                    </div>
                </div>

                <!-- Order Summary Section -->
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <h2 class="text-xl font-semibold text-gray-700">Recent Orders</h2>
                        </div>
                        <a href="myorders.php" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php if (empty($recentOrders)): ?>
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <p class="text-gray-500">No orders yet</p>
                                <a href="orders.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Browse Products</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <?php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'accepted' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800'
                                ];
                                $statusClass = $statusColors[strtolower($order['status'])] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <div class="flex justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-lg hover:shadow-sm transition-shadow cursor-pointer" 
                                     onclick="window.location.href='myorders.php'">
                                    <div class="flex-1">
                                        <h4 class="text-gray-900 font-medium"><?php echo h($order['product_name']); ?></h4>
                                        <p class="text-sm text-gray-500">Scheduled: <?php echo h(fmtDate($order['schedule_date'])); ?></p>
                                        <p class="text-sm text-gray-500">Ordered: <?php echo h(fmtDate($order['ordered_at'])); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold <?php echo $statusClass; ?>">
                                            <?php echo h(ucfirst($order['status'])); ?>
                                        </span>
                                        <p class="text-sm font-semibold text-gray-900 mt-1"><?php echo h(peso($order['total_amount'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h2 class="text-xl font-semibold text-gray-700 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="orders.php" class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                        <div class="p-2 bg-blue-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-blue-900">Browse Products</h3>
                            <p class="text-sm text-blue-600">Find and order construction materials</p>
                        </div>
                    </a>
                    
                    <a href="myorders.php" class="flex items-center p-4 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 transition-colors">
                        <div class="p-2 bg-green-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-green-900">My Orders</h3>
                            <p class="text-sm text-green-600">Track and manage your orders</p>
                        </div>
                    </a>
                    
                    <a href="settings.php" class="flex items-center p-4 bg-purple-50 border border-purple-200 rounded-lg hover:bg-purple-100 transition-colors">
                        <div class="p-2 bg-purple-100 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-medium text-purple-900">Settings</h3>
                            <p class="text-sm text-purple-600">Manage your account preferences</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Details Modal -->
    <div id="dayModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Delivery Details</h3>
                <div id="dayDetails" class="space-y-3">
                    <!-- Day details will be populated here -->
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeDayModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showDayDetails(day, orderCount) {
            const modal = document.getElementById('dayModal');
            const details = document.getElementById('dayDetails');
            
            if (orderCount > 0) {
                details.innerHTML = `
                    <p class="text-gray-600">You have <strong>${orderCount}</strong> delivery${orderCount > 1 ? 'ies' : 'y'} scheduled for ${day}.</p>
                    <div class="mt-4">
                        <a href="myorders.php" class="inline-flex items-center px-4 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            View Orders
                        </a>
                    </div>
                `;
            } else {
                details.innerHTML = `
                    <p class="text-gray-600">No deliveries scheduled for ${day}.</p>
                    <div class="mt-4">
                        <a href="orders.php" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Browse Products
                        </a>
                    </div>
                `;
            }
            
            modal.classList.remove('hidden');
        }

        function closeDayModal() {
            document.getElementById('dayModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('dayModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDayModal();
            }
        });
    </script>
</body>
</html>