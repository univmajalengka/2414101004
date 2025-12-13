<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $id_pesanan = mysqli_real_escape_string($koneksi, $_POST['id_pesanan']);
    
    // Semua key $_POST ini sudah cocok dengan name attribute di form yang sudah direvisi
    $nama_pemesan = mysqli_real_escape_string($koneksi, $_POST['nama_pemesan']);
    $nomor_hp = mysqli_real_escape_string($koneksi, $_POST['nomor_hp']);
    $tanggal_pesan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pesan']);
    
    // Perbaikan: Hapus argumen 'string:' yang tidak perlu dan hapus baris duplikat
    $waktu_hari = mysqli_real_escape_string($koneksi, $_POST['waktu_hari']);
    
    $jumlah_peserta = mysqli_real_escape_string($koneksi, $_POST['jumlah_peserta']);
    
    // Ambil data dari hidden input (setelah dihitung oleh JS)
    $harga_paket = mysqli_real_escape_string($koneksi, $_POST['hidden_harga_paket']);
    $jumlah_tagihan = mysqli_real_escape_string($koneksi, $_POST['hidden_jumlah_tagihan']);

    // Proses Checkbox Layanan
    $layanan = $_POST['layanan'] ?? [];
    $penginapan = in_array('Penginapan', $layanan) ? 'Y' : 'N';
    $transportasi = in_array('Transportasi', $layanan) ? 'Y' : 'N';
    $service_makan = in_array('Service/Makan', $layanan) ? 'Y' : 'N';
    
    // Query UPDATE
    $query = "UPDATE pesanan SET 
                nama_pemesan = '$nama_pemesan',      
                nomor_hp = '$nomor_hp',              
                tanggal_pesan = '$tanggal_pesan',    
                waktu_hari = '$waktu_hari',          
                penginapan = '$penginapan',
                transportasi = '$transportasi',
                service_makan = '$service_makan',
                jumlah_peserta = '$jumlah_peserta',
                harga_paket = '$harga_paket',
                jumlah_tagihan = '$jumlah_tagihan'
            WHERE id_pesanan = '$id_pesanan'";

    if (mysqli_query($koneksi, $query)) {
        // Jika update berhasil, arahkan kembali ke daftar pesanan
        header("Location: modifikasi_pesanan.php?status=updated");
        exit();
    } else {
        // Jika terjadi error
        echo "Error saat menyimpan perubahan: " . mysqli_error($koneksi);
    }
} else {
    // Jika diakses tanpa POST
    header("Location: modifikasi_pesanan.php");
    exit();
}

mysqli_close($koneksi);
?>