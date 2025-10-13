<?php
require_once 'db_connect.php'; 
session_start();

// Inisialisasi variabel
$rental_id = (int)($_GET['rental_id'] ?? 0);
$user_id = $_SESSION['user_id'] ?? 0;
$rental_data = null;
$error_message = '';
$success_message = '';
$logout_url = 'logout.php'; // Pastikan ini mengarah ke file logout yang benar

// 1. Cek Login
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// FUNGSI WAJIB: formatRupiah()
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

// -----------------------------------------------------------
// 2. Ambil Detail Pesanan (Rental) dari Database
// -----------------------------------------------------------
if ($rental_id > 0) {
    // Gunakan COALESCE untuk memastikan rental_status tidak NULL/kosong saat dibaca
    $stmt = $conn->prepare(
        "SELECT r.total_price, COALESCE(r.rental_status, 'pending_payment') AS rental_status, 
                r.rental_date, r.return_date, c.name AS costume_name
         FROM rentals r
         JOIN costumes c ON r.costume_id = c.id
         WHERE r.id = ? AND r.user_id = ?"
    );
    $stmt->bind_param("ii", $rental_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $rental_data = $result->fetch_assoc();
    } else {
        $error_message = "Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.";
        $rental_id = 0; // Invalidasi ID agar form tidak muncul
    }
    $stmt->close();
} else {
    $error_message = "ID Pesanan tidak valid.";
}


// -----------------------------------------------------------
// 3. Logika Pemrosesan Form Pembayaran (POST)
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $rental_data) {
    
    // Validasi status pesanan: hanya proses jika masih 'pending_payment'
    if ($rental_data['rental_status'] !== 'pending_payment') {
        $error_message = "Pembayaran untuk pesanan ini sudah diproses.";
    } else {
        // Ambil data POST
        $payment_method = $_POST['payment_method'] ?? '';

        // Validasi input
        if (empty($payment_method) || $payment_method === 'Pilih Metode') {
            $error_message = "Silakan pilih metode pembayaran.";
        } 
        
        // Asumsi: Anda akan menerapkan logika upload file bukti transfer di sini.
        // Untuk saat ini, kita akan simpan tanpa file upload dan status akan menunggu konfirmasi admin.
        
        if (!$error_message) {
            
            // Variabel untuk disisipkan ke tabel payments
            $transaction_id = 'TRX-' . time() . '-' . $rental_id; // ID transaksi sederhana
            $amount = $rental_data['total_price'];
            $payment_status = 'submitted'; // Bukti pembayaran telah diserahkan (menunggu verifikasi admin)
            
            // Simpan detail pembayaran ke tabel 'payments'
            $stmt_p = $conn->prepare(
                "INSERT INTO payments 
                (rental_id, payment_method, transaction_id, amount, payment_status, payment_date) 
                VALUES (?, ?, ?, ?, ?, NOW())"
            );
            
            // i=integer, s=string, d=double/float
            $stmt_p->bind_param(
                "issds", 
                $rental_id, 
                $payment_method, 
                $transaction_id, 
                $amount, 
                $payment_status
            );

            if ($stmt_p->execute()) {
                // Perbarui status di tabel rentals
                $stmt_r = $conn->prepare(
                    "UPDATE rentals SET rental_status = 'waiting_confirmation' WHERE id = ?"
                );
                $stmt_r->bind_param("i", $rental_id);
                $stmt_r->execute();
                $stmt_r->close();

                $success_message = "Pembayaran berhasil diajukan! Menunggu konfirmasi dari Admin.";
                
                // Refresh data rental setelah update
                $rental_data['rental_status'] = 'waiting_confirmation'; 

            } else {
                $error_message = "Gagal menyimpan detail pembayaran: " . $stmt_p->error;
            }
            $stmt_p->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan #<?php echo htmlspecialchars($rental_id); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: serif; background-color: #f7f7f7; }
        .serif-font { font-family: serif; }
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

    <main class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-3xl font-bold text-gray-800 mb-8 serif-font text-center">Halaman Pembayaran</h1>

        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10">
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p><?php echo $success_message; ?></p>
                    <p class="mt-2 text-sm">Status Pesanan Anda: **<?php echo htmlspecialchars(strtoupper($rental_data['rental_status'])); ?>**</p>
                </div>
                <a href="user_dashboard.php" class="block w-full text-center bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200">Lihat Pesanan Anda</a>
            <?php endif; ?>


            <?php if ($rental_data && $rental_data['rental_status'] === 'pending_payment' && !$success_message): ?>
                <h2 class="text-2xl font-semibold text-red-700 mb-6 border-b pb-3">
                    Pesanan #<?php echo htmlspecialchars($rental_id); ?>
                </h2>

                <div class="space-y-2 mb-6 text-gray-700">
                    <p class="font-medium">Kostum: <span class="float-right font-semibold"><?php echo htmlspecialchars($rental_data['costume_name']); ?></span></p>
                    <p class="font-medium">Tanggal Sewa: <span class="float-right"><?php echo htmlspecialchars($rental_data['rental_date']); ?> s/d <?php echo htmlspecialchars($rental_data['return_date']); ?></span></p>
                    <p class="text-xl font-bold text-red-800 border-t pt-2 mt-2">
                        Total Pembayaran: 
                        <span class="float-right"><?php echo formatRupiah($rental_data['total_price']); ?></span>
                    </p>
                </div>
                
                <p class="text-gray-600 mb-4 text-center">Silakan transfer ke rekening berikut:</p>
                <div class="bg-gray-100 p-4 rounded-lg mb-6 text-center border">
                    <p class="font-bold text-lg text-gray-800">BCA: 1234567890</p>
                    <p class="text-sm text-gray-500">A.N. ARUNIKA RENTAL</p>
                </div>

                <form action="payment.php?rental_id=<?php echo urlencode($rental_id); ?>" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-4">
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                        <select id="payment_method" name="payment_method" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-red-500 focus:border-red-500">
                            <option>Pilih Metode</option>
                            <option value="Transfer Bank BCA">Transfer Bank BCA</option>
                            <option value="Dana">Dana</option>
                            <option value="Gopay">Gopay</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="proof_of_payment" class="block text-sm font-medium text-gray-700 mb-1">Unggah Bukti Pembayaran (Opsional)</label>
                        <input type="file" id="proof_of_payment" name="proof_of_payment" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 bg-white">
                        <p class="text-xs text-gray-500 mt-1">Jika Anda tidak mengunggah sekarang, status akan menunggu konfirmasi Admin.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md text-lg"
                    >
                        Saya Sudah Bayar & Konfirmasi
                    </button>
                    
                </form>
            <?php elseif ($rental_data && $rental_data['rental_status'] !== 'pending_payment' && !$success_message): ?>
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 mb-4 rounded-md text-center">
                    <p class="font-bold text-lg">Status Pesanan Anda:</p>
                    <p class="text-2xl font-extrabold mt-1"><?php echo htmlspecialchars(strtoupper($rental_data['rental_status'])); ?></p>
                    <p class="mt-3">Pesanan ini sudah tidak dalam tahap pembayaran. Silakan cek dashboard Anda.</p>
                </div>
                <a href="user_dashboard.php" class="block w-full text-center bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200">Lihat Pesanan Anda</a>
            <?php endif; ?>
            
            </div>

    </main>

    <?php require_once 'includes/footer.php'; ?> 
</body>
</html>