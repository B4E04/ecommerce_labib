<?php
session_start();
include "../config/database.php";

$id_transaksi = $_GET['id'] ?? '';

if (empty($id_transaksi)) {
    header("Location: home.php");
    exit;
}

// Ambil data pesanan untuk ditampilkan ke user
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = '$id_transaksi' AND user_id = '{$_SESSION['user_id']}'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Pesanan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pesanan Berhasil | Cartix</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }

        .icon {
            font-size: 60px;
            color: #10b981;
            margin-bottom: 20px;
        }

        h2 {
            margin: 0 0 10px;
            color: #1f2937;
        }

        p {
            color: #6b7280;
            line-height: 1.6;
        }

        .order-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: left;
        }

        .order-box div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 14px;
        }

        /* Styling Tambahan untuk Upload */
        .upload-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px dashed #e5e7eb;
        }

        .upload-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 10px;
        }

        .input-file {
            width: 100%;
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .btn-upload {
            background: #10b981;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }

        .btn-upload:hover {
            background: #059669;
        }

        .transfer-info {
            background: linear-gradient(135deg, #f0f7ff 0%, #e0f2fe 100%);
            border: 2px solid #bfdbfe;
            padding: 20px;
            border-radius: 12px;
            margin: 15px 0;
            text-align: left;
        }

        .transfer-info h3 {
            margin: 0 0 15px;
            color: #1e40af;
            font-size: 14px;
        }

        .transfer-code {
            background: white;
            border: 2px dashed #3b82f6;
            padding: 15px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .transfer-code .code {
            font-size: 16px;
            font-weight: 700;
            color: #1e40af;
            font-family: 'Courier New', monospace;
            word-break: break-all;
            flex: 1;
        }

        .transfer-code .copy-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            transition: 0.3s;
        }

        .transfer-code .copy-btn:hover {
            background: #1e40af;
        }

        .transfer-details {
            font-size: 12px;
            color: #1f2937;
            margin-top: 10px;
            padding: 10px;
            background: white;
            border-radius: 6px;
        }

        .transfer-details p {
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <div class="success-card">
        <i class="fa-solid fa-circle-check icon"></i>
        <h2>Pesanan Diterima!</h2>
        <p>Terima kasih sudah berbelanja. Pesanan Anda sedang diproses oleh admin.</p>

        <div class="order-box">
            <div><span>ID Transaksi:</span> <strong>#<?= $data['id'] ?></strong></div>
            <div><span>Total Bayar:</span> <strong>Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></strong></div>
            <div><span>Metode:</span> <strong><?php 
                if (strpos($data['metode_bayar'], 'VA-') === 0) {
                    echo 'Virtual Account ' . str_replace('VA-', '', $data['metode_bayar']);
                } elseif (in_array($data['metode_bayar'], ['DANA', 'GOPAY', 'OVO', 'SHOPEEPAY'])) {
                    echo 'E-Wallet ' . $data['metode_bayar'];
                } else {
                    echo $data['metode_bayar'];
                }
            ?></strong></div>
            <div><span>Status:</span> <strong style="color: #d97706;"><?= ucfirst($data['status_pesanan']) ?></strong></div>
        </div>

        <?php 
        // Tampilkan informasi transfer berdasarkan metode
        if (strpos($data['metode_bayar'], 'VA-') === 0) {
            // Virtual Account
            ?>
            <div class="transfer-info">
                <h3><i class="fa-solid fa-wallet" style="color: #1e40af;"></i> Nomor Virtual Account</h3>
                <div class="transfer-code">
                    <span class="code"><?= $data['nomor_va'] ?></span>
                    <button class="copy-btn" onclick="copyToClipboard('<?= $data['nomor_va'] ?>')">Salin</button>
                </div>
                <div class="transfer-details">
                    <p><strong>Instruksi:</strong></p>
                    <p>1. Buka aplikasi banking atau ATM</p>
                    <p>2. Pilih Transfer ke Bank Lain</p>
                    <p>3. Masukkan nomor VA di atas</p>
                    <p>4. Jumlah transfer: <strong>Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></strong></p>
                    <p>5. Konfirmasi dan transfer</p>
                </div>
            </div>
            <?php
        } elseif (in_array($data['metode_bayar'], ['DANA', 'GOPAY', 'OVO', 'SHOPEEPAY'])) {
            // E-Wallet
            $ewallet_info = [
                'DANA' => ['no' => '081234567890', 'atas_nama' => 'Toko Labib'],
                'GOPAY' => ['no' => '081234567891', 'atas_nama' => 'Toko Labib'],
                'OVO' => ['no' => '081234567892', 'atas_nama' => 'Toko Labib'],
                'SHOPEEPAY' => ['no' => '081234567893', 'atas_nama' => 'Toko Labib'],
            ];
            $ewallet = $ewallet_info[$data['metode_bayar']];
            ?>
            <div class="transfer-info">
                <h3><i class="fa-solid fa-mobile-screen-button" style="color: #1e40af;"></i> Transfer E-Wallet</h3>
                <div class="transfer-code">
                    <span class="code"><?= $ewallet['no'] ?></span>
                    <button class="copy-btn" onclick="copyToClipboard('<?= $ewallet['no'] ?>')">Salin</button>
                </div>
                <div class="transfer-details">
                    <p><strong>E-Wallet:</strong> <?= $data['metode_bayar'] ?></p>
                    <p><strong>Nomor:</strong> <?= $ewallet['atas_nama'] ?></p>
                    <p><strong>Jumlah Transfer:</strong> Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></p>
                    <p><strong>Instruksi:</strong></p>
                    <p>1. Buka aplikasi <?= $data['metode_bayar'] ?></p>
                    <p>2. Pilih Transfer atau Kirim Uang</p>
                    <p>3. Masukkan nomor di atas</p>
                    <p>4. Jumlah: Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></p>
                    <p>5. Konfirmasi transfer</p>
                </div>
            </div>
            <?php
        } else {
            // Transfer Bank Biasa - tampilkan informasi rekening
            $rekening_bank = [
                'BCA' => ['no' => '1234567890', 'atas_nama' => 'Toko Labib'],
                'MANDIRI' => ['no' => '0987654321', 'atas_nama' => 'Toko Labib'],
                'BNI' => ['no' => '1122334455', 'atas_nama' => 'Toko Labib'],
            ];
            $bank = str_replace('BANK', '', $data['metode_bayar']);
            $bank = isset($rekening_bank[$bank]) ? $bank : 'BCA';
            $rek = $rekening_bank[$bank];
            ?>
            <div class="transfer-info">
                <h3><i class="fa-solid fa-building-columns" style="color: #1e40af;"></i> Rekening Transfer</h3>
                <div class="transfer-code">
                    <span class="code"><?= $rek['no'] ?></span>
                    <button class="copy-btn" onclick="copyToClipboard('<?= $rek['no'] ?>')">Salin</button>
                </div>
                <div class="transfer-details">
                    <p><strong>Bank:</strong> <?= $bank ?></p>
                    <p><strong>Rekening Atas Nama:</strong> <?= $rek['atas_nama'] ?></p>
                    <p><strong>Jumlah Transfer:</strong> Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></p>
                </div>
            </div>
            <?php
        }
        ?>


        <div class="upload-section">
            <form action="proses_upload_bukti.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_transaksi" value="<?= $data['id'] ?>">
                <label class="upload-label"><i class="fa-solid fa-camera"></i> Upload Bukti Transfer</label>
                <input type="file" name="bukti_transfer" class="input-file" accept="image/*" required>
                <button type="submit" class="btn-upload">Kirim Bukti Pembayaran</button>
            </form>
        </div>

        <p style="font-size: 13px; margin-top: 20px;">Silakan simpan ID Transaksi Anda jika sewaktu-waktu diperlukan.</p>

        <a href="home.php" class="btn-home">Kembali ke Beranda</a>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Nomor berhasil disalin ke clipboard!');
            }).catch(err => {
                // Fallback untuk browser lama
                const textarea = document.createElement("textarea");
                textarea.value = text;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand("copy");
                document.body.removeChild(textarea);
                alert('Nomor berhasil disalin ke clipboard!');
            });
        }
    </script>

</body>

</html>