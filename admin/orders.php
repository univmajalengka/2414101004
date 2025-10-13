<?php
require_once '../db_connect.php'; 
session_start();

// Pengamanan Akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); 
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

// LOGIKA HAPUS PESANAN (DELETE)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $rental_id = (int)$_GET['id'];
    $delete_success = false;

    // Mulai Transaksi
    $conn->begin_transaction();
    
    // Inisialisasi statement di luar try untuk memastikan mereka bisa ditutup di luar
    $stmt_p = null;
    $stmt_r = null;
    
    try {
        // Hapus data pembayaran terkait (jika ada)
        $stmt_p = $conn->prepare("DELETE FROM payments WHERE rental_id = ?");
        $stmt_p->bind_param("i", $rental_id);
        $stmt_p->execute();

        // Hapus pesanan dari tabel rentals
        $stmt_r = $conn->prepare("DELETE FROM rentals WHERE id = ?");
        $stmt_r->bind_param("i", $rental_id);
        $stmt_r->execute();
        
        if ($stmt_r->affected_rows > 0) {
            $conn->commit();
            $delete_success = true;
        } else {
            // Jika pesanan tidak ditemukan di rentals, rollback dan berikan pesan error.
            $conn->rollback();
            throw new Exception("Pesanan tidak ditemukan atau gagal dihapus.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        // Redirect dengan pesan error
        header('Location: orders.php?message=delete_error');
        exit();
    } finally {
        // Selalu tutup statement, terlepas dari keberhasilan try/catch
        if ($stmt_p) $stmt_p->close();
        if ($stmt_r) $stmt_r->close();
    }

    // Redirect setelah sukses (harus di luar blok try/catch/finally jika ada output buffer)
    if ($delete_success) {
        header('Location: orders.php?message=delete_success');
        exit();
    }
}

// PENGAMBILAN DATA SEMUA PESANAN
$stmt = $conn->prepare(
    "SELECT 
        r.id AS rental_id, 
        r.rental_date, 
        r.return_date, 
        r.total_price, 
        r.rental_status, 
        u.full_name AS user_name,
        c.name AS costume_name
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN costumes c ON r.costume_id = c.id
    ORDER BY r.created_at DESC"
);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fungsi untuk status badge
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
    return "<span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-medium text-white {$badge_class}'>".htmlspecialchars(strtoupper(str_replace('_', ' ', $status)))."</span>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Semua Pesanan Sewa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> body { font-family: sans-serif; background-color: #f7f7f7; } </style>
</head>
<body>
    <?php include 'admin_header.php'; // Kita akan buat file ini setelahnya ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h2 class="text-3xl font-bold text-gray-800 mb-8">Manajemen Semua Pesanan</h2>
        
        <?php if (isset($_GET['message']) && $_GET['message'] === 'delete_success'): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p>Pesanan berhasil dihapus secara permanen.</p></div>
        <?php elseif (isset($_GET['message']) && $_GET['message'] === 'delete_error'): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>Gagal menghapus pesanan.</p></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded-md text-center mt-6">
                <p class="font-bold text-xl">Tidak ada riwayat pesanan sewa.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto shadow-xl rounded-lg mt-6">
                <table class="min-w-full bg-white">
                    <thead class="bg-red-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">ID</th>
                            <th class="py-3 px-4 text-left">Pelanggan</th>
                            <th class="py-3 px-4 text-left">Kostum</th>
                            <th class="py-3 px-4 text-left">Tgl Sewa</th>
                            <th class="py-3 px-4 text-left">Total</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($orders as $order): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 font-semibold"><?php echo htmlspecialchars($order['rental_id']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($order['costume_name']); ?></td>
                            <td class="py-3 px-4 text-sm"><?php echo htmlspecialchars(date('d/m/Y', strtotime($order['rental_date']))); ?></td>
                            <td class="py-3 px-4 font-bold"><?php echo formatRupiah($order['total_price']); ?></td>
                            <td class="py-3 px-4"><?php echo getStatusBadge($order['rental_status']); ?></td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="order_detail.php?id=<?php echo $order['rental_id']; ?>" class="text-blue-600 hover:text-blue-800 font-semibold text-sm mr-3">Detail</a>
                                
                                <a href="order_edit.php?id=<?php echo $order['rental_id']; ?>" class="text-green-600 hover:text-green-800 font-semibold text-sm mr-3">Edit</a>
                                
                                <a href="orders.php?action=delete&id=<?php echo $order['rental_id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus pesanan #<?php echo $order['rental_id']; ?>? Aksi ini tidak bisa dibatalkan.')"
                                   class="text-red-600 hover:text-red-800 font-semibold text-sm">Hapus</a>
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