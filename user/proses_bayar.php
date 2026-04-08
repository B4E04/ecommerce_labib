<?php
session_start();
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_user.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama_pembeli = mysqli_real_escape_string($conn, $_POST['nama_pembeli']);
    $hp = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode']);
    $total_final = $_POST['total_final'];
    $tanggal = date("Y-m-d H:i:s");

    // 1. Simpan ke tabel PESANAN (Data utama untuk Admin)
    $sql_pesanan = "INSERT INTO pesanan (user_id, nama_penerima, hp, alamat, total_bayar, metode_bayar, status_pesanan, tanggal_pesan) 
                    VALUES ('$user_id', '$nama_pembeli', '$hp', '$alamat', '$total_final', '$metode', 'Pending', '$tanggal')";
    
    if (mysqli_query($conn, $sql_pesanan)) {
        $pesanan_id = mysqli_insert_id($conn); // Mengambil ID pesanan yang baru saja masuk

        // Generate nomor VA jika metode pembayaran adalah Virtual Account
        $nomor_va = null;
        if (strpos($metode, 'VA-') === 0) {
            $bank_code = str_replace('VA-', '', $metode); // Ambil BCA, MANDIRI, BNI
            $random_suffix = rand(10000, 99999); // Generate random 5 digit
            $nomor_va = "VA-{$bank_code}-{$pesanan_id}{$random_suffix}";
            
            // Update nomor_va di tabel pesanan
            mysqli_query($conn, "UPDATE pesanan SET nomor_va = '$nomor_va' WHERE id = '$pesanan_id'");
        }

        // 2. Simpan rincian barang ke PESANAN_DETAIL
        foreach ($_SESSION['keranjang'] as $produk_id => $qty) {
            $res_p = mysqli_query($conn, "SELECT harga FROM produk WHERE id = '$produk_id'");
            $row_p = mysqli_fetch_assoc($res_p);
            $harga_saat_ini = $row_p['harga'];

            $sql_detail = "INSERT INTO pesanan_detail (pesanan_id, produk_id, qty, harga_saat_ini) 
                           VALUES ('$pesanan_id', '$produk_id', '$qty', '$harga_saat_ini')";
            mysqli_query($conn, $sql_detail);
        }

        // 3. Bersihkan keranjang belanja
        unset($_SESSION['keranjang']);

        echo "<script>
                alert('Pesanan Berhasil dikirim ke Admin!');
                window.location='pembayaran.php?id=$pesanan_id';
              </script>";
    } else {
        echo "Gagal: " . mysqli_error($conn);
    }
}
?>