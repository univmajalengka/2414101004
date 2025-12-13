<?php
include 'koneksi.php';

// Query untuk mengambil semua data pesanan 
$query = "SELECT * FROM pesanan ORDER BY tanggal_pesan DESC";
$result = mysqli_query($koneksi, $query);

// Cek jika query gagal
if (!$result) {
    die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan Paket Wisata</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body">
    <header class="bg-teal-800 text-white shadow-lg fixed top-0 w-full z-50">
      <div class="container mx-auto flex justify-between items-center p-4">
        <div class="text-3xl font-bold">Situ Cipanten</div>

        <nav class="hidden md:flex space-x-6 text-base">
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Beranda</a
          >
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >About</a
          >
          <a
            href="index.php"
            class="hover:text-teal-200 transition duration-300"
            >Fasilitas Wisata</a
          >
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Paket Wisata</a
          >
          <a
            href="pemesanan.php"
            class="hover:text-teal-200 transition duration-300"
            >Pemesanan</a
          >
          <a
            href="modifikasi_pesanan.php"
            class="hover:text-teal-200 transition duration-300"
            >Modifikasi Pesanan
          </a>
          <a href="index.php" class="hover:text-teal-200 transition duration-300"
            >Galery</a
          >
        </nav>

        <button id="menu-button" class="md:hidden p-2 focus:outline-none">
          <svg
            class="w-8 h-8"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 6h16M4 12h16M4 18h16"
            ></path>
          </svg>
        </button>
      </div>

      <nav
        id="mobile-menu"
        class="hidden md:hidden bg-teal-700 w-full transition-all duration-300"
      >
        <a
          href="#hero"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Beranda</a
        >
        <a
          href="#about"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >About</a
        >
        <a
          href="#fasilitas"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Fasilitas Wisata</a
        >
        <a
          href="#paket"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Paket Wisata</a
        >
        <a
          href="#pemesanan"
          class="block py-3 px-4 text-center border-b border-teal-600 hover:bg-teal-600"
          >Pemesanan</a
        >
        <a href="#galery" class="block py-3 px-4 text-center hover:bg-teal-600"
          >Galery</a
        >
      </nav>
    </header>

<div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
    <h2 class="text-3xl font-bold text-teal-700 mb-8 border-b pb-4">Daftar Pesanan Paket Wisata</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-teal-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pemesan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. HP</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml. Hari</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jml. Peserta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Layanan Terpilih</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tagihan</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            
            <tbody class="bg-white divide-y divide-gray-200">
                <?php while ($row = mysqli_fetch_assoc($result)) : 

                    // Membuat daftar layanan yang dipilih
                    $layanan_terpilih = [];
                    if (isset($row['penginapan']) && $row['penginapan'] == 'Y') { $layanan_terpilih[] = 'Penginapan'; }
                    if (isset($row['transportasi']) && $row['transportasi'] == 'Y') { $layanan_terpilih[] = 'Transportasi'; }
                    if (isset($row['service_makan']) && $row['service_makan'] == 'Y') { $layanan_terpilih[] = 'Service/Makan'; }
                    $layanan_display = empty($layanan_terpilih) ? 'Tidak Ada' : implode(', ', $layanan_terpilih);
                ?>
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama_pemesan']) ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['nomor_hp']) ?></td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['waktu_hari']) ?> Hari</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['jumlah_peserta']) ?></td>

                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= $layanan_display ?></td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">Rp <?= number_format($row['harga_paket'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-teal-600 font-bold">Rp <?= number_format($row['jumlah_tagihan'], 0, ',', '.') ?></td>

                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center space-x-2">
                        <button onclick="goToEdit(<?= $row['id_pesanan'] ?>)" 
                                class="bg-indigo-500 text-white px-3 py-1 rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out">
                            Edit
                        </button>
                        
                        <button onclick="confirmDelete(<?= $row['id_pesanan'] ?>)" 
                                class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-700 transition duration-150 ease-in-out">
                            Hapus
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
                
                <?php if (mysqli_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">Belum ada data pesanan yang tersimpan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    
    function goToEdit(id) {
        window.location.href = 'edit_pesanan.php?id=' + id;
    }

    function confirmDelete(id) {
        // Tampilkan pop-up konfirmasi (sesuai spesifikasi) 
        if (confirm("Anda yakin akan hapus pesanan ini?")) {
            // Jika user klik OK, arahkan ke proses_hapus.php dengan ID 
            window.location.href = 'proses_hapus.php?id=' + id;
        }
    }
</script>

</body>
</html>