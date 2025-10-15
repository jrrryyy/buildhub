

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
   <section  class="flex-1 flex items-center justify-center p-8 md:p-12 bg-white border-l border-gray-100">
            <div class="w-full max-w-md" <?= $active_form = $_SESSION['active_form'] ?? 'add'; ?>>
               <form action="crud.php" method="POST" enctype="multipart/form-data">
                     <h2 id="form-title" class="text-2xl font-semibold text-gray-900 mb-6 text-center">Create Account</h2>
                
                     <div>
                        <input id="product-name" type="text" name="product_name" placeholder="Product Name" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div>
                        <input id="price" type="number" name="price" placeholder="Price" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div>
                        <input id="quantity" type="number" name="quantity" placeholder="Quantity" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                     <div>
                        <input id="weight" type="number" name="weight" placeholder="Weight" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                     <div>
                        <input id="delivery" type="number" name="delivery" placeholder="Delivery" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <div>
                        <input id="file" type="file" name="image" class="w-full px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                    </div>
                    <button id="add" type="submit" name="add" class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        add
                    </button>
                     <button id="delete" type="submit" name="delete" class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        delete
                    </button> <button id="update" type="submit" name="update" class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        update
                    </button>
                    <a href="../buyer/orders.php" class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                        ahsjkdhkjahsdkjhakjsd
                    </a>
                </form>
                
            </div>
        </section>
    <button id="logout" type="submit" name="logout" class="w-full py-3 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
        <a href="../nav/logout.php">
            Logout
        </a>
    </button>
</body>

</html>