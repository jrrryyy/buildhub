<?php
session_start();

$errors = [
   'login' => $_SESSION['login_error'] ?? '',
];

unset($_SESSION['login_error']); // clear after use

function displayError($msg) {
    return !empty($msg) ? '<p class="error-message">'.$msg.'</p>' : '';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub - Sign In</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/remove_autofill.js" defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'build-yellow': '#FFC107',
                    }
                }
            }
        }
    </script>
    <style>
        /* Custom styles for logo */
        .logo {
            background-color: #FFC107;
            color: #333333;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            
        }
        .error-message {
            padding: 12px;  
            background: #f8d7da;
            border-radius: 6px;
            font-size: 16px;
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

    </style>
</head>
<body class="bg-white text-gray-800 font-sans antialiased" id="create-account">
    <!-- Navigation Bar -->
    <nav class="flex justify-between items-center px-5 py-4 bg-white shadow-md sticky top-0 z-50">
        <a href="#" class="logo text-lg">BuildHub</a>
        <ul class="flex space-x-8 list-none">
            <li><a href="../main/index.php#welcome" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Home</a></li>
            <li><a href="../main/how_it_work.php#get-started" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">How It Works</a></li>
            <li><a href="../main/about_us.php#about" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">About</a></li>
            <li><a href="../login/index.php#create-account" class="text-build-yellow hover:text-build-yellow font-medium transition-colors">Sign In</a></li>
            <li><a href="../login/register.php#create-account" class="text-gray-800 hover:text-build-yellow font-bold border-2 hover:border-build-yellow px-4 py-2 rounded-full transition-colors">Create Account</a></li>
        </ul>
    </nav>

    <!-- Main Content Sections -->
    <main class="flex flex-col md:flex-row min-h-screen">
        <!-- Left Section: Login Form -->
        <section class="flex-1 flex items-center justify-center p-8 md:p-12 bg-white">
            <div class="w-full max-w-md">
                <form id="loginForm" method="post" action="login_register.php" autocomplete="off" class="space-y-4">
  <h2 id="form-title" class="text-2xl font-semibold text-gray-900 mb-6 text-center">Sign In</h2>
  <?= displayError($errors['login']) ?>

  <!-- decoys (first in DOM; browsers waste autofill here) -->
  <input type="text" autocomplete="username" class="hidden" tabindex="-1" aria-hidden="true">
  <input type="password" autocomplete="current-password" class="hidden" tabindex="-1" aria-hidden="true">

  <!-- hidden real POST fields -->
  <input type="hidden" name="email">
  <input type="hidden" name="password">
  <input type="hidden" name="login" value="1">

  <!-- visible (unstyled names removed, same UI) -->
  <div>
    <input
      data-sync="email"
      id="email"
      type="text"
      placeholder="Email"
      autocomplete="off"
      autocapitalize="none"
      spellcheck="false"
      inputmode="email"
      readonly
      class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
    >
  </div>

  <div>
    <input
      data-sync="password"
      id="password"
      type="password"
      placeholder="Password"
      autocomplete="off"
      autocapitalize="off"
      spellcheck="false"
      readonly
      class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
    >
  </div>

  <p class="text-center text-sm text-gray-600">
    <a id="forgot-password-link" href="forgot_password.php" class="text-yellow-500 hover:underline font-medium">Forgot Password?</a>
  </p>

  <button id="submit-sign-in"
          class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
    Sign In
  </button>
</form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    Don't have an account? <a href="../login/register.php" class="text-yellow-500 hover:underline font-medium">Create Account</a>
                </p>
            </div>
        </section>

        <!-- Right Section: Welcome Text -->
        <section class="flex-1 flex items-center justify-center p-8 md:p-12 bg-white border-l border-gray-100">
            <h1 id="welcome-text" class="text-4xl md:text-5xl font-bold text-gray-900 text-center">
                Welcome to<br>Build<font class="text-amber-500">Hub</font>
            </h1>
        </section>
    </main>
</body>
</html>
