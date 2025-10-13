<?php
require_once 'db_connect.php'; 
session_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$kostum_id = (int)($_GET['kostum_id'] ?? 0); 

$message = '';
$error_message = '';
$harga_sewa = 0; // Inisialisasi
$nama_kostum_dipilih = ''; // Inisialisasi
$stok_tersedia = 0; // Inisialisasi
$logout_url = 'logout.php'; // Inisialisasi variabel ini jika belum ada

// FUNGSI WAJIB
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}


// ------------------------------------------------------------------
// A. LOGIKA PENGAMBILAN DATA KOSTUM (BARU DITAMBAHKAN)
// ------------------------------------------------------------------
if ($kostum_id > 0) {
    $stmt_k = $conn->prepare("SELECT name, harga_sewa, stock FROM costumes WHERE id = ?");
    $stmt_k->bind_param("i", $kostum_id);
    $stmt_k->execute();
    $result_k = $stmt_k->get_result();

    if ($result_k->num_rows === 1) {
        $kostum_data = $result_k->fetch_assoc();
        $nama_kostum_dipilih = $kostum_data['name'];
        $harga_sewa = $kostum_data['harga_sewa'];
        $stok_tersedia = $kostum_data['stock'];
    } else {
        // Jika ID kostum di URL tidak valid
        $error_message = "Kostum tidak ditemukan.";
        // Atau, alihkan kembali ke katalog
        // header('Location: katalog_kostum.php'); exit();
    }
    $stmt_k->close();
} else {
     // Jika tidak ada kostum_id, alihkan ke katalog
     header('Location: katalog_kostum.php'); 
     exit();
}


// ------------------------------------------------------------------
// B. LOGIKA PENGAMBILAN DATA PENGGUNA (Kode Anda yang sudah direvisi)
// ------------------------------------------------------------------
$user_data = [];
// Pastikan ID pengguna valid 
if ($user_id > 0) { 
    $stmt = $conn->prepare("SELECT full_name, email, no_telp, role FROM users WHERE id = ?");
    
    // ... (sisa kode user data Anda) ...
    // Saya berasumsi sisa kode ini sudah benar dan terisi.
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $user_data = $result->fetch_assoc();
        } else {
            session_destroy();
            header('Location: login.php?error=invalid_session');
            exit();
        }
    } else {
        die('MySQL execute error: ' . $stmt->error);
    }
    $stmt->close();
} else {
    // Jika user_id di session 0 atau tidak valid
    session_destroy();
    header('Location: login.php?error=no_user_id');
    exit();
}

// ===================================================================
// D. LOGIKA PEMROSESAN FORMULIR POST (Saat Tombol "Konfirmasi & Bayar" diklik)
// ===================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil dan sanitasi data input dari form
    $tgl_mulai = $_POST['tgl_mulai'] ?? null;
    $tgl_selesai = $_POST['tgl_selesai'] ?? null;
    $jumlah = (int)($_POST['jumlah'] ?? 0);
    
    // Pastikan semua data wajib terisi
    if (!$tgl_mulai || !$tgl_selesai || $jumlah <= 0) {
        $error_message = "Semua kolom tanggal dan jumlah harus diisi dengan benar.";
    } 
    
    // Validasi Tanggal
    $date_mulai = new DateTime($tgl_mulai);
    $date_selesai = new DateTime($tgl_selesai);
    $today = new DateTime(date('Y-m-d'));

    if ($date_mulai < $today) {
        $error_message = "Tanggal mulai sewa tidak boleh kurang dari hari ini.";
    } elseif ($date_mulai > $date_selesai) {
        $error_message = "Tanggal selesai sewa harus setelah atau sama dengan tanggal mulai sewa.";
    } 
    
    // Validasi Stok
    if ($jumlah > $stok_tersedia) {
        $error_message = "Jumlah pemesanan melebihi stok yang tersedia.";
    }

    // 2. Jika tidak ada error, hitung total harga
    if (!$error_message) {
        $interval = $date_mulai->diff($date_selesai);
        // Hitung durasi hari (termasuk hari mulai dan hari selesai)
        $diff_days = $interval->days + 1; 

        // Hitung total harga
        // $harga_sewa sudah diambil dari DB (pastikan di bagian A sudah benar)
        $total_price = $harga_sewa * $jumlah * $diff_days;

        // 3. Simpan data ke tabel rentals
        $rental_status = 'pending_payment'; // <-- HARUS ADA BARIS INI ATAU SEJENISNYA

        // Pastikan variabel $kostum_id dan $user_id sudah ada dan benar
        $stmt_i = $conn->prepare("INSERT INTO rentals (user_id, costume_id, rental_date, return_date, total_price, rental_status) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt_i->bind_param(
            "iissds", // i=integer, s=string/date, d=double/float
            $user_id, 
            $kostum_id, 
            $tgl_mulai, 
            $tgl_selesai, 
            $total_price, 
            $rental_status
        );

        if ($stmt_i->execute()) {
            $last_rental_id = $conn->insert_id;

            // 4. Setelah berhasil, alihkan ke halaman pembayaran
            //    Anda dapat membuat halaman baru (misalnya: payment.php)
            //    dan mengirimkan ID pesanan
            header("Location: payment.php?rental_id=" . $last_rental_id);
            exit();

        } else {
            $error_message = "Gagal menyimpan pemesanan ke database: " . $stmt_i->error;
        }
        $stmt_i->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan: <?php echo htmlspecialchars($nama_kostum_dipilih); ?></title>
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

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <h1 class="text-3xl font-bold text-gray-800 mb-8 serif-font text-center">Formulir Pemesanan Kostum</h1>

        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10">
            
            <h2 class="text-2xl font-semibold text-red-700 mb-6 border-b pb-3">
                Kostum: <?php echo htmlspecialchars($nama_kostum_dipilih); ?> 
                (Harga: <?php echo formatRupiah($harga_sewa); ?> / Hari)
            </h2>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="form_pemesanan.php?kostum_id=<?php echo urlencode($kostum_id); ?>" method="POST">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user_data['full_name'] ?? ''); ?>" required readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 bg-gray-100 cursor-not-allowed">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" required readonly class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-3 bg-gray-100 cursor-not-allowed">
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2 mt-6">Periode Sewa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Sewa</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" required 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-red-500 focus:border-red-500"
                        >
                    </div>
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Sewa</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" required 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-red-500 focus:border-red-500"
                        >
                    </div>
                </div>

                <div class="mb-8">
                    <label for="jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kostum (Stok tersedia: <?php echo htmlspecialchars($stok_tersedia ?? 'N/A'); ?>)</label>
                    <input type="number" id="jumlah" name="jumlah" min="1" max="<?php echo htmlspecialchars($stok_tersedia ?? 1); ?>" value="1" required 
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-3 focus:ring-red-500 focus:border-red-500"
                    >
                </div>
                
                <div class="bg-red-50 p-4 rounded-lg border border-red-200 mb-6">
                    <p class="text-gray-700 font-semibold text-lg mb-1">Harga per Hari per Kostum: <span class="float-right text-red-700"><?php echo formatRupiah($harga_sewa); ?></span></p>
                    <p class="text-gray-700 font-semibold text-lg border-t pt-2 mt-2 border-red-200">Total Estimasi Biaya: <span id="total_biaya" class="float-right text-red-800 font-extrabold">Rp. 0</span></p>
                </div>

                <button type="submit"
                    class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md text-lg"
                >
                    Konfirmasi & Bayar
                </button>
                
                <div class="mt-4 text-center text-sm">
                    <a href="katalog_kostum.php" class="text-red-700 hover:text-red-900 font-medium">Batalkan Pemesanan</a>
                </div>

            </form>
            </div>

    </main>

    <?php require_once 'includes/footer.php'; ?> 
    
    <script>
        // Fungsi untuk format angka ke Rupiah
        function formatRupiahJS(angka) {
            var number_string = angka.toString(),
                sisa    = number_string.length % 3,
                rupiah  = number_string.substr(0, sisa),
                ribuan  = number_string.substr(sisa).match(/\d{3}/g);
                
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return 'Rp. ' + rupiah;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hargaSewa = <?php echo $harga_sewa; ?>; 
            const tglMulai = document.getElementById('tgl_mulai');
            const tglSelesai = document.getElementById('tgl_selesai');
            const jumlah = document.getElementById('jumlah');
            const totalBiayaSpan = document.getElementById('total_biaya');

            // Fungsi perhitungan utama
            function hitungTotal() {
                const mulai = tglMulai.value;
                const selesai = tglSelesai.value;
                const jumlahKostum = parseInt(jumlah.value);

                if (mulai && selesai && jumlahKostum > 0) {
                    const date1 = new Date(mulai);
                    const date2 = new Date(selesai);
                    
                    // Hitung durasi hari (termasuk hari mulai dan hari selesai)
                    const diffTime = Math.abs(date2 - date1);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; 

                    if (date1 > date2) {
                        totalBiayaSpan.textContent = "Tanggal Tidak Valid";
                        return;
                    }
                    
                    const total = hargaSewa * jumlahKostum * diffDays;
                    totalBiayaSpan.textContent = formatRupiahJS(total);
                } else {
                    totalBiayaSpan.textContent = formatRupiahJS(0);
                }
            }

            // Tambahkan listener pada perubahan input
            tglMulai.addEventListener('change', hitungTotal);
            tglSelesai.addEventListener('change', hitungTotal);
            jumlah.addEventListener('input', hitungTotal);

            // Set tanggal minimum hari ini
            const today = new Date().toISOString().split('T')[0];
            tglMulai.setAttribute('min', today);
            tglSelesai.setAttribute('min', today);

            // Jalankan perhitungan saat halaman dimuat (jika ada nilai default)
            hitungTotal();
        });
    </script>
</body>
</html>