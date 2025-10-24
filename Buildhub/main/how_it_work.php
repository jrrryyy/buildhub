<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub - How It Works & Features</title>
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
    </style>
</head>
<body class="bg-white text-gray-800 font-sans antialiased" id="get-started">
    <!-- Navigation Bar -->
    <nav class="flex justify-between items-center px-5 py-4 bg-white shadow-md sticky top-0 z-50">
        <a href="../main/index.php" class="logo text-lg">BuildHub</a>
        <ul class="flex space-x-8 list-none">
            <li><a href="../main/index.php#welcome" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Home</a></li>
            <li><a href="../main/how_it_work.php#get-started" class="text-build-yellow hover:text-build-yellow font-medium transition-colors">How It Works</a></li>
            <li><a href="../main/about_us.php#about" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">About</a></li>
            <li><a href="../login/index.php#create-account" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Sign In</a></li>
            <li><a href="../login/register.php#create-account" class="text-gray-800 hover:text-build-yellow font-bold border-2 hover:border-build-yellow px-4 py-2 rounded-full transition-colors">Create Account</a></li>
        </ul>
    </nav>

    <!-- How It Works & Features Section -->
    <section  class="py-16 px-5 max-w-6xl mx-auto">
        <!-- How It Works -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                How BuildHub Works
            </h2>
            <p class="text-xl text-gray-600 mb-6 max-w-2xl mx-auto">
                Connect with users and schedule materials for projects seamlessly.
            </p>
            <div class="text-2xl font-bold text-build-yellow mb-12">
                3 Simple Steps — from posting to completion.
            </div>
            <div class="flex justify-between gap-8 flex-wrap">
                <!-- Step 1 -->
                <div class="flex-1 min-w-[250px] bg-white border-2 border-gray-100 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="w-12 h-12 bg-build-yellow text-gray-800 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Post Your Order
                    </h3>
                    <p class="text-gray-600">
                        Place a detailed order with the materials you need.
                    </p>
                </div>
                <!-- Step 2 -->
                <div class="flex-1 min-w-[250px] bg-white border-2 border-gray-100 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="w-12 h-12 bg-build-yellow text-gray-800 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Get Matched
                    </h3>
                    <p class="text-gray-600">
                        Receive offers and get matched by sellers.
                    </p>
                </div>
                <!-- Step 3 -->
                <div class="flex-1 min-w-[250px] bg-white border-2 border-gray-100 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="w-12 h-12 bg-build-yellow text-gray-800 rounded-full flex items-center justify-center text-xl font-bold mx-auto mb-4">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Complete & Review
                    </h3>
                    <p class="text-gray-600">
                        Materials are delivered, then mark as complete and review.
                    </p>
                </div>
            </div>
        </div>

        <!-- Platform Features -->
        <div class="mb-16">
            <h2 class="text-4xl font-bold text-gray-800 text-center mb-4">
                Platform Features – Order and track your material deliveries
            </h2>
            <p class="text-xl text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                <!-- Subtext can be added here if needed, but per spec it's just the heading -->
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        📅
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Book Online
                    </h3>
                    <p class="text-gray-600">
                        Request materials and schedule deliveries anytime.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        💳
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Pay Onsite
                    </h3>
                    <p class="text-gray-600">
                        Simple, face-to-face transaction upon delivery, no hidden charges.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        📊
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">
                        Calendar Management
                    </h3>
                    <p class="text-gray-600">
                        Built-in calendar to view, manage, and adjust schedules.
                    </p>
                </div>
            </div>
        </div>

        <!-- Call-to-Action -->
        <div class="bg-gray-50 rounded-2xl p-8 md:p-12 text-center">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">
                Ready to Get Started?
            </h2>
            <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
                Join BuildHub today and experience a more convenient way of getting materials for your projects.
            </p>
            <a href="../login/register.php" class="bg-build-yellow text-gray-800 px-8 py-4 rounded-full font-bold hover:bg-yellow-500 transition-all transform hover:-translate-y-1 shadow-lg inline-block">
                Get Started
            </a>
        </div>
    </section>
</body>
</html>
