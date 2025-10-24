
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub - Smarter Scheduling for Construction Materials</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        /* Custom styles for logo and any non-Tailwind needs */
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
    </style>
</head>
<body class="bg-white text-gray-800 font-sans antialiased" id="welcome">
    <!-- Navigation Bar -->
    <nav class="flex justify-between items-center px-5 py-4 bg-white shadow-md sticky top-0 z-50">
        <a href="#" class="logo text-lg">BuildHub</a>
        <ul class="flex space-x-8 list-none">
            <li><a href="../main/index.php#welcome" class="text-build-yellow hover:text-build-yellow font-medium transition-colors">Home</a></li>
            <li><a href="../main/how_it_work.php#get-started" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">How It Works</a></li>
            <li><a href="../main/about_us.php#about" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">About</a></li>
            <li><a href="../login/index.php#create-account" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Sign In</a></li>
            <li><a href="../login/register.php#create-account" class="text-gray-800 hover:text-build-yellow font-bold border-2 hover:border-build-yellow px-4 py-2 rounded-full transition-colors">Create Account</a></li>
        </ul>
    </nav>

    <!-- Hero Section -->
    <section  class="flex items-center min-h-screen px-5 py-16 max-w-6xl mx-auto gap-16">
        <!-- Hero Content -->
        <div class="flex-1 pr-8">
            <h1 class="text-5xl font-bold text-gray-800 mb-4 leading-tight">
                Smarter Scheduling for Construction Materials
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-md">
                Connecting buyers and suppliers in one place.
            </p>
            <div class="flex gap-4 flex-wrap">
                <a href="../login/register.php" class="bg-build-yellow text-gray-800 px-8 py-4 rounded-full font-bold hover:bg-yellow-500 transition-all transform hover:-translate-y-1 shadow-lg">
                    Get Started
                </a>
                <a href="../main/how_it_work.php#get-started" class="border-2 border-build-yellow text-gray-800 px-8 py-4 rounded-full font-bold hover:bg-build-yellow transition-all transform hover:-translate-y-1">
                    Learn More
                </a>
            </div>
        </div>
        
        <!-- Hero Image -->
        <div class="flex-1 max-w-md">
            <img src="main_image.png" alt="Stack of construction materials like cement bags on a pallet, representing logistics and supply chain" class="w-full h-auto rounded-2xl shadow-lg">
        </div>
    </section>
</body>
</html>
