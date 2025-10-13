<?php
require_once 'db_connect.php'; // Pastikan file koneksi database Anda sudah ada

$message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';

    if (!empty($email)) {
        // 1. Cek apakah email terdaftar
        $stmt = $conn->prepare("SELECT id, full_name FROM users WHERE email = ? AND password_hash IS NOT NULL");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['id'];
            $full_name = $user['full_name'];
            
            // 2. Buat token unik dan waktu kedaluwarsa (misalnya 1 jam)
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

            // 3. Hapus token lama dan Simpan token baru di database
            // CATATAN: Anda perlu membuat tabel baru, misalnya 'password_resets'
            // Struktur tabel 'password_resets': id, user_id, token, expires, created_at
            
            $conn->begin_transaction();
            try {
                // Hapus token lama untuk user ini
                $stmt_delete = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
                $stmt_delete->bind_param("i", $user_id);
                $stmt_delete->execute();

                // Simpan token baru
                $stmt_insert = $conn->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
                $stmt_insert->bind_param("iss", $user_id, $token, $expires);
                $stmt_insert->execute();
                
                $conn->commit();

                // 4. KIRIM EMAIL (INI HANYA SIMULASI/PLACEHOLDER)
                // Link reset yang akan dikirim ke email user
                $reset_link = "http://localhost/arunika/reset_password.php?token=" . $token;

                // Link reset yang akan dikirim ke email user
                $reset_link = "http://localhost/arunika/reset_password.php?token=" . $token;

                // Ganti pesan yang ditampilkan (HANYA SIMULASI)
                $message = 'Link reset password telah dikirim ke email Anda. Silakan cek kotak masuk Anda. 
            <br>
            <div class="mt-2 font-bold">
                (Klik <a href="' . $reset_link . '" class="text-blue-600 hover:text-blue-800 underline">tautan ini</a> untuk atur ulang)
            </div>';
                
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Terjadi kesalahan saat membuat token. Silakan coba lagi. Error: " . $e->getMessage();
            }

        } else {
            $error_message = "Email tidak terdaftar atau merupakan akun Google Sign-in (tidak memiliki password).";
        }
    } else {
        $error_message = "Mohon masukkan alamat email Anda.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> /* ... (Styles yang sama dengan login/register) ... */ </style>
</head>
<body class="flex items-center justify-center p-6 md:p-10 min-h-screen bg-[#f7f7f7]">
    
    <main class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Lupa Kata Sandi?</h2>
            <p class="text-gray-500 text-center mb-8">Masukkan alamat email Anda untuk menerima tautan atur ulang kata sandi.</p>

            <?php if ($message): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert"><?php echo $message; ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="forgot_password.php" method="POST">
                <div class="mb-6">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email" required 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-700 focus:border-red-700 transition duration-150"
                        placeholder="nama@contoh.com"
                    >
                </div>

                <button type="submit"
                    class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md"
                >
                    Kirim Tautan Atur Ulang
                </button>
            </form>

            <div class="mt-4 text-center text-sm">
                <a href="login.php" class="text-red-700 hover:text-red-900 font-medium">Kembali ke Login</a>
            </div>
        </div>
    </main>
</body>
</html>