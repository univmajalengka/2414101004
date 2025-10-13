<?php
// 1. Mulai sesi
session_start();

// 2. Hapus semua variabel sesi
$_SESSION = array();

// 3. cookies sesi 
// periksa cookie sesi , lalu hapus.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. menghancurkan sesi
session_destroy();

// 5. mengalihkan pengguna kembali ke halaman login atau halaman utama
header("Location: index.php");
exit;
?>