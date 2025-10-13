<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      
define('DB_PASS', '');          
define('DB_NAME', 'arunika');  

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Opsional: Atur timezone jika diperlukan
date_default_timezone_set('Asia/Jakarta');
?>