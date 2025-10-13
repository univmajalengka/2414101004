<?php

// Hapus: Tidak perlu memuat vendor/autoload.php jika tidak ada library eksternal lain yang dipakai.
// require_once 'vendor/autoload.php';
require_once 'db_connect.php';

session_start();
$error_message = '';

// --- BAGIAN GOOGLE CLIENT & LOGIKA AUTENTIKASI GOOGLE DIHAPUS TOTAL ---

// 3. LOGIKA UNTUK REGISTRASI FORM BIASA (FORM POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $no_telp = $_POST['no_telp'] ?? ''; 
    
    if (!empty($full_name) && !empty($email) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        // Mengambil username dari bagian email sebelum '@'
        $username = explode('@', $email)[0];
        $role = 'user';

        // Cek duplikasi email
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error_message = "Email ini sudah terdaftar. Silakan login.";
        } else {
            // Insert user (google_id sekarang tidak diperlukan)
            $sql = "INSERT INTO users (username, email, password_hash, full_name, role, no_telp) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $username, $email, $password_hash, $full_name, $role, $no_telp);
            
            if ($stmt->execute()) {
                // Redirect ke halaman login dengan pesan sukses
                header("Location: login.php?registration=success");
                exit();
            } else {
                $error_message = "Pendaftaran gagal: " . $conn->error;
            }
        }
    } else {
        $error_message = "Nama, Email, dan Password wajib diisi.";
    }
}

// Hapus: Logika untuk Tombol Sign-in di HTML (tidak perlu $authUrl)

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Arunika Sewa Kimono</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f7f7f7;
        }
    </style>
</head>
<body class="flex flex-col min-h-screen">
    
    <main class="flex-grow flex items-center justify-center p-6 md:p-10">
        
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <div class="hidden lg:block">
                    <img 
                        src="assets/images/form.jpeg" 
                        alt="Taman Arunika dengan model kimono" 
                        class="h-full w-full object-cover"
                    >
                </div>

                <div class="p-8 md:p-12">
                    <div class="text-center mb-6">
                        <h2 class="text-3xl font-bold text-gray-800">Selamat Datang di Arunika Rental</h2>
                        <p class="text-gray-500 mt-2">Silahkan buat akun terlebih dahulu</p>
                    </div>

                    <?php if ($error_message): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                            <p><?php echo $error_message; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name <span class="text-red-600">*</span></label>
                            <input type="text" id="name" name="full_name" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition duration-150"
                                placeholder="Masukkan Nama Lengkap"
                            >
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-600">*</span></label>
                            <input type="email" id="email" name="email" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition duration-150"
                                placeholder="nama@email.com"
                            >
                        </div>
                        
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password <span class="text-red-600">*</span></label>
                            <input type="password" id="password" name="password" required 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition duration-150"
                                placeholder="Minimal 8 karakter"
                            >
                        </div>

                        <div class="mb-6">
                            <label for="no_telp" class="block text-sm font-semibold text-gray-700 mb-2">No. Telepon</label>
                            <input type="text" id="no_telp" name="no_telp" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition duration-150"
                                placeholder="Cth: 081234567890"
                            >
                        </div>

                        <button type="submit"
                            class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md"
                        >
                            Create Account
                        </button>
                    </form>

                    <div class="mt-6 text-center text-sm">
                        <p class="text-gray-600">Already have an account? 
                            <a href="login.php" class="text-red-700 hover:text-red-900 font-semibold">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>