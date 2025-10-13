<?php
require_once '../db_connect.php'; // Sesuaikan path jika db_connect.php ada di luar folder admin
session_start();

// ===================================================================
// 1. PENGAMANAN AKSES (Hanya untuk Admin)
// ===================================================================
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // mengalihkan ke halaman login jika belum login atau bukan admin
    header('Location: ../login.php'); 
    exit();
}

$admin_name = $_SESSION['full_name'] ?? 'Admin';
$logout_url = '../logout.php';

// FUNGSI WAJIB: formatRupiah() 
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

$message = '';

// ===================================================================
// 2. LOGIKA PEMROSESAN AKSI ADMIN (Verifikasi / Tolak)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $rental_id_to_process = (int)($_POST['rental_id'] ?? 0);

    if ($rental_id_to_process > 0) {
        if ($action === 'verify') {
            // A. VERIFIKASI PEMBAYARAN: Update 2 Tabel (Rentals dan Payments)
            
            // Mulai Transaksi untuk memastikan kedua query berhasil atau gagal bersamaan
            $conn->begin_transaction();
            try {
                // 1. Update status di tabel rentals menjadi 'booked'
                $stmt_r = $conn->prepare("UPDATE rentals SET rental_status = 'booked' WHERE id = ? AND rental_status = 'waiting_confirmation'");
                $stmt_r->bind_param("i", $rental_id_to_process);
                $stmt_r->execute();

                // 2. Update status di tabel payments menjadi 'lunas'
                $stmt_p = $conn->prepare("UPDATE payments SET payment_status = 'lunas' WHERE rental_id = ? AND payment_status = 'submitted'");
                $stmt_p->bind_param("i", $rental_id_to_process);
                $stmt_p->execute();
                
                // Jika kedua query berhasil, commit transaksi
                if ($stmt_r->affected_rows > 0 || $stmt_p->affected_rows > 0) {
                    $conn->commit();
                    $message = "<div class='bg-green-100 text-green-700 p-3 rounded-md'>Pembayaran Pesanan #{$rental_id_to_process} berhasil diverifikasi! Status menjadi BOOKED.</div>";
                } else {
                     // Jika tidak ada baris yang terpengaruh (mungkin status sudah berubah)
                    throw new Exception("Gagal memverifikasi. Status pesanan mungkin sudah berubah.");
                }
                
                $stmt_r->close();
                $stmt_p->close();

            } catch (Exception $e) {
                $conn->rollback();
                $message = "<div class='bg-red-100 text-red-700 p-3 rounded-md'>Error: " . $e->getMessage() . "</div>";
            }
            
        } elseif ($action === 'reject') {
            // B. TOLAK PEMBAYARAN: Update status di rentals menjadi 'pending_payment' (perlu pembayaran ulang)
            // Di dunia nyata, Anda mungkin juga perlu mengirim notifikasi.
             $stmt_r = $conn->prepare("UPDATE rentals SET rental_status = 'pending_payment' WHERE id = ?");
             $stmt_r->bind_param("i", $rental_id_to_process);
             $stmt_r->execute();
             $stmt_r->close();
             
             // Update status di payments menjadi 'gagal'
             $stmt_p = $conn->prepare("UPDATE payments SET payment_status = 'gagal' WHERE rental_id = ?");
             $stmt_p->bind_param("i", $rental_id_to_process);
             $stmt_p->execute();
             $stmt_p->close();
             
             $message = "<div class='bg-orange-100 text-orange-700 p-3 rounded-md'>Pembayaran Pesanan #{$rental_id_to_process} ditolak. Status dikembalikan ke PENDING_PAYMENT.</div>";
        }
    }
}

// ===================================================================
// 3. PENGAMBILAN DATA PESANAN UNTUK DASHBOARD
// ===================================================================
// Ambil pesanan yang statusnya WAITING_CONFIRMATION (menunggu verifikasi)
$stmt = $conn->prepare(
    "SELECT 
        r.id AS rental_id, 
        r.rental_date, 
        r.return_date, 
        r.total_price, 
        r.rental_status, 
        u.full_name AS user_name,
        c.name AS costume_name,
        p.payment_method,
        p.payment_date
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN costumes c ON r.costume_id = c.id
    LEFT JOIN payments p ON r.id = p.rental_id
    WHERE r.rental_status IN ('waiting_confirmation', 'pending_payment')
    ORDER BY r.created_at ASC"
);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Verifikasi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; background-color: #f7f7f7; }
        .serif-font { font-family: serif; }
    </style>
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

        <h2 class="text-3xl font-bold text-gray-800 mb-8 serif-font">Verifikasi Pesanan dan Pembayaran</h2>

        <?php echo $message; // Menampilkan pesan sukses/error setelah aksi ?>

        <?php if (empty($orders)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-md text-center mt-6">
                <p class="font-bold text-xl">Tidak ada pesanan baru yang menunggu verifikasi.</p>
                <p class="mt-2">Semua pembayaran sudah diverifikasi.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto shadow-xl rounded-lg mt-6">
                <table class="min-w-full bg-white">
                    <thead class="bg-red-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Pelanggan</th>
                            <th class="py-3 px-4 text-left">Kostum</th>
                            <th class="py-3 px-4 text-left">Total Bayar</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Metode Bayar</th>
                            <th class="py-3 px-4 text-left">Tanggal Sewa</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($orders as $order): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($order['rental_id']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($order['costume_name']); ?></td>
                            <td class="py-3 px-4 font-bold text-red-700"><?php echo formatRupiah($order['total_price']); ?></td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    <?php 
                                        $status = strtolower($order['rental_status']);
                                        if ($status == 'waiting_confirmation') echo 'bg-yellow-100 text-yellow-800';
                                        elseif ($status == 'pending_payment') echo 'bg-red-100 text-red-800';
                                        else echo 'bg-gray-100 text-gray-800';
                                    ?>">
                                    <?php echo htmlspecialchars(strtoupper($order['rental_status'])); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($order['payment_method'] ?? '-'); ?></td>
                            <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars(date('d M Y', strtotime($order['rental_date']))); ?> - <?php echo htmlspecialchars(date('d M Y', strtotime($order['return_date']))); ?></td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                
                                <?php if (strtolower($order['rental_status']) === 'waiting_confirmation'): ?>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="rental_id" value="<?php echo $order['rental_id']; ?>">
                                        <input type="hidden" name="action" value="verify">
                                        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 transition">
                                            ✅ Verifikasi
                                        </button>
                                    </form>
                                    <form method="POST" class="inline-block ml-2">
                                        <input type="hidden" name="rental_id" value="<?php echo $order['rental_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600 transition">
                                            ❌ Tolak
                                        </button>
                                    </form>
                                    <?php elseif (strtolower($order['rental_status']) === 'pending_payment'): ?>
                                    <span class="text-xs text-red-500">Menunggu Pelanggan Bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

    </main>
</body>
</html>