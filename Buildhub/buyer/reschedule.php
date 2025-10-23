<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Set timezone to Philippines for consistent date handling
date_default_timezone_set('Asia/Manila');

$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { 
  http_response_code(500); 
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => false, 'message' => 'Database connection error']);
  } else {
    echo 'DB error';
  }
  exit; 
}

$buyerId = (int)($_SESSION['user_id'] ?? 0);
if ($buyerId <= 0) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => false, 'message' => 'Not authorized']);
  } else {
    $_SESSION['flash_error'] = 'Not authorized.';
    header('Location: myorders.php');
  }
  exit;
}

// Handle both GET (old page) and POST (AJAX) requests
$orderId = (int)(($_GET['item_id'] ?? $_POST['item_id']) ?? 0);
if ($orderId <= 0) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
  } else {
    $_SESSION['flash_error'] = 'Invalid order ID.';
    header('Location: myorders.php');
  }
  exit;
}

/* 1) Verify the order belongs to this buyer and can be rescheduled */
$check = $conn->prepare("SELECT id, status, schedule_date, product_name FROM orders WHERE id = ? AND buyer_id = ? LIMIT 1");
$check->bind_param('ii', $orderId, $buyerId);
$check->execute();
$res = $check->get_result();
$order = $res->fetch_assoc();
$check->close();

if (!$order) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => false, 'message' => 'Order not found or you do not have permission to reschedule it']);
  } else {
    $_SESSION['flash_error'] = 'Order not found or you do not have permission to reschedule it.';
    header('Location: myorders.php');
  }
  exit;
}

$currentStatus = strtolower($order['status']);
if (!in_array($currentStatus, ['pending', 'accepted'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only pending or accepted orders can be rescheduled']);
  } else {
    $_SESSION['flash_error'] = 'Only pending or accepted orders can be rescheduled.';
    header('Location: myorders.php');
  }
  exit;
}

/* Handle form submission */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newScheduleDate = $_POST['schedule_date'] ?? '';
  
  if (empty($newScheduleDate)) {
    echo json_encode(['success' => false, 'message' => 'Please select a new schedule date']);
    exit;
  }
  
  // Validate date format and ensure it's not in the past
  $newDate = strtotime($newScheduleDate);
  $today = strtotime('today');
  
  if ($newDate === false) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    exit;
  }
  
  if ($newDate < $today) {
    echo json_encode(['success' => false, 'message' => 'Schedule date cannot be in the past']);
    exit;
  }
  

/* Update the order schedule date AND reset status to pending */
$update = $conn->prepare("UPDATE orders SET schedule_date = ?, status = 'pending' WHERE id = ? AND buyer_id = ?");
$update->bind_param('sii', $newScheduleDate, $orderId, $buyerId);
$update->execute();
$affected = $update->affected_rows;
$update->close();

  
  if ($affected > 0) {
    echo json_encode(['success' => true, 'message' => 'Order rescheduled successfully to ' . date('M d, Y', $newDate)]);
  } else {
    echo json_encode(['success' => false, 'message' => 'Could not reschedule order. Please try again']);
  }
  exit;
}

/* Display reschedule form */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reschedule Order</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
  <?php include '../nav/topbar_buyer.php'; ?>
  <div class="flex">
    <?php include '../nav/sidebar_buyer.php'; ?>

    <main class="pt-16 pl-[320px]">
      <div class="max-w-[600px] mx-auto px-6 py-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
          <h1 class="text-2xl font-bold text-slate-900 mb-2">Reschedule Order</h1>
          <p class="text-slate-600 mb-6">Select a new delivery date for your order.</p>
          
          <div class="mb-6 p-4 bg-slate-50 rounded-lg">
            <h3 class="font-semibold text-slate-900"><?php echo htmlspecialchars($order['product_name']); ?></h3>
            <p class="text-sm text-slate-600">Current schedule: <?php echo date('M d, Y', strtotime($order['schedule_date'])); ?></p>
            <p class="text-sm text-slate-600">Status: <span class="font-medium"><?php echo ucfirst($order['status']); ?></span></p>
          </div>

          <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
              <?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
            </div>
          <?php endif; ?>

          <form method="POST" class="space-y-4">
            <div>
              <label for="schedule_date" class="block text-sm font-medium text-slate-700 mb-2">
                New Schedule Date
              </label>
              <input
                type="date"
                id="schedule_date"
                name="schedule_date"
                min="<?php echo date('Y-m-d'); ?>"
                class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              >
              <p class="text-xs text-slate-500 mt-1">Select a date from today onwards</p>
            </div>

            <div class="flex gap-3 pt-4">
              <button
                type="submit"
                class="flex-1 bg-yellow-400 text-white px-4 py-3 rounded-lg font-medium hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
              >
                Reschedule Order
              </button>
              <a
                href="myorders.php"
                class="flex-1 bg-slate-200 text-slate-700 px-4 py-3 rounded-lg font-medium hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 text-center"
              >
                Cancel
              </a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
