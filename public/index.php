
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Welcome to RCALB</title>
</head>
<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-blue-500 p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-white text-2xl font-bold">RCALB</a>
            <div class="px-6">
               
                    
                
                    <a href="#about" class="text-white ml-4 text-white-700 font-bold">Contact</a>

                    <a href="#contact" class="text-white ml-4 text-white-700 font-bold">About us</a>
                
            </div>
        </div>
    </nav>
<section >
    <!-- Hero Section -->
    <div class=" bg-color-white py-20">
        <div class="container mx-auto px-6 text-center">
            <h1 class="text-5xl font-bold text-blue-600">Welcome to RCA Logbook</h1>
            <p class="mt-4 text-gray-600 font-bold">Join us and explore our services</p>
            <div class="mt-8">
                <a href="register.php" class="bg-blue-500 text-white py-3 px-6 rounded-lg shadow-md font-bold mr-4 hover:bg-blue-700">Sign Up</a>
                <a href="login.php" class="bg-gray-500 text-white py-3 px-6 rounded-lg shadow-md font-bold hover:bg-gray-700">Login</a>
            </div>
        </div>
    </div>
</section>
    <!-- Features Section -->
    <section>
    <div class="bg-gray-100 py-20">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-blue-600 mb-6">Features</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 align-item-center">
                <div class="bg-white p-8 rounded-lg shadow-md">
                    <img src="../images/let Journal App Icon.jpg" alt="Feature One" class="w-1/2 h-30 object-cover mb-4 rounded">
                    <h3 class="text-xl font-bold text-blue-500 mb-4">RECORD</h3>
                    <p class="text-gray-600">Record all your actions.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <img src="../images/Time Sheet Log Book.jpg" alt="Feature Two" class="w-1/2 h-28.5 object-cover mb-4 rounded">
                    <h3 class="text-xl font-bold text-blue-500 mb-4">PLAN</h3>
                    <p class="text-gray-600"> review your actions</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <img src="../images/Log Books.jpg" alt="Feature Three" class="w-1/2 h-30 object-cover mb-4 rounded">
                    <h3 class="text-xl font-bold text-blue-500 mb-4">FAST</h3>
                    <p class="text-gray-600">make your own log book fast.</p>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- About Us Section -->
    <section id="about">
    <div class="bg-white py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">About Us</h2>
            <p class="text-gray-600 text-center mb-6">
                We are committed to providing the best service to our users. Our team is dedicated to continuously improving our platform and adding new features to enhance your experience.
            </p>
            <div class="flex flex-wrap justify-center">
                <div class="w-full md:w-1/3 px-4 py-2">
                    <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-blue-500 mb-4">Our Mission</h3>
                        <p class="text-gray-600">To deliver high-quality services that bring value to our users.</p>
                    </div>
                </div>
                <div class="w-full md:w-1/3 px-4 py-2">
                    <div class="bg-gray-100 p-6 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-blue-500 mb-4">Our Vision</h3>
                        <p class="text-gray-600">To be the leading platform in our industry, known for innovation and excellence.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- Contact Us Section -->
    <section id="contact">
    <div class="bg-gray-100 py-20">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-blue-600 mb-6 text-center">Contact Us</h2>
            <form class="max-w-lg mx-auto">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Name</label>
                    <input class="w-full px-3 py-2 border rounded" type="text" name="name" id="name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input class="w-full px-3 py-2 border rounded" type="email" name="email" id="email" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="message">Message</label>
                    <textarea class="w-full px-3 py-2 border rounded" name="message" id="message" rows="4" required></textarea>
                </div>
                <div class="text-center">
                    <button class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-700" type="submit">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</section>
    <!-- Footer -->
    <footer class="bg-blue-500 py-4 text-white">
        <div class="container mx-auto text-center">
            <p>&copy; 2024 RCALB. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

