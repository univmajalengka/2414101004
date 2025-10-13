<?php
require_once 'db_connect.php'; 
session_start();

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_full_name = $_SESSION['full_name'] ?? 'User';
$logout_url = 'logout.php'; 

// FUNGSI WAJIB: formatRupiah()
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

// 1. Ambil semua pesanan (rentals) milik user
$stmt = $conn->prepare(
    "SELECT 
        r.id AS rental_id, 
        r.rental_date, 
        r.return_date, 
        r.total_price, 
        r.rental_status, 
        c.name AS costume_name,
        c.harga_sewa
    FROM rentals r
    JOIN costumes c ON r.costume_id = c.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$rentals = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fungsi untuk mendapatkan badge warna status
function getStatusBadge($status) {
    $status = strtolower($status);
    $badge_class = 'bg-gray-500';
    $text = '';
    
    switch ($status) {
        case 'pending_payment':
            $badge_class = 'bg-red-500';
            $text = 'Menunggu Pembayaran';
            break;
        case 'waiting_confirmation':
            $badge_class = 'bg-yellow-500';
            $text = 'Menunggu Konfirmasi Admin';
            break;
        case 'booked':
            $badge_class = 'bg-green-500';
            $text = 'Pesanan Dikonfirmasi';
            break;
        case 'rented':
            $badge_class = 'bg-blue-500';
            $text = 'Sedang Disewa';
            break;
        case 'returned':
            $badge_class = 'bg-gray-700';
            $text = 'Selesai';
            break;
        case 'canceled':
            $badge_class = 'bg-red-800';
            $text = 'Dibatalkan';
            break;
        default:
            $badge_class = 'bg-gray-500';
            $text = 'Tidak Diketahui';
            break;
    }
    return "<span class='inline-flex items-center px-3 py-1 rounded-full text-sm font-medium text-white {$badge_class}'>{$text}</span>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Arunika</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: serif; background-color: #f7f7f7; }
        .serif-font { font-family: serif; }
    </style>
</head>
<body>
    <header class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <a href="index.php" class="text-xl font-extrabold text-red-700 tracking-wider">ARUNIKA</a>
            <div class="flex items-center space-x-3">
                <a href="#" class="text-lg font-semibold text-gray-700 hover:text-red-700">Halo, <?php echo htmlspecialchars($user_full_name); ?></a>
                <a href="<?php echo $logout_url; ?>" class="px-4 py-2 text-sm font-semibold text-white bg-red-700 rounded-lg hover:bg-red-800 transition duration-200">Logout</a>
            </div>
        </div>
        <hr class="border-gray-200">
    </header> 

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pt-16">

        <h1 class="text-3xl font-bold text-gray-800 mb-8 serif-font">Dashboard Pesanan Anda</h1>

        <?php if (empty($rentals)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-md text-center">
                <p class="font-bold text-xl">Anda belum memiliki pesanan.</p>
                <p class="mt-2">Silakan kembali ke <a href="index.php" class="font-semibold underline hover:text-blue-800">Halaman Utama</a> untuk mulai menyewa kostum.</p>
            </div>
        <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($rentals as $rental): ?>
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="flex justify-between items-start mb-4 border-b pb-3">
                        <h2 class="text-xl font-bold text-red-700">Pesanan #<?php echo htmlspecialchars($rental['rental_id']); ?></h2>
                        <?php echo getStatusBadge($rental['rental_status']); ?>
                    </div>
                    
                    <div class="space-y-3 text-gray-700">
                        <p class="font-medium">Kostum: <span class="float-right font-semibold"><?php echo htmlspecialchars($rental['costume_name']); ?></span></p>
                        <p class="font-medium">Jadwal Sewa: <span class="float-right"><?php echo htmlspecialchars(date('d M Y', strtotime($rental['rental_date']))); ?> s/d <?php echo htmlspecialchars(date('d M Y', strtotime($rental['return_date']))); ?></span></p>
                        <p class="text-lg font-bold text-gray-800 border-t pt-2 mt-3">
                            Total Bayar: 
                            <span class="float-right text-red-800"><?php echo formatRupiah($rental['total_price']); ?></span>
                        </p>
                    </div>

                    <?php if (strtolower($rental['rental_status']) === 'pending_payment'): ?>
                        <div class="mt-4 pt-3 border-t">
                            <a href="payment.php?rental_id=<?php echo $rental['rental_id']; ?>" 
                               class="block w-full text-center bg-red-700 hover:bg-red-800 text-white font-semibold py-2 rounded-lg transition duration-200 text-sm">
                                Lanjutkan Pembayaran Sekarang
                            </a>
                        </div>
                    <?php endif; ?>

                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <?php 
        // Anda mungkin perlu membuat file footer.php jika belum ada
        // require_once 'includes/footer.php'; 
    ?> 
</body>
</html>