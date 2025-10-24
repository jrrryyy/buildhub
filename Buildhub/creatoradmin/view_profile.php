<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "user_db");
if (!$conn) {
  die("Database connection failed.");
}

$userId = $_GET['id'] ?? 0;
$user = null;

if ($userId > 0) {
  $sql = "SELECT fname, lname, email, role, profile_picture FROM users WHERE id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $userId);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);
  mysqli_stmt_close($stmt);
}

if (!$user) {
  echo "<p class='text-red-600 text-center mt-10'>User not found.</p>";
  exit;
}

$profilePicture = $userData['profile_picture'] ?? null;
$profilePicturePath = $profilePicture
  ? "../images/profiles/" . $profilePicture
  : "../images/default-icon.png"; // fallback image
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Profile - BuildHub</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">
  <div class="max-w-2xl mx-auto mt-10 bg-white rounded-lg shadow-lg p-6">
    <a href="index.php" class="text-blue-600 hover:underline">&larr; Back to All Users</a>
    
    <div class="flex flex-col items-center mt-6">
      <img src="<?= htmlspecialchars($profilePicturePath) ?>" alt="Profile" 
           class="w-32 h-32 rounded-full border border-gray-300 object-cover mb-4">
      <h1 class="text-2xl font-bold mb-2"><?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?></h1>
      <p class="text-gray-600 mb-1">Role: <span class="font-medium"><?= htmlspecialchars($user['role']) ?></span></p>
      <p class="text-gray-600 mb-4">Email: <span class="font-medium"><?= htmlspecialchars($user['email']) ?></span></p>

      <div class="flex space-x-4 mt-4">
        <a href="mailto:<?= htmlspecialchars($user['email']) ?>" 
           class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition">Send Email</a>
      </div>
    </div>
  </div>
</body>
</html>
