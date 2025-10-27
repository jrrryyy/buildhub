<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "user_db");

// ✅ Define sellerId from session (fixes ' variable' and missing profile)
$sellerId = $_SESSION['user_id'] ?? 0;

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

// ✅ Correct fallback logic
$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture
  ? "../images/profiles/" . $profilePicture
  : "../images/default-icon.png"; // fallback image

$sql = "
  SELECT p.*,
         TRIM(CONCAT(
           COALESCE(u.fname, ''),
           ' ',
           COALESCE(u.lname, '')
         )) AS posted_by
  FROM products p
  LEFT JOIN users u ON u.id = p.user_id
  ORDER BY p.id DESC
";
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
    <!-- Top Navbar -->
        <?php include '../nav/topbar_buyer.php'; ?>
  <div class="flex flex-col md:flex-row min-h-screen">
    <!-- Sidebar -->
    <?php include '../nav/sidebar_buyer.php'; ?>
    <!-- Main Content -->
    <main class="ml-16 md:ml-64 p-0 md:p-6 flex-1">
      
        <!-- Content -->
        <div class="p-6">
            <!-- Section Title -->
            <h2 class="text-2xl font-semibold text-black mb-6">Browse</h2>

            <!-- Product Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6">
  <?php while($row = mysqli_fetch_assoc($result)) { ?>
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 w-full">
      <img
        src="../images/<?php echo $row['file']; ?>"
        alt="<?php echo htmlspecialchars($row['product_name']); ?>"
        class="w-full h-80 object-cover rounded-t-lg aspect-[4/3]"
        style="object-position: center;"
      />


      <div class="p-4 md:p-6">
        <h3 class="text-base md:text-lg font-medium text-black mb-1">
          <?php echo $row['product_name']; ?>
        </h3>

        <p class="text-gray-600 text-sm mb-3">
          <?php echo $row['description']; ?>
        </p>

        <p class="text-gray-600 text-sm mb-3">
          Stock: <?php echo $row['quantity']; ?>
        </p>

        <p class="text-gray-600 text-sm mb-3">
        Posted by <?= htmlspecialchars($row['posted_by'] ?? 'Unknown') ?>
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
             <input type="hidden" name="product_id" value="<?= (int)$row['id'] ?>">
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="price"        value="<?php echo $row['unit_price']; ?>">
            <input type="hidden" name="description"  value="<?php echo $row['description']; ?>">
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
    <div class="flex items-stretch border border-gray-300 rounded-md bg-white shadow-sm
            w-full max-w-[10rem] sm:max-w-[12rem]">
  <input type="number" min="0" value="0" class="quantity-field w-full text-center text-black outline-none px-3 py-2
           text-base sm:text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
  />
</div>


            <!-- Total on the right (stays right on md+, drops under on mobile) -->
            <p class="text-sm text-gray-600 whitespace-nowrap ml-0 md:ml-0 h-10 flex items-center">
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
