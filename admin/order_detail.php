<?php
require_once '../db_connect.php'; 
session_start();

// Pengamanan Akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); 
    exit();
}

// Tambahkan definisi variabel untuk header
$admin_name = $_SESSION['full_name'] ?? 'Admin';
$logout_url = '../logout.php';

// Ambil ID dari URL
$rental_id = (int)($_GET['id'] ?? 0);
if ($rental_id === 0) {
    // Arahkan ke dashboard jika ID tidak valid, bukan orders.php
    header('Location: dashboard.php'); 
    exit();
}

$admin_name = $_SESSION['full_name'] ?? 'Admin';
$logout_url = '../logout.php';

// Fungsi Helper
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

// =================================================================
// PENGAMBILAN DATA DETAIL PESANAN - REVISI (Perbaikan p.image_path)
// =================================================================
$stmt = $conn->prepare(
    "SELECT 
        r.*, 
        u.full_name AS user_name, 
        u.email AS user_email, 
        u.no_telp,             
        c.name AS costume_name, 
        c.harga_sewa, 
        p.payment_method, 
        p.payment_date, 
        p.amount, 
        p.payment_status 
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN costumes c ON r.costume_id = c.id
    LEFT JOIN payments p ON r.id = p.rental_id
    WHERE r.id = ?"
);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: orders.php?message=not_found');
    exit();
}

// =================================================================
// LOGIKA PERHITUNGAN LAMA SEWA (RENTAL DURATION) - PERBAIKAN
// Kolom rental_duration tidak ada, jadi harus dihitung.
// =================================================================
$rental_date = new DateTime($order['rental_date']);
$return_date = new DateTime($order['return_date']);
// Menghitung selisih hari (+1 karena hari sewa dan hari kembali dihitung)
$interval = $rental_date->diff($return_date);
$rental_duration = $interval->days + 1; 
$order['rental_duration'] = $rental_duration;


function getStatusBadge($status) {
    $status = strtolower($status);
    $badge_class = 'bg-gray-500';
    switch ($status) {
        case 'pending_payment': $badge_class = 'bg-red-500'; break;
        case 'waiting_confirmation': $badge_class = 'bg-yellow-500'; break;
        case 'booked': $badge_class = 'bg-green-500'; break;
        case 'rented': $badge_class = 'bg-blue-500'; break;
        case 'returned': $badge_class = 'bg-gray-700'; break;
        case 'canceled': $badge_class = 'bg-red-800'; break;
    }
    return "<span class='inline-flex items-center px-4 py-2 rounded-full text-sm font-bold text-white {$badge_class}'>".htmlspecialchars(strtoupper(str_replace('_', ' ', $status)))."</span>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?php echo $rental_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: sans-serif; background-color: #f7f7f7; } </style>
</head>
<body>
    <header class="bg-red-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
            <h1 class="text-xl font-extrabold text-white tracking-wider">ADMIN ARUNIKA</h1>
        
            <nav class="hidden md:flex space-x-6">
                <a href="dashboard.php" class="text-white hover:text-gray-300 font-semibold 
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'border-b-2 border-white' : ''); ?>">
                    Verifikasi Pembayaran
                </a>
                <a href="orders.php" class="text-white hover:text-gray-300 font-semibold
                    <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'border-b-2 border-white' : ''); ?>">
                    Semua Pesanan
                </a>
            </nav>
            <div class="flex items-center space-x-3">
                <span class="text-lg font-semibold text-white">Halo, <?php echo htmlspecialchars($admin_name); ?></span>
                <a href="<?php echo $logout_url; ?>" class="px-4 py-2 text-sm font-semibold text-red-800 bg-white rounded-lg hover:bg-gray-200 transition duration-200">Logout</a>
            </div>
        </div>
    </header> 

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Detail Pesanan #<?php echo $rental_id; ?></h2>
        
        <div class="flex justify-between items-center mb-6">
            <a href="orders.php" class="text-red-700 hover:text-red-900 font-semibold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                Kembali ke Daftar Pesanan
            </a>
            <div class="space-x-4">
                <a href="order_edit.php?id=<?php echo $rental_id; ?>" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                    Edit Pesanan
                </a>
                <a href="print_invoice.php?id=<?php echo $rental_id; ?>" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg text-sm">
                    Cetak Invoice
                </a>
            </div>
        </div>

        <div class="bg-white shadow-xl rounded-lg p-8">
            <div class="flex justify-between items-start border-b pb-4 mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Status Pesanan</h3>
                <?php echo getStatusBadge($order['rental_status']); ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-b py-4">
                <div>
                    <h4 class="text-xl font-semibold mb-3 text-red-700">Informasi Pelanggan</h4>
                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                    <p><strong>Telepon:</strong> <?php echo htmlspecialchars($order['no_telp']); ?></p> 
                </div>
                <div>
                    <h4 class="text-xl font-semibold mb-3 text-red-700">Detail Sewa</h4>
                    <p><strong>Tgl. Ambil:</strong> <?php echo htmlspecialchars(date('d F Y', strtotime($order['rental_date']))); ?></p>
                    <p><strong>Tgl. Kembali:</strong> <?php echo htmlspecialchars(date('d F Y', strtotime($order['return_date']))); ?></p>
                    <p><strong>Kostum:</strong> <?php echo htmlspecialchars($order['costume_name']); ?></p>
                    <p><strong>Harga/hari:</strong> <?php echo formatRupiah($order['harga_sewa']); ?></p>
                    <p><strong>Lama Sewa:</strong> <?php echo htmlspecialchars($order['rental_duration']); ?> hari</p>
                </div>
            </div>

            <div class="py-4">
                <h4 class="text-xl font-semibold mb-3 text-red-700">Informasi Pembayaran</h4>
                <?php if ($order['payment_date']): ?>
                    <p><strong>Metode Bayar:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Tgl. Bayar:</strong> <?php echo htmlspecialchars(date('d F Y H:i:s', strtotime($order['payment_date']))); ?></p>
                    <p><strong>Status Bayar:</strong> 
                        <span class="font-bold text-<?php echo strtolower($order['payment_status'] ?? '') === 'lunas' ? 'green' : 'orange'; ?>-600">
                            <?php echo htmlspecialchars(strtoupper($order['payment_status'] ?? 'BELUM BAYAR')); ?>
                        </span>
                    </p>
                <?php endif; ?>
            </div>

            <div class="border-t pt-4 mt-4">
                <p class="text-3xl font-bold text-gray-900 flex justify-between">
                    <span>TOTAL TAGIHAN:</span>
                    <span class="text-red-700"><?php echo formatRupiah($order['total_price']); ?></span>
                </p>
            </div>
        </div>

    </main>
</body>
</html>