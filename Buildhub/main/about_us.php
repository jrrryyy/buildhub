<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuildHub - About Us</title>
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
<body class="bg-white text-gray-800 font-sans antialiased" id="about">
    <!-- Navigation Bar -->
    <nav class="flex justify-between items-center px-5 py-4 bg-white shadow-md sticky top-0 z-50">
        <a href="../main/index.php" class="logo text-lg">BuildHub</a>
        <ul class="flex space-x-8 list-none">
            <li><a href="../main/index  .php#welcome" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Home</a></li>
            <li><a href="../main/how_it_work.php#get-started" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">How It Works</a></li>
            <li><a href="../main/about_us.php#about" class="text-build-yellow hover:text-build-yellow font-medium transition-colors">About</a></li>
            <li><a href="../login/index.php#create-account" class="text-gray-800 hover:text-build-yellow font-medium transition-colors">Sign In</a></li>
            <li><a href="../login/register.php#create-account" class="text-gray-800 hover:text-build-yellow font-bold border-2 hover:border-build-yellow px-4 py-2 rounded-full transition-colors">Create Account</a></li>
        </ul>
    </nav>

    <!-- About Us Section -->
    <section  class="py-16 px-5 max-w-6xl mx-auto">
        <!-- About BuildHub -->
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-6">
                About BuildHub
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                BuildHub’s purpose is to let builders book delivery and reserve materials from suppliers and pay onsite for faster operation.
            </p>
        </div>

        <!-- Core Values -->
        <div class="mb-16">
            <h3 class="text-3xl font-bold text-gray-800 text-center mb-12">
                Our Core Values
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Value 1: User Satisfaction -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        😊
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">
                        User Satisfaction
                    </h4>
                    <p class="text-gray-600 leading-relaxed">
                        Your satisfaction is our priority. We’re here to help you make your projects a success.
                    </p>
                </div>
                <!-- Value 2: Community Driven -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        👥
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">
                        Community Driven
                    </h4>
                    <p class="text-gray-600 leading-relaxed">
                        A platform shaped by collaboration, where every connection strengthens the industry.
                    </p>
                </div>
                <!-- Value 3: Efficient -->
                <div class="bg-white border-2 border-yellow-400 rounded-2xl p-8 text-center shadow-sm hover:shadow-md transition-all hover:-translate-y-2">
                    <div class="text-5xl text-build-yellow mb-4">
                        ⚡
                    </div>
                    <h4 class="text-xl font-bold text-gray-800 mb-3">
                        Efficient
                    </h4>
                    <p class="text-gray-600 leading-relaxed">
                        Optimizing material requests and delivery schedules to reduce delays and keep operations running smoothly.
                    </p>
                </div>
            </div>
        </div>

        <!-- Mission Statement -->
        <div class="bg-gray-50 rounded-2xl p-8 md:p-12 text-center">
            <h3 class="text-3xl font-bold text-gray-800 mb-6">
                Our Mission
            </h3>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                Our mission is to empower the construction industry through technology, enabling fast, accurate, and efficient operations. We strive to enhance both customer experience and internal workflows, helping businesses build smarter and deliver better. With BuildHub, connections become simple and reliable.
            </p>
        </div>
    </section>
</body>
</html>
