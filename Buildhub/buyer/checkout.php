<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'] ?? '';
    $price        = (float)($_POST['price'] ?? 0);
    $quantity     = (int)  ($_POST['quantity'] ?? 0);
    $total        = (float)($_POST['total'] ?? 0);
    $weight       = $_POST['weight'] ?? '';
    $image        = $_POST['image'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Checkout Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../js/dropdown.js" defer></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { 'light-gray': '#fafafa' }
        }
      }
    };
  </script>
</head>
<body class="bg-light-gray min-h-screen font-sans flex flex-col lg:flex-row">

  <!-- Sidebar -->
    <?php include __DIR__ . '/../nav/sidebar_buyer.php'; ?>


  <div class="flex-1 lg:ml-64 mt-16 lg:mt-0">
    <!-- Topbar -->
   
      <?php include __DIR__ . '/../nav/topbar_buyer.php'; ?>
    <!-- Main content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
      <h1 class="text-2xl font-bold text-black mb-8">Checkout Details</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Address -->
        <div class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold text-black mb-6">Address Details</h2>
          <input type="text" placeholder="Recipient’s Name" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <input type="text" placeholder="Address" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <input type="text" placeholder="Unit/Floor" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <input type="tel" placeholder="Phone Number" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
          <input type="date" class="w-full p-3 border border-gray-300 rounded-md mb-6 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <button class="w-full bg-yellow-400 text-white py-3 rounded-md font-semibold hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-colors">
            Place Order
          </button>
        </div>

        <!-- Summary -->
        <?php
          $subtotal = (float)$price * (int)$quantity;
          $delivery = 50;
          $grand    = $subtotal + $delivery;
        ?>
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
          <h2 class="text-lg font-semibold text-black mb-6">Summary</h2>
          <div class="rounded-lg border border-gray-200 p-4">
            <div class="flex items-start gap-4">
              <?php if (!empty($image)) : ?>
                <img src="<?php echo htmlspecialchars($image); ?>"
                     alt="<?php echo htmlspecialchars($product_name); ?>"
                     class="w-28 h-20 object-cover rounded-md border border-gray-200" />
              <?php else: ?>
                <div class="w-28 h-20 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">
                  No image
                </div>
              <?php endif; ?>
              <div class="flex-1 leading-tight">
                <h3 class="font-semibold text-black"><?php echo htmlspecialchars($product_name); ?></h3>
                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($weight); ?>KG per bag</p>
                <p class="mt-1 text-sm font-semibold text-black">
                  ₱<?php echo number_format((float)$price, 2); ?>
                  <span class="text-gray-500 font-normal">/ bag</span>
                </p>
              </div>
            </div>
          </div>

          <div class="mt-6 space-y-2 text-sm">
            <div class="flex items-center justify-between text-gray-700">
              <span>Subtotal</span>
              <span class="text-gray-900"><?php echo number_format($subtotal, 2); ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-700">
              <span>Delivery</span>
              <span class="text-gray-900"><?php echo number_format($delivery, 2); ?></span>
            </div>
            <div class="pt-3 mt-2 border-t border-gray-200 flex items-center justify-between">
              <span class="font-semibold text-black">Total</span>
              <span class="font-bold text-black text-lg">
                ₱<?php echo number_format($grand, 2); ?>
              </span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Mobile menu JS -->
  <script>
    const menu = document.getElementById('mobileMenu');
    const toggle = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('closeMenu');

    toggle?.addEventListener('click', () => menu.classList.remove('-translate-x-full'));
    closeBtn?.addEventListener('click', () => menu.classList.add('-translate-x-full'));
  </script>
</body>
</html>

