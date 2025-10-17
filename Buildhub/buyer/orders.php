<?php
session_start();
$conn=mysqli_connect("localhost","root","","user_db");
$sql  = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <script src="../js/qnty.js" defer></script>
    <script src="../js/dropdown.js" defer></script>
    <script src="../js/book.js" defer></script>
  
    
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
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
  <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 w-full max-w-sm mx-auto">
      <img
        src="../images/<?php echo $row['file']; ?>"
        alt="Cement Bags"
        class="w-full h-auto max-h-48 md:max-h-64 object-cover rounded-t-lg"
      />

      <div class="p-4 md:p-6">
        <h3 class="text-base md:text-lg font-medium text-black mb-1">
          <?php echo $row['product_name']; ?>
        </h3>

        <p class="text-gray-600 text-sm mb-3">
          Description: <?php echo $row['description']; ?>
        </p>

        <p class="text-gray-600 text-sm mb-3">
          Stock: <?php echo $row['quantity']; ?>
        </p>

        <p class="text-gray-600 text-sm mb-3">
          Posted by <?= htmlspecialchars($_SESSION['user_id'] ?? 'fname') ?>
        </p>

        <p class="text-lg md:text-xl font-semibold text-black mb-4">
          ₱<span class="product-price"><?php echo $row['unit_price']; ?></span> / unit(s)
        </p>

        <!-- Row layout preserved; now wraps cleanly on small screens -->
        <div class="flex items-center flex-wrap gap-x-4 gap-y-2">
          <form
            action="checkout.php"
            method="POST"
            class="book-form flex items-center flex-wrap gap-x-4 gap-y-2"
            data-image="../images/<?php echo $row['file']; ?>"
          >
            <!-- Hidden Inputs -->
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="price"        value="<?php echo $row['unit_price']; ?>">
            <input type="hidden" name="weight"       value="<?php echo $row['description']; ?>">
            <input type="hidden" name="quantity"     class="quantity-input" value="0">
            <input type="hidden" name="total"        class="total-input" value="0">
            <input type="hidden" name="image"        value="../images/<?php echo $row['file']; ?>">

            <!-- Book button -->
            <button
              type="submit"
              class="book-btn px-4 py-2 bg-white text-black border border-gray-300 rounded-md hover:bg-gray-50 transition-colors"
            >
              Book
            </button>

            <!-- Counter -->
            <div class="flex items-center border border-gray-300 rounded-md px-2 py-1 bg-white shadow-sm">
              <button type="button" class="decreaseBtn px-2 py-1 text-gray-500 hover:text-gray-700">-</button>
              <span class="counter mx-3 text-sm font-medium">0</span>
              <button type="button" class="increaseBtn px-2 py-1 text-gray-500 hover:text-gray-700">+</button>
            </div>

            <!-- Total on the right (stays right on md+, drops under on mobile) -->
            <p class="text-sm text-gray-600 whitespace-nowrap ml-0 md:ml-4">
              Total: <span class="total-value font-semibold text-black">₱0.00</span>
            </p>
          </form>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

        </div>
    </main>
  </div>
</body>
</html>