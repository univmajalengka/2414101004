<?php
require_once 'db_connect.php'; 
session_start();

// memastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

/**
 * Fungsi untuk memformat angka menjadi format Rupiah.
 */
function formatRupiah($number) {
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

$logout_url = 'logout.php'; 
$kostum_id = (int)($_GET['id'] ?? 0); 
$kostum_pilihan = null;

// 1. Ambil Data Kostum dari Database berdasarkan ID
if ($kostum_id > 0) {
    $stmt = $conn->prepare("SELECT id, name, deskripsi, harga_sewa, stock, image_url FROM costumes WHERE id = ?");
    $stmt->bind_param("i", $kostum_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $kostum_pilihan = $result->fetch_assoc();
        
        // Mapping data
        $nama_kostum = $kostum_pilihan['name'];
        $harga = $kostum_pilihan['harga_sewa'];
        $gambar = $kostum_pilihan['image_url'];
        $deskripsi = $kostum_pilihan['deskripsi'];
        $stok = $kostum_pilihan['stock'];

    } else {
        // Kostum tidak ditemukan
        header('Location: katalog_kostum.php');
        exit();
    }
} else {
    // ID tidak valid
    header('Location: katalog_kostum.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail <?php echo htmlspecialchars($nama_kostum); ?></title>
    
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

        <h1 class="text-3xl font-bold text-gray-800 mb-8 serif-font">Detail Kostum: <?php echo htmlspecialchars($nama_kostum); ?></h1>

        <div class="bg-white rounded-xl shadow-lg p-6 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="flex justify-center">
                    <img 
                        src="<?php echo htmlspecialchars($gambar); ?>" 
                        alt="<?php echo htmlspecialchars($nama_kostum); ?>" 
                        class="w-full max-w-md h-auto object-cover rounded-xl border border-gray-200 shadow-md aspect-square"
                    >
                </div>
                
                <div class="flex flex-col justify-between">
                    <div>
                        <h2 class="text-4xl font-extrabold text-red-700 serif-font mb-4">
                            <?php echo htmlspecialchars($nama_kostum); ?>
                        </h2>
                        <p class="text-2xl font-bold text-gray-800 mb-6">
                            Harga Sewa: <span class="text-red-700"><?php echo formatRupiah($harga); ?></span> / Hari
                        </p>

                        <h3 class="text-xl font-semibold text-gray-700 mb-3 border-b pb-1">Deskripsi</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">
                            <?php echo htmlspecialchars($deskripsi); ?>
                        </p>
                        
                        <div class="mb-6">
                            <span class="text-lg font-semibold text-gray-700">Stok Tersedia: </span>
                            <span class="text-lg font-bold <?php echo ($stok > 0) ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo htmlspecialchars($stok); ?> Set
                            </span>
                        </div>
                    </div>

                    <div>
                        <?php if ($stok > 0): ?>
                            <a href="form_pemesanan.php?kostum_id=<?php echo $kostum_id; ?>"
                                class="w-full md:w-auto inline-block text-center bg-red-700 text-white font-semibold py-3 px-8 rounded-lg hover:bg-red-800 transition duration-200 shadow-lg text-lg"
                            >
                                Pesan Kostum Ini
                            </a>
                        <?php else: ?>
                            <button disabled class="w-full md:w-auto bg-gray-400 text-white font-semibold py-3 px-8 rounded-lg cursor-not-allowed text-lg">
                                Stok Habis
                            </button>
                        <?php endif; ?>
                        
                        <a href="katalog_kostum.php" class="mt-4 md:mt-0 md:ml-4 inline-block text-center text-red-700 border border-red-700 font-semibold py-3 px-8 rounded-lg hover:bg-red-50 transition duration-200">
                            Kembali ke Katalog
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <?php require_once 'includes/footer.php'; ?> 
</body>
</html>