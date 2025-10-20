<?php
session_start();
$q = trim($_GET['q'] ?? '');

// Set timezone to Philippines for consistent date display
date_default_timezone_set('Asia/Manila');

/* ---------- DB (optional) ---------- */
$conn = mysqli_connect("localhost","root","","user_db");
if (!$conn) { /* no hard die — page still works without DB */ }

/* ---------- helpers ---------- */
function h($v){ return htmlspecialchars((string)($v ?? ""), ENT_QUOTES, 'UTF-8'); }
function peso($n){ return "₱" . number_format((float)$n, 2); }
function fmtDate($s){ if(!$s) return "—"; $t=strtotime($s); return $t?date("M d, Y",$t):"—"; }
function fmtDateTime($s){ if(!$s) return "—"; $t=strtotime($s); return $t?date("M d, Y \\a\\t h:i A",$t):"—"; }
function badgeClasses($status){
  $s = strtolower($status ?? '');
  if ($s==='completed') return 'bg-green-100 text-green-700';
  if ($s==='accepted')  return 'bg-blue-100 text-blue-700';
  if ($s==='cancelled') return 'bg-red-100 text-red-700';
  return 'bg-yellow-100 text-yellow-800';
}
function renderCard($row){
  $title       = $row['product_name'] ?? 'Untitled';
  // If no explicit description was set, compose one from address + province + phone
  $desc        = $row['description'] ?? (($row['address_line'] ?? '') .
                   (isset($row['province']) && $row['province'] !== null ? ' • '.$row['province'] : '') .
                   (isset($row['phone']) && $row['phone'] !== '' ? ' • '.$row['phone'] : ''));
  $desc        = trim($desc) === '' ? 'No description provided.' : $desc;

  $status      = $row['status']        ?? 'pending';
  $orderedAt   = $row['ordered_at']    ?? null;              // DATETIME
  $scheduledAt = $row['schedule_date'] ?? null;              // DATE (no time)
  $total       = isset($row['total_amount']) ? (float)$row['total_amount'] : 0;
  $id          = $row['id'] ?? '';

  ?>
  <article class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mt-4">
    <div class="flex items-start justify-between">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-lg bg-indigo-50 flex items-center justify-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M12 2l7 4-7 4-7-4 7-4z" stroke-width="1.5"/>
            <path d="M19 8v8l-7 4-7-4V8" stroke-width="1.5"/>
            <path d="M12 12l7-4M12 12L5 8" stroke-width="1.5"/>
          </svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900 leading-tight"><?php echo h($title); ?></h3>
      </div>
      <span class="inline-flex items-center rounded-full <?php echo badgeClasses($status); ?> px-3 py-1 text-xs font-medium">
        <?php echo h(ucfirst($status)); ?>
      </span>
    </div>

    <p class="mt-2 text-sm text-slate-600"><?php echo h($desc); ?></p>

    <div class="mt-4 space-y-2 text-sm text-slate-700">
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9" stroke-width="1.5" fill="none"/>
        </svg>
        <span>Ordered: <?php echo h(fmtDateTime($orderedAt)); ?></span>
      </div>
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M8 2v3M16 2v3M3 9h18M4 7h16a1 1 0 011 1v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1z"/>
        </svg>
        <span>Scheduled: <?php echo h(fmtDate($scheduledAt)); ?></span>
      </div>
    </div>

    <hr class="my-4 border-slate-200"/>
    <div class="flex items-center justify-between">
      <div class="text-xl font-semibold text-blue-600"><?php echo h(peso($total)); ?></div>
      <?php if (strtolower($status)==='cancelled'): ?>
      <form action="delete.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this cancelled order? This action cannot be undone.');">
        <input type="hidden" name="item_id" value="<?php echo h($id); ?>">
        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-100 text-red-600 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" title="Delete this order permanently">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="2" d="M6 6l12 12M18 6l-12 12"/>
          </svg>
        </button>
      </form>
      <?php endif; ?>
    </div>

    <?php if (strtolower($status)==='pending' || strtolower($status)==='accepted'): ?>
    <div class="mt-4 flex flex-wrap gap-2">
      <form action="reschedule.php" method="GET">
        <input type="hidden" name="item_id" value="<?php echo h($id); ?>">
        <button class="inline-flex items-center gap-2 rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.5" d="M8 2v3M16 2v3M3 9h18M4 7h16a1 1 0 011 1v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1z"/>
          </svg>
          Reschedule
        </button>
      </form>
      <form action="cancel.php" method="POST" onsubmit="return confirm('Cancel this order?');">
        <input type="hidden" name="item_id" value="<?php echo h($id); ?>">
        <button class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-width="1.5" d="M6 6l12 12M18 6l-12 12"/>
          </svg>
          Cancel
        </button>
      </form>
    </div>
    <?php endif; ?>
  </article>
  <?php
}

/* ---------- collect DB rows from ORDERS (for BUYER, with search) ---------- */
$buckets = ['Pending'=>[], 'Accepted'=>[], 'Completed'=>[], 'Cancelled'=>[]];

$buyerId = (int)($_SESSION['user_id'] ?? 0);
if ($conn && $buyerId > 0) {

  if ($q === '') {
    // No search — original query
    $sql = "SELECT id, product_name, status, ordered_at, schedule_date,
                   total_amount, address_line, province, phone
            FROM orders
            WHERE buyer_id = ?
            ORDER BY id DESC";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, 'i', $buyerId);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
    }
  } else {
    // With search — filter multiple fields
    $sql = "SELECT id, product_name, status, ordered_at, schedule_date,
                   total_amount, address_line, province, phone
            FROM orders
            WHERE buyer_id = ?
              AND (
                   product_name LIKE ?
                OR status       LIKE ?
                OR phone        LIKE ?
                OR address_line LIKE ?
                OR province     LIKE ?
              )
            ORDER BY id DESC";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      $like = "%{$q}%";
      // 1 int + 5 strings
      mysqli_stmt_bind_param($stmt, 'isssss', $buyerId, $like, $like, $like, $like, $like);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
    }
  }

  if (!empty($res)) {
    while ($r = mysqli_fetch_assoc($res)) {
      // (optional) set a prebuilt description for the card
      $r['description'] = trim(
        ($r['address_line'] ?? '') .
        (isset($r['province']) && $r['province'] !== null ? ' • '.$r['province'] : '') .
        (isset($r['phone'])    && $r['phone']    !== ''   ? ' • '.$r['phone']    : '')
      );

      $s = strtolower($r['status'] ?? 'pending');
      $k = ($s==='accepted' ? 'Accepted' : ($s==='completed' ? 'Completed' : ($s==='cancelled' ? 'Cancelled' : 'Pending')));
      $buckets[$k][] = $r;
    }
    mysqli_stmt_close($stmt);
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">    
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../js/dropdown.js" defer></script>
</head>
<body class="bg-gray-50">
  <?php include '../nav/topbar_buyer.php'; ?>
  <div class="flex">
    <?php include '../nav/sidebar_buyer.php'; ?>

    <main class="pt-16 pl-[320px]">
      <div class="max-w-[1200px] mx-auto px-6 py-6">
        <h1 class="text-3xl font-bold">My Orders</h1>
        <p class="text-gray-500 mt-2">Track and manage your orders</p>

        <form method="GET" class="mt-4">
  <input
    type="search"
    name="q"
    value="<?= htmlspecialchars($q) ?>"
    class="w-full p-2 border border-gray-200 rounded-lg placeholder-gray-400
           focus:outline-none focus:ring-2 focus:ring-yellow-400"
    placeholder="Search orders by product, status, phone, address, province…"
  />
</form>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
          <section>
            <h2 class="text-xl font-bold">Pending Orders</h2>
            <?php if (empty($buckets['Pending'])): ?>
              <p class="text-sm text-slate-500 mt-4">No pending orders.</p>
            <?php else: foreach ($buckets['Pending'] as $row) { renderCard($row); } endif; ?>
          </section>

          <section>
            <h2 class="text-xl font-bold">Accepted Orders</h2>
            <?php if (empty($buckets['Accepted'])): ?>
              <p class="text-sm text-slate-500 mt-4">No accepted orders.</p>
            <?php else: foreach ($buckets['Accepted'] as $row) { renderCard($row); } endif; ?>
          </section>

          <section>
            <h2 class="text-xl font-bold">Completed Orders</h2>
            <?php if (empty($buckets['Completed'])): ?>
              <p class="text-sm text-slate-500 mt-4">No completed orders.</p>
            <?php else: foreach ($buckets['Completed'] as $row) { renderCard($row); } endif; ?>
          </section>

          <section>
            <h2 class="text-xl font-bold">Cancelled Orders</h2>
            <?php if (empty($buckets['Cancelled'])): ?>
              <p class="text-sm text-slate-500 mt-4">No cancelled orders.</p>
            <?php else: foreach ($buckets['Cancelled'] as $row) { renderCard($row); } endif; ?>
          </section>
        </div>
      </div>
    </main>
  </div>

  <!-- Reschedule Modal -->
  <div id="rescheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Reschedule Order</h2>
        <p class="text-slate-600 mb-6">Select a new delivery date for your order.</p>
        
        <div id="modalOrderInfo" class="mb-6 p-4 bg-slate-50 rounded-lg">
          <!-- Order info will be populated by JavaScript -->
        </div>

        <form id="rescheduleForm" class="space-y-4">
          <input type="hidden" id="modalOrderId" name="item_id" value="">
          
          <div>
            <label for="modalScheduleDate" class="block text-sm font-medium text-slate-700 mb-2">
              New Schedule Date
            </label>
            <input
              type="date"
              id="modalScheduleDate"
              name="schedule_date"
              min="<?php echo date('Y-m-d'); ?>"
              class="w-full p-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              required
            >
            <p class="text-xs text-slate-500 mt-1">Select a date from today onwards</p>
          </div>

          <div id="modalError" class="hidden p-3 bg-red-100 border border-red-300 text-red-700 rounded-md">
            <!-- Error messages will be shown here -->
          </div>

          <div class="flex gap-3 pt-4">
            <button
              type="submit"
              class="flex-1 bg-yellow-400 text-white px-4 py-3 rounded-lg font-medium hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2"
            >
              Reschedule Order
            </button>
            <button
              type="button"
              id="closeModal"
              class="flex-1 bg-slate-200 text-slate-700 px-4 py-3 rounded-lg font-medium hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
<script>
  const search = document.querySelector('input[name="q"]');
  if (search) {
    let t;
    search.addEventListener('input', () => {
      clearTimeout(t);
      t = setTimeout(() => search.form.submit(), 350);
    });
  }

  // Modal functionality
  const modal = document.getElementById('rescheduleModal');
  const closeModal = document.getElementById('closeModal');
  const rescheduleForm = document.getElementById('rescheduleForm');
  const modalError = document.getElementById('modalError');
  const modalOrderInfo = document.getElementById('modalOrderInfo');
  const modalOrderId = document.getElementById('modalOrderId');
  const modalScheduleDate = document.getElementById('modalScheduleDate');

  // Close modal when clicking outside or on close button
  closeModal.addEventListener('click', () => {
    modal.classList.add('hidden');
    clearModal();
  });

  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.add('hidden');
      clearModal();
    }
  });

  // Handle reschedule button clicks
  document.addEventListener('click', (e) => {
    if (e.target.closest('form[action="reschedule.php"]')) {
      e.preventDefault();
      const form = e.target.closest('form');
      const orderId = form.querySelector('input[name="item_id"]').value;
      openRescheduleModal(orderId);
    }
  });

  function openRescheduleModal(orderId) {
    // Get order details from the card
    const card = document.querySelector(`input[name="item_id"][value="${orderId}"]`).closest('article');
    const productName = card.querySelector('h3').textContent;
    
    // Find the scheduled date span
    const scheduleSpans = card.querySelectorAll('span');
    let currentSchedule = 'Not scheduled';
    scheduleSpans.forEach(span => {
      if (span.textContent.includes('Scheduled:')) {
        currentSchedule = span.textContent.replace('Scheduled:', '').trim();
      }
    });
    
    const status = card.querySelector('.inline-flex.items-center.rounded-full').textContent.trim();

    // Populate modal
    modalOrderId.value = orderId;
    modalOrderInfo.innerHTML = `
      <h3 class="font-semibold text-slate-900">${productName}</h3>
      <p class="text-sm text-slate-600">Current schedule: ${currentSchedule}</p>
      <p class="text-sm text-slate-600">Status: <span class="font-medium">${status}</span></p>
    `;

    // Reset form
    modalScheduleDate.value = '';
    modalError.classList.add('hidden');
    modalError.textContent = '';

    // Show modal
    modal.classList.remove('hidden');
    modalScheduleDate.focus();
  }

  function clearModal() {
    modalOrderId.value = '';
    modalOrderInfo.innerHTML = '';
    modalScheduleDate.value = '';
    modalError.classList.add('hidden');
    modalError.textContent = '';
  }

  // Handle form submission
  rescheduleForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(rescheduleForm);
    const orderId = formData.get('item_id');
    const scheduleDate = formData.get('schedule_date');

    // Basic validation
    if (!scheduleDate) {
      showModalError('Please select a new schedule date.');
      return;
    }

    const selectedDate = new Date(scheduleDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
      showModalError('Schedule date cannot be in the past.');
      return;
    }

    try {
      // Submit to reschedule.php via AJAX
      const response = await fetch('reschedule.php', {
        method: 'POST',
        body: formData
      });

      const result = await response.json();
      
      if (result.success) {
        // Success - close modal and reload page
        modal.classList.add('hidden');
        window.location.reload();
      } else {
        // Show error from server
        showModalError(result.message || 'Failed to reschedule order. Please try again.');
      }
    } catch (error) {
      showModalError('An error occurred. Please try again.');
    }
  });

  function showModalError(message) {
    modalError.textContent = message;
    modalError.classList.remove('hidden');
  }
</script>

</html>
