<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) { die("DB error: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8mb4");

/* Require login */
$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($sellerId <= 0) {
    header("Location: ../auth/login.php");
    exit;
}

/* Fetch this seller's items only (valid FK rows) */
$sql = "
  SELECT
    oi.id, oi.order_id, oi.product_id, oi.product_name, oi.description,
    oi.unit_price, oi.quantity, oi.line_total, oi.file,
    oi.created_at, oi.updated_at
  FROM order_items AS oi
  INNER JOIN users AS u ON u.id = oi.user_id
  WHERE u.role = 'seller' AND u.id = ?
  ORDER BY oi.id DESC
";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) { die("Prepare error: " . mysqli_error($conn)); }
mysqli_stmt_bind_param($stmt, "i", $sellerId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

$items = [];
while ($r = mysqli_fetch_assoc($res)) { $items[] = $r; }

mysqli_stmt_close($stmt);
mysqli_close($conn);

/* Helpers to avoid warnings and XSS */
function h($v, $fallback = "") {
    if ($v === null || $v === "") return $fallback;
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

/* Send HTML only (no JSON here) */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>BuildHub Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../js/dropdown.js" defer></script>
  <script src="../js/modal.js" defer></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ['Inter','sans-serif'] } } }
    };
  </script>
</head>
<body class="bg-white font-sans antialiased">
  <div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <?php include '../nav/sidebar_seller.php'; ?>
    <!-- Main -->
    <main class="ml-16 md:ml-64 p-0 md:p-6 flex-1">
      <?php include '../nav/topbar_seller.php'; ?>

      <div class="p-6">
        <h2 class="text-2xl font-semibold text-black mb-6">Listings</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <?php if (empty($items)): ?>
            <p class="text-gray-600">No items found.</p>
          <?php else: ?>
            <?php foreach ($items as $row): ?>
              <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-sm">
                <img
                  src="<?='../images/'.h($row['file'],'placeholder.png')?>"
                  alt="<?=h($row['product_name'],'Product')?>"
                  class="w-full h-auto max-h-48 md:max-h-64 object-cover rounded-t-lg"
                />

                <div class="p-6">
                  <h3 class="text-lg font-medium text-black mb-1"><?=h($row['product_name'],'—')?></h3>
                  <p class="text-gray-600 text-sm mb-3"><?=h($row['description'],'—')?></p>
                  <p class="text-gray-600 text-sm mb-3">Stock: <?=h($row['quantity'],'0')?></p>
                  <p class="text-gray-600 text-sm mb-3"></p>

                  <p class="text-xl font-semibold text-black mb-4">
                    ₱<span class="product-price"><?=h($row['unit_price'],'0')?></span> / unit(s)
                  </p>

                  <div class="flex items-center gap-x-4">
                    <form action="checkout.php" method="POST" class="book-form flex items-center gap-x-4"
                          data-image="<?='../images/'.h($row['file'])?>">
                      <!-- Hidden -->
                      <input type="hidden" name="product_name" value="<?=h($row['product_name'])?>">
                      <input type="hidden" name="price"        value="<?=h($row['unit_price'])?>">
                      <input type="hidden" name="weight"       value="<?=h($row['description'])?>">
                      <input type="hidden" name="quantity"     class="quantity-input" value="0">
                      <input type="hidden" name="total"        class="total-input" value="0">
                      <input type="hidden" name="image"        value="<?='../images/'.h($row['file'])?>">
                    
                      <button type="submit"
                        class="book-btn px-12 py-2 bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50">
                        Update
                      </button>
                    </form>
                       <form action="../admin/crud.php" method="POST" class="inline">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                          <button type="submit" name="delete" value="1"
                        class="px-14 py-2 bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50">
                      Delete
                    </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
  <button id="addProductBtn" class="fixed bottom-4 right-4 bg-white border border-black rounded-full p-4 shadow-lg flex items-center"> 
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"> 
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/> 
    </svg> Add product </button> 
  <!-- MODAL --> <!-- MODAL --> 
   <div id="modal" class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 ease-in-out">
     <div id="modalContent" class="w-full max-w-md rounded-2xl shadow-2xl border border-yellow-200 bg-gradient-to-b from-white to-yellow-50 p-6 transform scale-95 transition-all duration-300"> 
      <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Add Product</h2> <form action="../admin/crud.php" method="POST" enctype="multipart/form-data" class="space-y-3"> 
        <!-- Product Name --> 
         <div> <label class="block text-sm text-gray-700 mb-1">Product Name:</label> <input type="text" name="product_name" class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Enter product name"> 
        </div> 
        <!-- Quantity --> 
         <div> 
          <label class="block text-sm text-gray-700 mb-1">Quantity:</label> 
          <input type="number" name="quantity" class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Enter quantity"> </div> 
          <!-- Price --> 
           <div> <label class="block text-sm text-gray-700 mb-1">Price:</label> <input type="text" name="price" class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Enter price"> </div> 
           <!-- Description --> 
            <div> <label class="block text-sm text-gray-700 mb-1">Description:</label> <textarea name="description" rows="2" class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" placeholder="Enter description"></textarea> </div> 
            <!-- Upload Photo --> 
             <div> <label class="block text-sm text-gray-700 mb-1">Add Photo:</label> 
             <div class="relative"> <div class="w-full h-10 rounded-md border border-gray-300 bg-white flex items-center px-3"> <span id="fileName" class="text-sm text-gray-400 truncate">Upload Photo</span> </div> 
             <label for="imageInput" class="absolute right-1 top-1 h-8 px-3 rounded-md bg-gray-100 border border-gray-300 text-sm text-gray-700 flex items-center cursor-pointer hover:bg-gray-200"> Browse </label> <input id="imageInput" type="file" name="image" accept="image/*" class="sr-only"> 
            </div> 
          </div> 
          <!-- Submit --> 
           <button type="submit" name="add" class="w-full h-10 rounded-md bg-yellow-500 text-black font-semibold shadow-sm hover:bg-yellow-400/90 focus:outline-none focus:ring-2 focus:ring-yellow-400"> Add Product </button> 
          </form> 
        </div> 
      </div> 
    </div>
</body>
</html>
