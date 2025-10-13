<?php
// Wajib disalin: Memuat koneksi database Anda
require_once 'db_connect.php';

// Hapus: Tidak perlu memuat vendor/autoload.php jika tidak ada library eksternal lain yang dipakai.
// require_once 'vendor/autoload.php'; 

session_start();
$message = '';
$error_message = '';

// --- BAGIAN GOOGLE DIHAPUS ---

// 2. Tampilkan pesan sukses dari Registrasi
if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
    $message = '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md flex-shrink-0" role="alert">
                    <p class="font-bold">Registrasi Berhasil!</p>
                    <p>Akun Anda telah dibuat. Silakan login menggunakan email dan password Anda.</p>
                </div>';
}

// 3. LOGIKA PEMROSESAN LOGIN (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Cek user di database
        // PASTIKAN Anda juga mengambil kolom full_name untuk kebutuhan dashboard user
        $stmt = $conn->prepare("SELECT id, password_hash, role, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifikasi Password 
            // Cek apakah password_hash ada dan verifikasi password
            if (!empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
                
                // Login Berhasil
                session_regenerate_id(true); // Opsional: Tambahan keamanan
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name']; // Simpan full_name
                
                // ===========================================
                // ✅ KOREKSI: LOGIKA PENGALIHAN ROLE
                // ===========================================
                if ($user['role'] === 'admin') {
                    // Alihkan ke Dashboard Admin
                    header('Location: admin/orders.php'); // Mengganti dashboard.php ke orders.php
                    exit();
                } else {
                    // Alihkan ke Dashboard User (default)
                    header('Location: katalog_kostum.php'); 
                    exit();
                }
                
            } else {
                $error_message = "Email atau Password salah.";
            }
        } else {
            $error_message = "Email tidak terdaftar.";
        }
        $stmt->close(); // Tutup statement setelah selesai
    } else {
        $error_message = "Email dan Password wajib diisi.";
    }
}

// Hapus: Tidak perlu menghasilkan URL untuk tombol Google lagi

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Arunika Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Hapus overflow: hidden dari body agar konten bisa di-scroll jika melebihi vh */
        body {
            min-height: 100vh;
            background-color: #f7f7f7; 
        }
    </style>
</head>
<body class="flex items-center justify-center p-6 md:p-10">
    
    <main class="w-full h-full flex items-center justify-center">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <div class="hidden lg:block">
                    <img 
                        src="assets/images/login1.jpeg" 
                        alt="Taman Arunika dengan model kimono" 
                        class="h-full w-full object-cover min-h-[500px]"
                    >
                </div>

                <div class="p-8 md:p-12 flex flex-col justify-between min-h-[500px]">
                    <div class="flex flex-col">
                        <div class="text-center mb-6 flex-shrink-0">
                            <h2 class="text-3xl font-bold text-gray-800 serif-font">Selamat Datang di Arunika Rental</h2>
                            <p class="text-gray-500 mt-2">Silahkan login terlebih dahulu</p>
                        </div>
    
                        <?php echo $message; ?>
                        <?php if ($error_message): ?>
                            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md flex-shrink-0" role="alert">
                                <p><?php echo $error_message; ?></p>
                            </div>
                        <?php endif; ?>
    
                        </div>
                    
                    <form action="login.php" method="POST" class="flex flex-col justify-end">
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-600">*</span></label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-700 focus:border-red-700 transition duration-150"
                                placeholder="nama@contoh.com"
                            >
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-600">*</span></label>
                            <input type="password" id="password" name="password" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-700 focus:border-red-700 transition duration-150"
                                placeholder="Masukkan Password"
                            >
                            <div class="text-right mt-1">
                                <a href="forgot_password.php" class="text-sm text-red-700 hover:text-red-900 font-medium">Forgot Password?</a>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md mt-6"
                        >
                            Login
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm flex-shrink-0">
                        <p class="text-gray-600">Don't have an account? 
                            <a href="register.php" class="text-red-700 hover:text-red-900 font-semibold">Sign Up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>