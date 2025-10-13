<?php
require_once 'db_connect.php'; 
session_start();

// memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$kostum_list = [];
$sql = "SELECT id, name, harga_sewa, image_url FROM costumes WHERE stock > 0 ORDER BY name ASC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $kostum_list[] = [
            'id' => $row['id'],
            'nama' => $row['name'],
            'harga' => $row['harga_sewa'], 
            'gambar' => $row['image_url']
        ];
    }
} else {
    // menampilkan pesan jika tidak ada kostum
    $error_message = "Tidak ada kostum tersedia saat ini.";
}

/**
 * Fungsi untuk memformat angka menjadi format Rupiah.
 */
function formatRupiah($number) {
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

$logout_url = 'logout.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Kostum - Arunika</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body {
            font-family: serif; 
            background-color: #f7f7f7;
        }
        .serif-font {
            font-family: serif; 
        }
    </style>
</head>
<body>
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            
            <div class="flex-shrink-0">
                <a href="index.php" class="text-xl font-extrabold text-red-700 tracking-wider">
                    ARUNIKA
                </a>
            </div>

            <nav class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-4">
                    <a href="index.php" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">Home</a>
                    <a href="index.php" class="text-gray-600 hover:text-red-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150">About</a>
                    <a href="katalog_kostum.php" class="text-red-700 font-bold hover:text-red-800 border-b-2 border-red-700 px-3 py-2 text-sm font-medium transition duration-150">Costume</a>
                    <a href="#contact" class="text-gray-700 hover:text-red-700">Contact</a>
                </div>
            </nav>

            <div class="flex items-center space-x-3">
                <a href="#" class="text-lg font-semibold text-gray-700 hover:text-red-700">Halo, <?php echo htmlspecialchars($_SESSION['role']); ?></a>
                <a href="<?php echo $logout_url; ?>" class="px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800 transition duration-200">
                    Logout
                </a>
            </div>

        </div>
        <hr class="border-gray-200">
    </header>
 

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center serif-font">Katalog Kostum Kimono</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md text-center" role="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 md:gap-8">
            
            <?php foreach ($kostum_list as $kostum): ?>
                <div class="bg-white border border-gray-200 rounded-xl shadow-lg hover:shadow-xl transition duration-300">
                    
                    <div class="p-2">
                        <img 
                            src="<?php echo htmlspecialchars($kostum['gambar']); ?>" 
                            alt="<?php echo htmlspecialchars($kostum['nama']); ?>" 
                            class="w-full h-auto object-cover rounded-lg border border-gray-100 aspect-square"
                        >
                    </div>

                    <div class="p-4 pt-2 text-center">
                        <h3 class="text-lg font-semibold text-gray-800 serif-font mb-1">
                            <?php echo htmlspecialchars($kostum['nama']); ?>
                        </h3>
                        <p class="text-red-700 font-bold text-base">
                            <?php echo formatRupiah($kostum['harga']); ?>
                        </p>
                        
                        <a href="detail_kostum.php?id=<?php echo $kostum['id']; ?>"
                            class="mt-3 block w-full bg-red-700 text-white py-2 rounded-md hover:bg-red-800 transition duration-200 text-sm font-medium"
                        >
                            Sewa Sekarang
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>

    </main>

    <?php require_once 'includes/footer.php'; ?> 
</body>
</html>