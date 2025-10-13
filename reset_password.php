<?php
require_once 'db_connect.php'; 

$message = '';
$error_message = '';
$token = $_GET['token'] ?? '';
$is_valid_token = false;
$user_id = null;

// 1. Cek validitas token di database
if (!empty($token)) {
    $current_time = date("Y-m-d H:i:s");
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires > ?");
    $stmt->bind_param("ss", $token, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $data = $result->fetch_assoc();
        $user_id = $data['user_id'];
        $is_valid_token = true;
    } else {
        $error_message = "Tautan reset tidak valid atau sudah kedaluwarsa.";
    }
} else {
    $error_message = "Token reset tidak ditemukan.";
}

// 2. LOGIKA PEMROSESAN PASSWORD BARU
if ($is_valid_token && $_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($new_password !== $confirm_password) {
        $error_message = "Kata sandi tidak cocok.";
    } elseif (strlen($new_password) < 8) {
        $error_message = "Kata sandi minimal 8 karakter.";
    } else {
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Mulai transaksi untuk memastikan keduanya sukses
        $conn->begin_transaction();
        try {
            // Update password hash di tabel users
            $stmt_update = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt_update->bind_param("si", $password_hash, $user_id);
            $stmt_update->execute();

            // Hapus token dari tabel password_resets
            $stmt_delete_token = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt_delete_token->bind_param("i", $user_id);
            $stmt_delete_token->execute();

            $conn->commit();
            
            // Redirect ke halaman login dengan pesan sukses
            header("Location: login.php?reset=success");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Gagal memperbarui kata sandi. Silakan coba lagi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style> /* ... (Styles yang sama) ... */ </style>
</head>
<body class="flex items-center justify-center p-6 md:p-10 min-h-screen bg-[#f7f7f7]">
    
    <main class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-2xl p-8 md:p-10">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-4">Atur Ulang Kata Sandi</h2>
            
            <?php if ($error_message): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <?php if ($is_valid_token): ?>
                <p class="text-gray-600 text-center mb-6">Masukkan kata sandi baru Anda.</p>
                <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-700 focus:border-red-700 transition duration-150"
                        >
                    </div>

                    <div class="mb-6">
                        <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-700 focus:border-red-700 transition duration-150"
                        >
                    </div>

                    <button type="submit"
                        class="w-full bg-red-700 hover:bg-red-800 text-white font-semibold py-3 rounded-lg transition duration-200 shadow-md"
                    >
                        Simpan Kata Sandi Baru
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-4 text-center text-sm">
                <a href="login.php" class="text-red-700 hover:text-red-900 font-medium">Kembali ke Login</a>
            </div>
        </div>
    </main>
</body>
</html>