<?php
session_start();

/* Always set safe defaults so GET won't throw notices */
$product_name = $_POST['product_name'] ?? '';
$price        = (float)($_POST['price'] ?? 0);
$quantity     = (int)  ($_POST['quantity'] ?? 0);
$weight       = $_POST['weight'] ?? '';
$image        = $_POST['image'] ?? '';

$subtotal = $price * $quantity;
$delivery = 50;
$grand    = $subtotal + $delivery;

/* Optional redirect if opened without a product payload */
// if ($product_name === '' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
//   header('Location: ../buyer/browse.php'); exit;
// }
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
    tailwind.config = { theme:{ extend:{ colors:{ 'light-gray':'#fafafa' }}}}
  </script>
</head>
<body class="bg-light-gray min-h-screen font-sans flex flex-col lg:flex-row">

  <!-- Sidebar -->
  <?php include __DIR__ . '/../nav/sidebar_buyer.php'; ?>

  <div class="flex-1 lg:ml-64 mt-16 lg:mt-0">
    <!-- Topbar -->
    <?php include __DIR__ . '/../nav/topbar_buyer.php'; ?>

    <main class="max-w-7xl mx-auto px-6 py-8">
      <h1 class="text-2xl font-bold text-black mb-8">Checkout Details</h1>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Address (=> form posts to My Orders) -->
        <form method="POST" action="../buyer/crud.php" class="bg-white p-6 rounded-lg shadow-md">
          <h2 class="text-lg font-semibold text-black mb-6">Address Details</h2>
       
          <!-- Address fields -->
          <input name="recipient_name" type="text" placeholder="Recipient's Name" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
          <input name="address_line" type="text" placeholder="Address" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
          <input name="province" type="text" placeholder="Province" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          <input name="phone" type="tel" placeholder="Phone Number" class="w-full p-3 border border-gray-300 rounded-md mb-4 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>
          <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date</label>
          <input name="schedule_date" type="date" class="w-full p-3 border border-gray-300 rounded-md mb-6 focus:outline-none focus:ring-2 focus:ring-yellow-400" required>

          <!-- Hidden product payload -->
          <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
          <input type="hidden" name="price"        value="<?php echo htmlspecialchars($price); ?>">
          <input type="hidden" name="quantity"     value="<?php echo htmlspecialchars($quantity); ?>">
          <input type="hidden" name="weight"       value="<?php echo htmlspecialchars($weight); ?>">
          <input type="hidden" name="image"        value="<?php echo htmlspecialchars($image); ?>">
          <input type="hidden" name="delivery"     value="<?php echo htmlspecialchars($delivery); ?>">
          <input type="hidden" name="subtotal"     value="<?php echo htmlspecialchars($subtotal); ?>">
          <input type="hidden" name="grand"        value="<?php echo htmlspecialchars($grand); ?>">
          <input type="hidden" name="ordered_at"   value="<?php echo date('Y-m-d'); ?>">
          <input type="hidden" name="status"       value="Pending">

          <button name="place_order" class="w-full bg-yellow-400 text-white py-3 rounded-md font-semibold hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 transition-colors">
            Place Order
          </button>
        </form>
       
        <!-- Summary -->
        <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
          <h2 class="text-lg font-semibold text-black mb-6">Summary</h2>
          <div class="rounded-lg border border-gray-200 p-4">
            <div class="flex items-start gap-4">
              <?php if (!empty($image)) : ?>
                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" class="w-28 h-20 object-cover rounded-md border border-gray-200" />
              <?php else: ?>
                <div class="w-28 h-20 bg-gray-200 rounded-md flex items-center justify-center text-xs text-gray-500">No image</div>
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
              <span class="font-bold text-black text-lg">₱<?php echo number_format($grand, 2); ?></span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
