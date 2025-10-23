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
    <div class="text-xl font-semibold text-blue-600"><?php echo h(peso($total)); ?></div>

    <!-- Order actions -->
    <?php if (strtolower($status)==='pending'): ?>
  <div class="mt-4 flex flex-wrap gap-2">
    <!-- Accept -->
    <form action="accept_order.php" method="POST"
          onsubmit="return confirm('Accept this order?');">
      <input type="hidden" name="order_id" value="<?php echo h($id); ?>">
      <button class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-green-700 border border-green-300 bg-green-50 hover:bg-green-100">
        <!-- check icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M5 13l4 4L19 7"/>
        </svg>
        Accept
      </button>
    </form>

    <!-- (existing) Reschedule -->


    <!-- Cancel -->
    <form action="cancel.php" method="POST" onsubmit="return confirm('Cancel this order?');">
      <input type="hidden" name="order_id" value="<?php echo h($id); ?>">
      <button class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-red-600 border border-red-300 bg-red-50 hover:bg-red-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M6 6l12 12M18 6l-12 12"/>
        </svg>
        Cancel
      </button>
    </form>
  </div>
<?php endif; ?>

<?php if (strtolower($status) === 'accepted'): ?>
  <div class="mt-4 flex flex-wrap gap-2">
    <form action="complete_order.php" method="POST"
          onsubmit="return confirm('Mark this order as completed?');">
      <input type="hidden" name="order_id" value="<?php echo h($id); ?>">
      <button class="inline-flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium text-blue-700 border border-blue-300 bg-blue-50 hover:bg-blue-100">
        <!-- check-double icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M5 13l4 4L19 7M3 12l4 4L17 6"/>
        </svg>
        Mark Completed
      </button>
    </form>
  </div>
<?php endif; ?>


  </article>
  <?php
}

/* ---------- collect DB rows from ORDERS (for SELLER) ---------- */
$buckets = ['Pending'=>[], 'Accepted'=>[], 'Completed'=>[], 'Cancelled'=>[]];

$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($conn && $sellerId > 0) {

  if ($q === '') {
    // No search term: simple query
    $sql = "SELECT o.id, o.product_name, o.status, o.ordered_at, o.schedule_date,
                   o.total_amount, o.address_line, o.province, o.phone,
                   o.buyer_id,
                   TRIM(CONCAT(COALESCE(u.fname,''),' ',COALESCE(u.lname,''))) AS buyer_name
            FROM orders o
            LEFT JOIN users u ON u.id = o.buyer_id
            WHERE o.supplier_id = ?
            ORDER BY o.id DESC";

    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, 'i', $sellerId);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
    }

  } else {
    // With search term: filter multiple fields
    $sql = "SELECT o.id, o.product_name, o.status, o.ordered_at, o.schedule_date,
                   o.total_amount, o.address_line, o.province, o.phone,
                   o.buyer_id,
                   TRIM(CONCAT(COALESCE(u.fname,''),' ',COALESCE(u.lname,''))) AS buyer_name
            FROM orders o
            LEFT JOIN users u ON u.id = o.buyer_id
            WHERE o.supplier_id = ?
              AND (
                    o.product_name LIKE ?
                 OR o.status       LIKE ?
                 OR TRIM(CONCAT(COALESCE(u.fname,''),' ',COALESCE(u.lname,''))) LIKE ?
                 OR o.phone        LIKE ?
                 OR o.address_line LIKE ?
                 OR o.province     LIKE ?
              )
            ORDER BY o.id DESC";

    if ($stmt = mysqli_prepare($conn, $sql)) {
      $like = "%{$q}%";
      // 1 int + 6 strings  -> "issssss"
      mysqli_stmt_bind_param($stmt, 'issssss',
        $sellerId, $like, $like, $like, $like, $like, $like
      );
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
    }
  }

  if (!empty($res)) {
    while ($r = mysqli_fetch_assoc($res)) {
      // Optional: richer description (who ordered + where)
      $r['description'] = trim(
        ($r['buyer_name'] ? "Buyer: {$r['buyer_name']}" : '') .
        (($r['address_line'] ?? '') !== '' ? ' • '.$r['address_line'] : '') .
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
$userData = null;
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

$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture ? "../images/profiles/" . $profilePicture : null;
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
  <?php include '../nav/topbar_seller.php'; ?>
  <div class="flex">
    <?php include '../nav/sidebar_seller.php'; ?>

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
    placeholder="Search orders by product, buyer name, status, phone, address…"
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
</body>
<script>
  const search = document.querySelector('input[name="q"]');
if (search) {
  let timer;
  search.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(async () => {
      const query = search.value.trim();
      const url = new URL(window.location.href);
      url.searchParams.set('q', query);

      // fetch partial results via AJAX, no full reload
      const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' } });
      const text = await res.text();

      // extract only the order grid from response and replace the old one
      const parser = new DOMParser();
      const doc = parser.parseFromString(text, 'text/html');
      const newGrid = doc.querySelector('.grid');
      const oldGrid = document.querySelector('.grid');
      if (newGrid && oldGrid) oldGrid.replaceWith(newGrid);
    }, 400);
  });
}

</script>

</html>
