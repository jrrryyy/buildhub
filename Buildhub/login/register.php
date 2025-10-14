<?php
session_start();

$errors = [
    'register' => $_SESSION['register_error'] ?? ''
];

function displayError($msg) {
    return !empty($msg) ? '<p class="error-message">'.$msg.'</p>' : '';
}

// clear only the key after storing it
unset($_SESSION['register_error']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub - Construction Project Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../js/register_form.js" defer></script>
</head>
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
            <li><a href="../login/index.php#create-account" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Sign In</a></li>
            <li><a href="../login/register.php#create-account" class="text-build-yellow font-bold border-2 border-build-yellow px-4 py-2 rounded-full transition-colors">Create Account</a></li>
        </ul>
    </nav>

    <!-- Main Content Sections -->
    <main class="flex flex-col md:flex-row min-h-screen">
        <!-- Left Section: Slogan -->
        <section class="flex-1 flex items-center justify-center p-8 md:p-12 bg-white">
            <h1 id="hero-slogan" class="text-5xl md:text-6xl font-bold text-black leading-tight text-center md:text-left">
                Schedule smarter.<br>Connect faster.<br>Build Better.
            </h1>
        </section>

        <!-- Right Section: Create Account Form -->
        <section  class="flex-1 flex items-center justify-center p-8 md:p-12 bg-white border-l border-gray-100">
            <div class="w-full max-w-md" <?= $active_form = $_SESSION['active_form'] ?? 'register'; ?>>
                <form id="registerForm" action="login_register.php" method="post" autocomplete="off" class="space-y-4">
  <h2 id="form-title" class="text-2xl font-semibold text-gray-900 mb-6 text-center">Create Account</h2>
  <?= displayError($errors['register']) ?>

  <!-- decoys -->
  <input type="text" autocomplete="username" class="hidden" tabindex="-1" aria-hidden="true">
  <input type="password" autocomplete="new-password" class="hidden" tabindex="-1" aria-hidden="true">

  <!-- hidden real POST fields -->
  <input type="hidden" name="first_name">
  <input type="hidden" name="last_name">
  <input type="hidden" name="email">
  <input type="hidden" name="password">
  <input type="hidden" name="register" value="1">

  <!-- visible fields (no name attributes, same UI) -->
  <div>
    <input data-sync="first_name" id="first-name" type="text" placeholder="First Name" autocomplete="off" readonly
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
  </div>

  <div>
    <input data-sync="last_name" id="last-name" type="text" placeholder="Last Name" autocomplete="off" readonly
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
  </div>

  <div>
    <input data-sync="email" id="email" type="text" placeholder="Email" autocomplete="off" autocapitalize="none" spellcheck="false" inputmode="email" readonly
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
  </div>

  <div>
    <input data-sync="password" id="password" type="password" placeholder="Password" autocomplete="off" readonly
           class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
  </div>

  <div>
    <select name="role" required
            class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
      <option value="buyer">Buyer</option>
      <option value="seller">Seller</option>
    </select>
  </div>

  <button id="submit-create-account" type="submit"
          class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
    Create Account
  </button>
</form>

                <p class="mt-4 text-center text-sm text-gray-600">
                    <a href="../login/index.php" class="text-yellow-500 hover:underline font-medium">Already have an account? Sign in</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>
