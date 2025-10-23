<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) { 
  $conn = null;
}

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

$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture ? "../images/profiles/" . $profilePicture : null;

$users = [];
if ($conn) {
  // ✅ Only show Seller and Buyer accounts
  $sql = "SELECT id, fname, lname, role FROM users WHERE role IN ('Seller', 'Buyer') ORDER BY id ASC";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>BuildHub Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../js/dropdown.js" defer></script>
</head>
<body class="bg-white text-black font-sans">

  <!-- Header -->
  <?php include __DIR__ . '/../nav/topbar_admin.php'; ?>
  <?php include __DIR__ . '/../nav/sidebar_admin.php'; ?>

  <main class="max-w-4xl mx-auto px-6 py-4">
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md">
        <?= htmlspecialchars($_SESSION['message']) ?>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <h2 class="text-2xl font-bold mb-6 flex items-center">
      <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
      </svg>
      Sellers & Buyers
    </h2>

    <!-- Search Bar -->
    <div class="mb-6">
      <input 
        type="text" 
        id="searchInput" 
        placeholder="Search user by name or role..." 
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" />
    </div>

    <!-- User Cards -->
    <div id="userList" class="space-y-4">
      <?php if (!empty($users)): ?>
        <?php foreach ($users as $user): ?>
          <div class="user-card bg-white border border-gray-300 rounded-lg p-4 flex justify-between items-center shadow-sm">
            <div>
              <div class="font-semibold text-lg user-name">
                <?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?>
              </div>
              <div class="text-gray-600 user-role"><?= htmlspecialchars($user['role']) ?></div>
            </div>
            <div class="flex space-x-3">
              <form method="POST" action="user_action.php" onsubmit="return confirmDelete(event)">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                <button 
                  name="delete" 
                  class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600 transition">
                  Delete
                </button>
              </form>

              <a 
                href="view_profile.php?id=<?= $user['id'] ?>" 
                class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition">
                View Profile
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-gray-500">No sellers or buyers found.</p>
      <?php endif; ?>
    </div>
  </main>

  <script>
    // Delete confirmation popup
    function confirmDelete(event) {
      const confirmed = confirm("Are you sure you want to delete this user?");
      if (!confirmed) {
        event.preventDefault();
        return false;
      }
      return true;
    }

    // Real-time search filter
    document.getElementById('searchInput').addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const users = document.querySelectorAll('.user-card');
      
      users.forEach(card => {
        const name = card.querySelector('.user-name').textContent.toLowerCase();
        const role = card.querySelector('.user-role').textContent.toLowerCase();
        card.style.display = (name.includes(searchValue) || role.includes(searchValue)) ? 'flex' : 'none';
      });
    });
  </script>

</body>
</html>
