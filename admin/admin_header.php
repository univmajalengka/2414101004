<header class="bg-red-800 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
        <h1 class="text-xl font-extrabold text-white tracking-wider">ADMIN ARUNIKA</h1>
        <nav class="flex space-x-6">
            <a href="dashboard.php" class="text-white hover:text-gray-300 font-semibold">Verifikasi Pembayaran</a>
            <a href="orders.php" class="text-white hover:text-gray-300 font-semibold border-b-2 border-white">Semua Pesanan</a>
        </nav>
        <div class="flex items-center space-x-3">
            <span class="text-lg font-semibold text-white">Halo, <?php echo htmlspecialchars($admin_name); ?></span>
            <a href="<?php echo $logout_url; ?>" class="px-4 py-2 text-sm font-semibold text-red-800 bg-white rounded-lg hover:bg-gray-200 transition duration-200">Logout</a>
        </div>
    </div>
</header>