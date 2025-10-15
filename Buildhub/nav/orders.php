<?php
session_start();
$conn=mysqli_connect("localhost","root","","user_db");
$sql  = "SELECT * FROM order_items";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/dropdown.js" defer></script>
    <script src="../js/modal.js" defer></script>
  
    
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
<body class="bg-white font-sans antialiased">
  <div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <?php include '../nav/sidebar_buyer.php'; ?>
    <!-- Main Content -->
    <main class="ml-16 md:ml-64 p-0 md:p-6 flex-1">
        <!-- Top Navbar -->
        <?php include '../nav/topbar_buyer.php'; ?>
        <!-- Content -->
        <div class="p-6">
            <!-- Section Title -->
            <h2 class="text-2xl font-semibold text-black mb-6">Browse</h2>

            <!-- Product Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php
                
                while($row = mysqli_fetch_assoc($result)) 
                {
                ?>
<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 max-w-sm">
  <img src="../images/<?php echo $row['file']; ?>" alt="Cement Bags" class="w-full h-auto max-h-48 md:max-h-64 object-cover rounded-t-lg" />

  <div class="p-6">
    <h3 class="text-lg font-medium text-black mb-1"><?php echo $row['product_name']; ?></h3>
    <p class="text-gray-600 text-sm mb-3">Description: <?php echo $row['description']; ?></p>
    <p class="text-gray-600 text-sm mb-3">Stock: <?php echo $row['quantity']; ?></p>
    <p class="text-gray-600 text-sm mb-3">Posted by <?= htmlspecialchars($_SESSION['fname'] ?? 'User') ?></p>
    <p class="text-xl font-semibold text-black mb-4">
      ₱<span class="product-price"><?php echo $row['unit_price']; ?></span> / unit(s)
    </p>

    <!-- Row layout like the screenshot -->
    <div class="flex items-center gap-x-4">
      <form action="checkout.php" method="POST" class="book-form flex items-center gap-x-4" data-image="../images/<?php echo $row['file']; ?>">
        <!-- Hidden Inputs -->
        <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
        <input type="hidden" name="price" value="<?php echo $row['unit_price']; ?>">
        <input type="hidden" name="weight" value="<?php echo $row['description']; ?>">
        <input type="hidden" name="quantity" class="quantity-input" value="0">
        <input type="hidden" name="total" class="total-input" value="0">
        <input type="hidden" name="image" value="../images/<?php echo $row['file']; ?>">
        <!-- Book button -->
        <button type="submit"
          class="book-btn px-4 py-2 bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
          Book
        </button>

        <!-- Counter -->
      
        <div class="flex items-center border border-gray-300 rounded-md px-2 py-1 bg-white shadow-sm">
          <button type="button" class="decreaseBtn px-2 py-1 text-gray-500 hover:text-gray-700 ">-</button>
          <span class="counter mx-3 text-sm font-medium">0</span>
          <button type="button" class="increaseBtn px-2 py-1 text-gray-500 hover:text-gray-700">+</button>
        </div>

        <!-- Total on the right -->
        <p class="ml-4 text-sm text-gray-600 whitespace-nowrap">
          Total: <span class="total-value font-semibold text-black">₱0.00</span>
        </p>
      </form>
    </div>
  </div>
</div>
<?php
}
?>
            </div>
        </div>
    </main>
     <button id="addProductBtn"
            class="fixed bottom-4 right-4 bg-white border border-black rounded-full p-4 shadow-lg flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Add product
    </button>

    <!-- MODAL -->
<!-- MODAL -->
<div id="modal"
  class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 ease-in-out">
  
  <div id="modalContent"
       class="w-full max-w-md rounded-2xl shadow-2xl border border-yellow-200
              bg-gradient-to-b from-white to-yellow-50 p-6 transform scale-95 transition-all duration-300">
              
    <h2 class="text-xl font-bold text-gray-900 mb-4 text-center">Add Product</h2>

    <form action="../admin/crud.php" method="POST" enctype="multipart/form-data" class="space-y-3">
      <!-- Product Name -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Product Name:</label>
        <input type="text" name="product_name"
               class="w-full h-10 rounded-md border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter product name">
      </div>

      <!-- Quantity -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Quantity:</label>
        <input type="number" name="quantity"
               class="w-full h-10 rounded-md border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter quantity">
      </div>

      <!-- Price -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Price:</label>
        <input type="text" name="price"
               class="w-full h-10 rounded-md border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter price">
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Description:</label>
        <textarea name="description" rows="2"
                  class="w-full rounded-md border border-gray-300 bg-white px-3 py-2
                         placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
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
                 class="absolute right-1 top-1 h-8 px-3 rounded-md bg-gray-100 border border-gray-300
                        text-sm text-gray-700 flex items-center cursor-pointer hover:bg-gray-200">
            Browse
          </label>
          <input id="imageInput" type="file" name="image" accept="image/*" class="sr-only">
        </div>
      </div>

      <!-- Submit -->
      <button type="submit" name="add"
              class="w-full h-10 rounded-md bg-yellow-500 text-black font-semibold
                     shadow-sm hover:bg-yellow-400/90 focus:outline-none focus:ring-2 focus:ring-yellow-400">
        Add Product
      </button>
    </form>
  </div>
</div>


  </div>
  </div>
</body>
</html>