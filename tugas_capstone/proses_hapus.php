<?php
include 'koneksi.php';

// Pastikan ID diterima dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query DELETE
    $query = "DELETE FROM pesanan WHERE id_pesanan = '$id'";
    
    if (mysqli_query($koneksi, $query)) {
        // Jika penghapusan berhasil
        header("Location: modifikasi_pesanan.php?status=deleted");
        exit();
    } else {
        // Jika terjadi error
        echo "Error: " . $query . "<br>" . mysqli_error($koneksi);
    }
} else {
    // Jika tidak ada ID yang diberikan
    header("Location: modifikasi_pesanan.php");
    exit();
}

mysqli_close($koneksi);
?>