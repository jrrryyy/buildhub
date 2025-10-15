<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}  
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Inventory Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <script src="../js/dropdown.js" defer></script>
  <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-100">
  <div id="app" class="min-h-screen">

    <!-- TOPBAR (fixed) -->
    <div class="fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200">
      <?php include '../nav/topbar_seller.php'; ?>
    </div>

    <!-- SIDEBAR (off-canvas on mobile, fixed on md+) -->
    <aside id="sidebar"
           class="fixed top-16 bottom-0 left-0 z-30 w-64 bg-white border-r border-gray-200
                  transform -translate-x-full transition-transform duration-200 ease-out
                  md:translate-x-0">
      <?php include '../nav/sidebar_seller.php'; ?>
    </aside>

    <!-- MOBILE OVERLAY -->
    <button id="sidebarOverlay"
            class="fixed inset-0 bg-black/30 z-20 hidden md:hidden"
            aria-hidden="true"></button>

    <!-- MAIN CONTENT -->
    <main class="pt-16 md:pl-64">
      <section class="p-6">
        <h2 class="text-3xl font-bold mb-6">Inventory</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Product Card Example -->
          <article class="bg-white rounded shadow border border-gray-200 overflow-hidden">
            <img src="https://via.placeholder.com/300x200?text=Concrete+Photo"
                 alt="Concrete" class="w-full h-40 object-cover">
            <div class="p-4">
              <h3 class="text-xl font-bold">Concrete</h3>
              <p class="text-gray-600 mt-1">A durable building material used for construction.</p>
            </div>
          </article>
          <!-- Add more cards ... -->
        </div>
      </section>
    </main>

    <!-- FAB -->
    <button id="addProductBtn"
            class="fixed bottom-4 right-4 bg-white border border-black rounded-full p-4 shadow-lg flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
      </svg>
      Add product
    </button>

    <!-- MODAL -->
  <div id="modal"
     class="fixed inset-0 bg-black/50 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
  <div id="modalContent"
       class="w-full max-w-md rounded-xl shadow-xl border border-yellow-100
              bg-gradient-to-b from-white to-yellow-50 p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Add Product:</h2>

    <form action="../admin/crud.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <!-- Product Name -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Product Name:</label>
        <input type="text" name="product_name"
               class="w-full h-11 rounded-lg border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter product name">
      </div>

      <!-- Quantity -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Quantity:</label>
        <input type="number" name="quantity"
               class="w-full h-11 rounded-lg border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter quantity">
      </div>

      <!-- Weight (if you want Price instead, keep your original field) -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Weight:</label>
        <input type="text" name="weight"
               class="w-full h-11 rounded-lg border border-gray-300 bg-white px-3
                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
               placeholder="Enter weight">
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Description:</label>
        <textarea name="description" rows="3"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2
                         placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                  placeholder="Enter description"></textarea>
      </div>

      <!-- Upload Photo (custom input to match screenshot) -->
      <div>
        <label class="block text-sm text-gray-700 mb-1">Add Photo:</label>

        <div class="relative">
          <!-- Faux input look -->
          <div class="w-full h-11 rounded-lg border border-gray-300 bg-white flex items-center px-3">
            <span id="fileName" class="text-sm text-gray-400">Upload Photo</span>
          </div>
          <!-- Clickable overlay button on the right -->
          <label for="imageInput"
                 class="absolute right-1 top-1 h-9 px-3 rounded-md bg-gray-100 border border-gray-300
                        text-sm text-gray-700 flex items-center cursor-pointer hover:bg-gray-200">
            Browse
          </label>
          <!-- Real file input (hidden) -->
          <input id="imageInput" type="file" name="image" accept="image/*" class="sr-only">
        </div>
      </div>

      <!-- Submit -->
      <button type="submit" name="add"
              class="w-full h-12 rounded-lg bg-yellow-500 text-black font-semibold
                     shadow-sm hover:bg-yellow-500/90 focus:outline-none focus:ring-2 focus:ring-yellow-400">
        Add Product
      </button>
    </form>
  </div>
</div>

  </div>

  <!-- TOGGLE JS -->
  <script>
    (function () {
      // Sidebar toggling — requires a button in topbar: [data-sidebar-toggle]
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');
      const toggles = document.querySelectorAll('[data-sidebar-toggle]');

      function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
      }
      function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      }

      toggles.forEach(btn => btn.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) openSidebar(); else closeSidebar();
      }));
      overlay.addEventListener('click', closeSidebar);

      // Ensure overlay closes when switching to md+
      const mq = window.matchMedia('(min-width: 768px)');
      mq.addEventListener?.('change', e => { if (e.matches) closeSidebar(); });

      // Modal toggle
      const addBtn = document.getElementById('addProductBtn');
      const modal = document.getElementById('modal');
      addBtn.addEventListener('click', () => {
        modal.classList.toggle('opacity-0');
        modal.classList.toggle('pointer-events-none');
        modal.classList.toggle('opacity-100');
        modal.classList.toggle('pointer-events-auto');
      });
      modal.addEventListener('click', (e) => {
        if (e.target === modal) {
          modal.classList.add('opacity-0', 'pointer-events-none');
          modal.classList.remove('opacity-100', 'pointer-events-auto');
        }
      });
    })();
  </script>
</body>
</html>
