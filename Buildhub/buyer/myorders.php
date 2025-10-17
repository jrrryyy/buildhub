<?php
session_start();

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
  if ($s==='completed') return 'bg-blue-100 text-blue-700';
  if ($s==='accepted')  return 'bg-green-100 text-green-700';
  if ($s==='cancelled') return 'bg-red-100 text-red-700';
  return 'bg-yellow-100 text-yellow-800';
}
function renderCard($row){
  $title       = $row['product_name'] ?? 'Untitled';
  $desc        = $row['description']   ?? ($row['desc'] ?? 'No description provided.');
  $status      = $row['status']        ?? 'Pending';
  $orderedAt   = $row['ordered_at']    ?? null;
  $scheduledAt = $row['scheduled_at']  ?? null;
  $qty         = isset($row['quantity'])   ? (float)$row['quantity']   : null;
  $unit        = isset($row['unit_price']) ? (float)$row['unit_price'] : (isset($row['price']) ? (float)$row['price'] : null);
  $total       = isset($row['total'])      ? (float)$row['total']      : (isset($row['grand']) ? (float)$row['grand'] : null);
  if ($total === null) { if ($qty!==null && $unit!==null) $total = $qty*$unit; elseif ($unit!==null) $total = $unit; else $total = 0; }
  $id = $row['id'] ?? '';

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
        <?php echo h($status); ?>
      </span>
    </div>

    <p class="mt-2 text-sm text-slate-600"><?php echo h($desc); ?></p>

    <div class="mt-4 space-y-2 text-sm text-slate-700">
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9" stroke-width="1.5" fill="none"/>
        </svg>
        <span>Ordered: <?php echo h(fmtDate($orderedAt)); ?></span>
      </div>
      <div class="flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-width="1.5" d="M8 2v3M16 2v3M3 9h18M4 7h16a1 1 0 011 1v11a2 2 0 01-2 2H5a2 2 0 01-2-2V8a1 1 0 011-1z"/>
        </svg>
        <span>Scheduled: <?php echo h(fmtDateTime($scheduledAt)); ?></span>
      </div>
    </div>

    <hr class="my-4 border-slate-200"/>

    <div class="text-xl font-semibold text-blue-600"><?php echo h(peso($total)); ?></div>

    <?php if (strtolower($status)==='pending'): ?>
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

/* ---------- collect DB rows (optional) ---------- */
$buckets = ['Pending'=>[], 'Accepted'=>[], 'Completed'=>[]];
if ($conn) {
  if ($res = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC")) {
    while ($r = mysqli_fetch_assoc($res)) {
      $s = strtolower($r['status'] ?? 'pending');
      $k = ($s==='accepted' ? 'Accepted' : ($s==='completed' ? 'Completed' : 'Pending'));
      $buckets[$k][] = $r;
    }
  }
}

/* ---------- capture POST from checkout (always shown as Pending) ---------- */
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $incoming = [
    'product_name' => $_POST['product_name'] ?? 'Untitled',
    'description'  => ($_POST['quantity'] ?? '1') . ' bag(s) • ' . ($_POST['weight'] ?? '—') . ' KG',
    'status'       => $_POST['status'] ?? 'Pending',
    'ordered_at'   => $_POST['ordered_at'] ?? date('Y-m-d'),
    'scheduled_at' => $_POST['schedule_date'] ?? null,
    'quantity'     => $_POST['quantity'] ?? 1,
    'unit_price'   => $_POST['price'] ?? 0,
    'total'        => $_POST['grand'] ?? null,
    'image'        => $_POST['image'] ?? '',
  ];
  $buckets['Pending'] = array_merge([$incoming], $buckets['Pending']); // show on top
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

        <input type="search" class="w-full p-2 mt-4 border border-gray-200 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Search orders..." />

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
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
        </div>
      </div>
    </main>
  </div>
</body>
</html>
