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

// Fungsi Helper
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

// Ambil ID dari URL
$rental_id = (int)($_GET['id'] ?? 0);
if ($rental_id === 0) {
    header('Location: orders.php?message=id_invalid'); 
    exit();
}

$message = '';
$error = '';

// =================================================================
// 1. PENGAMBILAN DATA PESANAN YANG AKAN DIEDIT
// (Mirip seperti order_detail.php, namun tanpa join payments)
// =================================================================
$stmt = $conn->prepare(
    "SELECT 
        r.id, r.user_id, r.costume_id, r.rental_date, r.return_date, r.total_price, r.rental_status,
        u.full_name AS user_name, 
        c.name AS costume_name, 
        c.harga_sewa
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN costumes c ON r.costume_id = c.id
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
// 2. LOGIKA MEMPROSES PEMBARUAN DATA (jika ada POST request)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_rental_date = $_POST['rental_date'];
    $new_return_date = $_POST['return_date'];
    $new_rental_status = $_POST['rental_status'];
    
    // Validasi dasar
    if (empty($new_rental_date) || empty($new_return_date) || empty($new_rental_status)) {
        $error = "Semua field wajib diisi.";
    } elseif (strtotime($new_rental_date) > strtotime($new_return_date)) {
        $error = "Tanggal kembali harus sama atau setelah tanggal ambil.";
    } else {
        try {
            // Hitung ulang total harga (jika diperlukan)
            $date1 = new DateTime($new_rental_date);
            $date2 = new DateTime($new_return_date);
            $interval = $date1->diff($date2);
            $duration = $interval->days + 1;
            $new_total_price = $order['harga_sewa'] * $duration;

            // Query UPDATE
            $update_stmt = $conn->prepare(
                "UPDATE rentals SET 
                    rental_date = ?, 
                    return_date = ?, 
                    total_price = ?, 
                    rental_status = ?, 
                    updated_at = NOW()
                WHERE id = ?"
            );
            $update_stmt->bind_param(
                "ssdsi", 
                $new_rental_date, 
                $new_return_date, 
                $new_total_price, 
                $new_rental_status, 
                $rental_id
            );

            if ($update_stmt->execute()) {
                $message = "Pesanan #{$rental_id} berhasil diperbarui menjadi status **" . strtoupper($new_rental_status) . "**.";
                // Muat ulang data pesanan untuk ditampilkan di formulir
                $order['rental_date'] = $new_rental_date;
                $order['return_date'] = $new_return_date;
                $order['rental_status'] = $new_rental_status;
                $order['total_price'] = $new_total_price;
            } else {
                $error = "Gagal memperbarui pesanan: " . $update_stmt->error;
            }
            $update_stmt->close();
        } catch (Exception $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Hitung lama sewa untuk tampilan informasi
$rental_date_obj = new DateTime($order['rental_date']);
$return_date_obj = new DateTime($order['return_date']);
$interval = $rental_date_obj->diff($return_date_obj);
$rental_duration = $interval->days + 1;

// Daftar semua status yang valid untuk dropdown
$status_options = ['pending_payment', 'waiting_confirmation', 'booked', 'rented', 'returned', 'canceled'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pesanan #<?php echo $rental_id; ?></title>
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

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Pesanan #<?php echo $rental_id; ?></h2>
        
        <div class="mb-6">
            <a href="order_detail.php?id=<?php echo $rental_id; ?>" class="text-red-700 hover:text-red-900 font-semibold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                Kembali ke Detail Pesanan
            </a>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow-xl rounded-lg p-8">
            <h3 class="text-xl font-bold text-red-700 mb-4">Informasi Pesanan</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 pb-4 border-b">
                <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                <p><strong>Kostum:</strong> <?php echo htmlspecialchars($order['costume_name']); ?></p>
                <p><strong>Harga Sewa/Hari:</strong> <?php echo formatRupiah($order['harga_sewa']); ?></p>
                <p><strong>Lama Sewa Saat Ini:</strong> <?php echo $rental_duration; ?> hari</p>
                <p class="text-xl font-bold col-span-2">TOTAL HARGA: <span class="text-red-700"><?php echo formatRupiah($order['total_price']); ?></span></p>
            </div>

            <form method="POST">
                <div class="space-y-4">
                    
                    <div>
                        <label for="rental_date" class="block text-sm font-medium text-gray-700">Tanggal Ambil (Sewa)</label>
                        <input type="date" name="rental_date" id="rental_date" value="<?php echo htmlspecialchars($order['rental_date']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>

                    <div>
                        <label for="return_date" class="block text-sm font-medium text-gray-700">Tanggal Kembali</label>
                        <input type="date" name="return_date" id="return_date" value="<?php echo htmlspecialchars($order['return_date']); ?>" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" required>
                    </div>
                    
                    <div>
                        <label for="rental_status" class="block text-sm font-medium text-gray-700">Status Pesanan</label>
                        <select name="rental_status" id="rental_status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 bg-white" required>
                            <?php foreach ($status_options as $status): ?>
                                <option value="<?php echo $status; ?>" 
                                    <?php echo (strtolower($order['rental_status']) === $status) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars(strtoupper(str_replace('_', ' ', $status))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition duration-150">
                            Perbarui Pesanan #<?php echo $rental_id; ?>
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </main>
</body>
</html>