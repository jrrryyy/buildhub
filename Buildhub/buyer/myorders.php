<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Orders Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS via CDN -->
</head>
<body class="bg-gray-50">

  <!-- Header for profile icon -->
  <?php include '../nav/topbar_buyer.php'; ?>

  <!-- Main content layout: Flex for sidebar and main area -->
  <div class="flex">
    
    <!-- Sidebar -->
    <?php include '../nav/sidebar_buyer.php'; ?>
    <!-- Main Content Area -->
    <main class="pt-16 pl-[320px]">
      <div class="max-w-[1200px] mx-auto px-6 py-6">
        <h1 class="text-3xl font-bold">My Orders</h1>
        <p class="text-gray-500 mt-2">Track and manage your orders</p>

        <!-- Search Bar -->
        <input
          type="search"
          class="w-full p-2 mt-4 border border-gray-200 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-400"
          placeholder="Search orders..."
        />

        <!-- Responsive Grid for Order Sections -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

          <!-- Pending Orders Column -->
          <section>
            <h2 class="text-xl font-bold">Pending Orders</h2>
            <article class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
              <span class="bg-yellow-400 text-black px-2 py-1 rounded inline-block">Pending</span>
              <h3 class="text-xl font-bold mt-2">Concrete</h3>
              <div class="flex items-center mt-2 text-gray-600"><span class="mr-1">📍</span> Location: Somewhere</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📅</span> Date: 2023-10-01</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📦</span> Quantity: 10</div>
              <hr class="my-4 border-gray-200">
              <p class="text-gray-500">Posted by: Username</p>
              <p class="text-gray-500">On 2023-10-01</p>
              <div class="flex flex-wrap gap-2 mt-4">
                <button class="border border-black px-4 py-2 rounded hover:bg-gray-100 transition">Cancel order</button>
                <button class="border border-black px-4 py-2 rounded hover:bg-gray-100 transition">Move order</button>
              </div>
            </article>
          </section>

          <!-- Accepted Orders Column -->
          <section>
            <h2 class="text-xl font-bold">Accepted Orders</h2>
            <article class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
              <span class="bg-green-500 text-white px-2 py-1 rounded inline-block">Accepted</span>
              <h3 class="text-xl font-bold mt-2">Steel Beams</h3>
              <div class="flex items-center mt-2 text-gray-600"><span class="mr-1">📍</span> Location: City Center</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📅</span> Date: 2023-09-15</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📦</span> Quantity: 5</div>
              <hr class="my-4 border-gray-200">
              <p class="text-gray-500">Posted by: Builder123</p>
              <p class="text-gray-500">On 2023-09-15</p>
              <div class="flex flex-wrap gap-2 mt-4">
                <button class="border border-black px-4 py-2 rounded hover:bg-gray-100 transition">Cancel order</button>
                <button class="border border-black px-4 py-2 rounded hover:bg-gray-100 transition">Move order</button>
              </div>
            </article>
          </section>

          <!-- Completed Orders Column -->
          <section>
            <h2 class="text-xl font-bold">Completed Orders</h2>
            <article class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 mt-4">
              <span class="bg-blue-500 text-white px-2 py-1 rounded inline-block">Completed</span>
              <h3 class="text-xl font-bold mt-2">Bricks</h3>
              <div class="flex items-center mt-2 text-gray-600"><span class="mr-1">📍</span> Location: Suburb Area</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📅</span> Date: 2023-08-20</div>
              <div class="flex items-center mt-1 text-gray-600"><span class="mr-1">📦</span> Quantity: 100</div>
              <hr class="my-4 border-gray-200">
              <p class="text-gray-500">Posted by: SupplierX</p>
              <p class="text-gray-500">On 2023-08-20</p>
            </article>
          </section>

        </div>
      </div>
    </main>
  </div>
</body>
</html>
