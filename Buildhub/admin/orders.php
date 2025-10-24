<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) { die("DB error: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8mb4");

/* Require login */
$sellerId = (int)($_SESSION['user_id'] ?? 0);
if ($sellerId <= 0) {
    header("Location: ../login/index.php");
    exit;
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
$profilePicturePath = $profilePicture
  ? "../images/profiles/" . $profilePicture
  : "../images/default-icon.png"; // fallback image

$sql = "
  SELECT
    oi.id, oi.order_id, oi.product_id, oi.product_name, oi.description,
    oi.unit_price, oi.quantity, oi.line_total, oi.file,
    oi.created_at, oi.updated_at
  FROM products AS oi
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
$flashError   = $_SESSION['crud_error']   ?? '';
$flashSuccess = $_SESSION['crud_success'] ?? '';
unset($_SESSION['crud_error'], $_SESSION['crud_success']);


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
  <script src="../js/fileinput.js" defer></script>
  <script src="../js/globalmodal.js" defer></script>
  <script>
    tailwind.config = {
      theme: { extend: { fontFamily: { sans: ['Inter','sans-serif'] } } }
    };
  </script>
</head>
<body class="bg-white font-sans antialiased"> 
  <?php include '../nav/topbar_seller.php'; ?>
  <div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <?php include '../nav/sidebar_seller.php'; ?>
    <!-- Main -->
    <main class="ml-16 md:ml-64 p-0 md:p-6 flex-1">
     

      <div class="p-6">
        <h2 class="text-2xl font-semibold text-black mb-6">Listings</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
  <?php if (empty($items)): ?>
    <p class="text-gray-600 col-span-full">No items found.</p>
  <?php else: ?>
    <?php foreach ($items as $row): ?>
      <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 w-full max-w-sm mx-auto h-full flex flex-col">
        <img
          src="<?='../images/'.h($row['file'],'placeholder.png')?>"
          alt="<?=h($row['product_name'],'Product')?>"
          class="w-full h-48 sm:h-56 md:h-64 object-cover rounded-t-lg"
        />

        <div class="p-4 sm:p-6 flex flex-col flex-1">
          <h3 class="text-lg font-medium text-black mb-1 line-clamp-1"><?=h($row['product_name'],'—')?></h3>
          <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?=h($row['description'],'—')?></p>
          <p class="text-gray-600 text-sm mb-3">Stock: <?=h($row['quantity'],'0')?></p>
          <p class="text-gray-600 text-sm mb-3"></p>

          <p class="text-xl font-semibold text-black mb-4">
            ₱<span class="product-price"><?=h($row['unit_price'],'0')?></span> / unit(s)
          </p>

          <div class="mt-auto">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4">
              <form action="" method="POST"
                    class="book-form flex items-center gap-3 sm:gap-4 w-full"
                    data-image="<?='../images/'.h($row['file'])?>">
                <!-- Hidden -->
                <input type="hidden" name="product_name" value="<?=h($row['product_name'])?>">
                <input type="hidden" name="price"        value="<?=h($row['unit_price'])?>">
                <input type="hidden" name="description"  value="<?=h($row['description'])?>">
                <input type="hidden" name="quantity"     class="quantity-input" value="0">
                <input type="hidden" name="total"        class="total-input" value="0">
                <input type="hidden" name="image"        value="<?='../images/'.h($row['file'])?>">

                <!-- Update button -->
<button type="button"
        class="openUpdateModal w-full min-w-0 px-4 sm:px-5 md:px-6 py-2
               bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50
               text-sm sm:text-base text-center"
                  data-id="<?= (int)$row['id'] ?>"
                  data-name="<?= h($row['product_name']) ?>"
                  data-qty="<?= h($row['quantity'], '0') ?>"
                  data-price="<?= h($row['unit_price'], '0') ?>"
                  data-desc="<?= h($row['description']) ?>"
                  data-image="<?='../images/'.h($row['file'],'placeholder.png')?>"
                >
                  Update
                </button>

                <!-- (Optional) space for your quantity/total controls if present -->
                <!-- <div class="hidden sm:block flex-1"></div> -->
              </form>

              <!-- Delete button -->
              <form action="../admin/crud.php" method="POST"
                    onsubmit="return confirm('Are you sure you want to delete this product?');"
                    class="w-full ">
                <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                <button type="submit" name="delete" value="1"
                        class="w-full min-w-0 px-4 sm:px-5 md:px-6 py-2
               bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50
               text-sm sm:text-base text-center">
                  Delete
                </button>
              </form>
            </div>
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
    </svg> Add product
  </button>

  <!-- ADD MODAL (existing) -->
  <div
    id="modal"
    class="fixed inset-0 z-[100] bg-black/50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 ease-in-out"
  >
    <div
      id="modalContent"
      class="w-full mx-4 sm:mx-0 max-w-md sm:max-w-lg max-h-[85vh] overflow-y-auto rounded-2xl shadow-2xl ring-1 ring-black/5 border border-yellow-200 bg-gradient-to-b from-white to-yellow-50 p-6 transform scale-95 transition-all duration-300"
    >
      <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Add Product</h2>
      <?php if ($flashError): ?>
      <div class="mb-3 rounded-md border border-red-300 bg-red-50 text-red-700 px-3 py-2 text-sm text-center">
        <?= h($flashError) ?>
      </div>
      <?php endif; ?>
      <form action="../admin/crud.php" method="POST" enctype="multipart/form-data" class="space-y-3">
        <!-- Product Name -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Product Name:</label>
          <input type="text" name="product_name"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter product name">
        </div>

        <!-- Quantity -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Quantity:</label>
          <input type="number" name="quantity"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter quantity">
        </div>

        <!-- Price -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Price:</label>
          <input type="text" name="price"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter price">
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Description:</label>
          <textarea name="description" rows="2"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                    placeholder="Enter description"></textarea>
        </div>

        <!-- Upload Photo -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Add Photo:</label>
          <div class="relative">
            <div class="w-full h-10 rounded-md border border-gray-300 bg-white flex items-center px-3">
              <span id="fileName" class="text-sm text-gray-400 truncate">Upload Photo</span>
            </div>
            <label for="imageInput"
                   class="absolute right-1 top-1 h-8 px-3 rounded-md bg-gray-100 border border-gray-300 text-sm text-gray-700 flex items-center cursor-pointer hover:bg-gray-200">
              Browse
            </label>
            <input id="imageInput" type="file" name="image" accept="image/*" class="sr-only">
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" name="add"
                class="w-full h-10 rounded-md bg-yellow-500 text-black font-semibold shadow-sm hover:bg-yellow-400/90 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          Add Product
        </button>
      </form>
    </div>
  </div>

  <!-- UPDATE MODAL (single, global) -->
  <div
    id="updateModal"
    class="fixed inset-0 z-[101] bg-black/50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 ease-in-out"
    aria-hidden="true"
  >
    <div
      id="updateModalContent"
      class="w-full mx-4 sm:mx-0 max-w-md sm:max-w-lg max-h-[85vh] overflow-y-auto rounded-2xl shadow-2xl ring-1 ring-black/5 border border-yellow-200 bg-gradient-to-b from-white to-yellow-50 p-6 transform scale-95 transition-all duration-300"
      role="dialog"
      aria-modal="true"
      aria-labelledby="updTitle"
    >
      <div class="flex items-center justify-between mb-4">
        <h2 id="updTitle" class="text-xl font-bold text-gray-900 text-center w-full">Update Product</h2>
        <button type="button" id="updateCloseBtn" class="absolute right-4 top-4 text-gray-500 hover:text-gray-700" aria-label="Close">✕</button>
      </div>

      <form action="../admin/crud.php" method="POST" enctype="multipart/form-data" class="space-y-3" id="updateForm">
        <input type="hidden" name="id" id="updId">

        <!-- Product Name -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Product Name:</label>
          <input type="text" name="product_name" id="updName"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter product name">
        </div>

        <!-- Quantity -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Quantity:</label>
          <input type="number" name="quantity" id="updQty" min="0"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter quantity">
        </div>

        <!-- Price -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Price:</label>
          <input type="text" name="price" id="updPrice"
                 class="w-full h-10 rounded-md border border-gray-300 bg-white px-3 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                 placeholder="Enter price">
        </div>

        <!-- Description -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Description:</label>
          <textarea name="description" id="updDesc" rows="2"
                    class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                    placeholder="Enter description"></textarea>
        </div>

        <!-- Current Photo Preview -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Current Photo:</label>
          <img id="updPreview" src="" alt="Preview"
               class="w-full h-40 object-cover rounded-md border border-gray-200 mb-2">
        </div>

        <!-- Replace Photo (optional) -->
        <div>
          <label class="block text-sm text-gray-700 mb-1">Replace Photo (optional):</label>
          <div class="relative">
            <div class="w-full h-10 rounded-md border border-gray-300 bg-white flex items-center px-3">
              <span id="updFileName" class="text-sm text-gray-400 truncate">Upload Photo</span>
            </div>
            <label for="updImageInput"
                   class="absolute right-1 top-1 h-8 px-3 rounded-md bg-gray-100 border border-gray-300 text-sm text-gray-700 flex items-center cursor-pointer hover:bg-gray-200">
              Browse
            </label>
            <input id="updImageInput" type="file" name="image" accept="image/*" class="sr-only">
          </div>
        </div>

        <!-- Submit -->
        <button type="submit" name="update"
                class="w-full h-10 rounded-md bg-yellow-500 text-black font-semibold shadow-sm hover:bg-yellow-400/90 focus:outline-none focus:ring-2 focus:ring-yellow-400">
          Save Changes
        </button>
      </form>
    </div>
  </div>

</body>
<script>
/* Auto-open Add modal if there was a flash error */
(function () {
  const hasError = <?= json_encode(!empty($flashError)) ?>;
  if (!hasError) return;

  const modal = document.getElementById('modal');
  const modalContent = document.getElementById('modalContent');

  function openModal() {
    modal.classList.remove('opacity-0', 'pointer-events-none');
    modalContent.classList.remove('scale-95');
    modalContent.classList.add('scale-100');
  }
  openModal();
})();
</script>
</html>
