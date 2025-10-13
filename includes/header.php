<?php
// Cek status login di sini jika diperlukan, tapi untuk sementara biarkan statis
?>

<nav class="bg-white/90 backdrop-blur-md shadow-md fixed top-0 w-full z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex-shrink-0">
                <a href="index.php" class="text-xl font-extrabold text-red-700 tracking-wider">
                    ARUNIKA
                </a>
            </div>
            
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Home</a>
                    <a href="#" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">About</a>
                    <a href="katalog_kostum.php" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Costume</a>
                    <a href="#" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Contact</a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                <a href="register.php" class="bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-lg text-sm font-semibold transition duration-150 shadow-md">
                    Sign Up
                </a>
                <a href="login.php" class="border border-red-700 text-red-700 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-semibold transition duration-150">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>