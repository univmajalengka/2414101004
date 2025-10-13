<?php
require_once '../db_connect.php'; 
session_start();

// Pengamanan Akses 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
}

$rental_id = (int)($_GET['id'] ?? 0);
if ($rental_id === 0) {
    die("ID Pesanan tidak valid.");
}

// Fungsi Helper
function formatRupiah($number) {
    if (!is_numeric($number)) {
        return 'Rp. 0';
    }
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

// PENGAMBILAN DATA INVOICE (REVISI)
$stmt = $conn->prepare(
    "SELECT 
        r.*, 
        u.full_name AS user_name, 
        u.email AS user_email, 
        u.no_telp,              
        c.name AS costume_name, 
        c.harga_sewa, 
        p.payment_method, 
        p.payment_date, 
        p.payment_status
    FROM rentals r
    JOIN users u ON r.user_id = u.id
    JOIN costumes c ON r.costume_id = c.id
    LEFT JOIN payments p ON r.id = p.rental_id
    WHERE r.id = ?"
);
$stmt->bind_param("i", $rental_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice_data = $result->fetch_assoc();
$stmt->close();

if (!$invoice_data) {
    die("Data Invoice tidak ditemukan.");
}

// Hitung Lama Sewa
$rental_date = new DateTime($invoice_data['rental_date']);
$return_date = new DateTime($invoice_data['return_date']);
$duration = $rental_date->diff($return_date)->days + 1; // +1 untuk hitungan hari penuh
$subtotal = $duration * $invoice_data['harga_sewa'];
$diskon = 0; 
$grand_total = $invoice_data['total_price'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - #<?php echo $rental_id; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        @media print {
            body { 
                margin: 0; 
                padding: 0;
            }
        }
    </style>
</head>
<body class="max-w-3xl mx-auto border-4 border-gray-300 rounded-3xl p-8">

    <div class="text-center border-b-4 border-gray-300 pb-4 mb-6">
        <h1 class="text-4xl font-extrabold text-red-700">ARUNIKA EATERY</h1>
        <p class="text-gray-600">Jl. Cigugur – Palutungan, Cisantana, Kecamatan Cigugur, Kabupaten Kuningan, Jawa Barat.  Telp: 0895-0707-1035</p>
    </div>

    <div class="flex justify-between mb-8">
        <div>
            <h2 class="text-xl font-bold mb-2">INVOICE PENYEWAAN</h2>
            <p><strong>Nomor Invoice:</strong> INV/<?php echo date('Ymd', strtotime($invoice_data['created_at'])); ?>/<?php echo $rental_id; ?></p>
            <p><strong>Tanggal Transaksi:</strong> <?php echo date('d F Y', strtotime($invoice_data['created_at'])); ?></p>
            <p><strong>Status Bayar:</strong> 
                <span class="font-extrabold text-<?php echo strtolower($invoice_data['payment_status']) === 'lunas' ? 'green' : 'orange'; ?>-700">
                    <?php echo htmlspecialchars(strtoupper($invoice_data['payment_status'] ?? 'BELUM BAYAR')); ?>
                </span>
            </p>
        </div>
        <div class="text-right">
            <h3 class="text-lg font-bold mb-2">PENYEWA :</h3>
            <p class="font-semibold"><?php echo htmlspecialchars($invoice_data['user_name']); ?></p>
            <p><?php echo htmlspecialchars($invoice_data['user_email']); ?></p>
            <p><?php echo htmlspecialchars($invoice_data['no_telp']); ?></p>
        </div>
    </div>

    <table class="w-full border-collapse border border-gray-400 mb-8">
        <thead class="bg-gray-200">
            <tr>
                <th class="border border-gray-400 p-2 text-left">Deskripsi Kostum</th>
                <th class="border border-gray-400 p-2">Lama Sewa</th>
                <th class="border border-gray-400 p-2 text-right">Harga Satuan</th>
                <th class="border border-gray-400 p-2 text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border border-gray-400 p-2 font-semibold">
                    Kostum: <?php echo htmlspecialchars($invoice_data['costume_name']); ?>
                    <p class="text-sm text-gray-600 mt-1">
                        Periode: <?php echo date('d/m/Y', strtotime($invoice_data['rental_date'])); ?> s/d <?php echo date('d/m/Y', strtotime($invoice_data['return_date'])); ?>
                    </p>
                </td>
                <td class="border border-gray-400 p-2 text-center"><?php echo $duration; ?> Hari</td>
                <td class="border border-gray-400 p-2 text-right"><?php echo formatRupiah($invoice_data['harga_sewa']); ?></td>
                <td class="border border-gray-400 p-2 text-right"><?php echo formatRupiah($subtotal); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="flex justify-end">
        <div class="w-full max-w-sm">
            <div class="flex justify-between border-t border-b py-2 font-semibold">
                <span>Subtotal Sewa:</span>
                <span><?php echo formatRupiah($subtotal); ?></span>
            </div>
            <div class="flex justify-between border-b py-2 font-semibold">
                <span>Diskon:</span>
                <span><?php echo formatRupiah($diskon); ?></span>
            </div>
            <div class="flex justify-between border-b py-2 font-extrabold text-xl text-red-700">
                <span>TOTAL HARGA:</span>
                <span><?php echo formatRupiah($grand_total); ?></span>
            </div>
        </div>
    </div>

    <div class="mt-10">
        <p class="text-sm font-semibold">Keterangan:</p>
        <ul class="text-xs list-disc pl-5">
            <li>Invoice ini adalah bukti sah penyewaan kostum.</li>
            <li>Status pesanan di sistem adalah: **<?php echo htmlspecialchars(strtoupper($invoice_data['rental_status'])); ?>**.</li>
        </ul>
    </div>
    
    <div class="mt-12 text-center text-sm">
        <p>Selamat bersenang-senang dan semoga hari anda menyenangkan!</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>