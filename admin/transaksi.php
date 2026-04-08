<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
    UPDATE STATUS TRANSAKSI
====================== */
if (isset($_POST['update_status'])) {
    $id     = intval($_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Pastikan nama kolom 'status_pesanan' atau 'status' sesuai dengan database kamu
    // Berdasarkan query riwayat kamu, saya gunakan 'status_pesanan'
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan='$status' WHERE id=$id");

    header("Location: transaksi.php");
    exit;
}

/* ======================
    HAPUS TRANSAKSI
====================== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM pesanan WHERE id=$id");
    header("Location: transaksi.php");
    exit;
}

/* ======================
    DATA TRANSAKSI (JOIN DENGAN TABEL USERS)
====================== */
$dataTransaksi = mysqli_query($conn, "SELECT pesanan.*, users.nama AS nama_akun 
     FROM pesanan 
     LEFT JOIN users ON pesanan.user_id = users.id 
     ORDER BY pesanan.id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Transaksi | Admin</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fb 0%, #e9ecf1 100%);
            min-height: 100vh;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
            align-items: flex-start;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0f172a 0%, #1a202c 100%);
            color: #e2e8f0;
            padding: 30px 0;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar h2 {
            font-size: 18px;
            font-weight: 700;
            margin: 0 20px 30px;
            color: #fff;
            letter-spacing: 0.5px;
        }

        .sidebar a {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #cbd5e1;
            text-decoration: none;
            padding: 12px 20px;
            margin: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover {
            background: rgba(37, 99, 235, 0.2);
            color: #fff;
            border-left-color: #2563eb;
            padding-left: 22px;
        }

        .sidebar a.active {
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.3), rgba(37, 99, 235, 0.1));
            color: #fff;
            border-left-color: #2563eb;
            font-weight: 600;
        }

        .sidebar a.logout-btn {
            color: #fca5a5;
            font-weight: 600;
            margin-top: 30px;
        }

        .sidebar a.logout-btn:hover {
            background: rgba(220, 38, 38, 0.2);
            color: #fecaca;
            border-left-color: #dc2626;
        }

        /* ===== CONTENT ===== */
        .content {
            flex: 1;
            margin-left: 260px;
            padding: 0 40px 40px 40px !important;
            padding-top: 0 !important;
            margin-top: 0 !important;
            position: relative;
            top: 0;
        }

        .content h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin: 0 0 30px 0 !important;
        }

        /* ===== TABLE STYLES ===== */
        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            min-width: 1050px;
        }

        th {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            padding: 14px 12px;
            text-align: left;
            white-space: nowrap;
            position: sticky;
            top: 0;
            letter-spacing: 0.3px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #4b5563;
            line-height: 1.5;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background: #f8fafc;
            box-shadow: inset 0 0 4px rgba(37, 99, 235, 0.05);
        }

        /* ===== STATUS BADGES ===== */
        .status {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            color: #fff;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pending {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);
        }

        .menungguverifikasi {
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        }

        .diproses {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.3);
        }

        .dikirim {
            background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
            box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);
        }

        .selesai {
            background: linear-gradient(135deg, #4ade80 0%, #16a34a 100%);
            box-shadow: 0 2px 4px rgba(22, 163, 74, 0.3);
        }

        .batal {
            background: linear-gradient(135deg, #f87171 0%, #dc2626 100%);
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
        }

        /* ===== FORM ELEMENTS ===== */
        select {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        select:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* ===== BUTTONS ===== */
        button {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .action.hapus {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(220, 38, 38, 0.2);
        }

        .action.hapus:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
        }

        .btn-view-bukti {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
        }

        .btn-view-bukti:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }

        .btn-view-bukti.empty {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* ===== MODAL STYLES ===== */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(3px);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 24px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 16px 16px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .close-modal {
            color: #9ca3af;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .close-modal:hover {
            color: #1f2937;
            background: #e5e7eb;
        }

        .modal-body {
            padding: 24px;
            max-height: 600px;
            overflow-y: auto;
        }

        .modal-body img {
            width: 100%;
            max-height: 450px;
            object-fit: contain;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
        }

        .bukti-info {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 16px;
            border-radius: 10px;
            margin-top: 16px;
            border: 1px solid #e5e7eb;
        }

        .bukti-info p {
            margin: 8px 0;
            font-size: 13px;
            color: #4b5563;
        }

        .bukti-info strong {
            color: #1f2937;
        }

        #buktiStatus {
            padding: 4px 8px !important;
            border-radius: 4px;
            color: white !important;
            font-weight: 600 !important;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <h2>ADMIN PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="petugas.php">Kelola Petugas</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php" class="active">Transaksi</a>
            <a href="laporan_transaksi.php"> Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>
        <div class="content">
            <h1>Kelola Transaksi</h1>
            <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th style="width: 40px;">No</th>
                    <th style="width: 110px;">ID Pesanan</th>
                    <th style="width: 130px;">Nama Akun</th>
                    <th style="width: 200px;">Produk</th>
                    <th style="width: 110px;">Metode</th>
                    <th style="width: 100px;">Bukti Bayar</th>
                    <th style="width: 120px; text-align: right;">Total</th>
                    <th style="width: 100px;">Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1;
                while ($t = mysqli_fetch_assoc($dataTransaksi)) :
                    $st_class = strtolower(str_replace(' ', '', $t['status_pesanan']));
                ?>
                    <tr>
                        <td style="width: 40px; text-align: center;"><?= $no++ ?></td>
                        <td style="width: 110px;"><strong>#CTX-<?= str_pad($t['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                        <td style="width: 130px;"><?= htmlspecialchars($t['nama_akun'] ?? 'User Terhapus') ?></td>
                        <td style="width: 200px; max-width: 200px; word-break: break-word; font-size: 12px;">
                            <?php
                            // Ambil produk yang dipesan
                            $produk_query = mysqli_query($conn, "SELECT produk.nama FROM pesanan_detail 
                                LEFT JOIN produk ON pesanan_detail.produk_id = produk.id 
                                WHERE pesanan_detail.pesanan_id = '{$t['id']}'\n                                ORDER BY pesanan_detail.id ASC");
                            $produk_list = [];
                            while ($p = mysqli_fetch_assoc($produk_query)) {
                                if ($p['nama']) {
                                    $produk_list[] = $p['nama'];
                                }
                            }
                            if (!empty($produk_list)) {
                                echo implode(', ', $produk_list);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td style="width: 110px; font-size: 12px;"><?= htmlspecialchars($t['metode_bayar'] ?? '-') ?></td>
                        <td style="width: 100px; text-align: center; font-size: 12px;">
                            <?php
                            // Ambil bukti pembayaran
                            $bukti_query = mysqli_query($conn, "SELECT * FROM bukti_pembayaran WHERE transaksi_id = '{$t['id']}' LIMIT 1");
                            $bukti = mysqli_fetch_assoc($bukti_query);
                            if ($bukti) {
                                ?>
                                <button class="btn-view-bukti" onclick="showBukti('<?= $bukti['bukti'] ?>', '<?= $bukti['status'] ?>', '<?= htmlspecialchars($t['nama_akun'] ?? 'User') ?>')">Lihat Bukti</button>
                                <?php
                            } else {
                                ?>
                                <span style="color: #9ca3af; font-size: 12px;">Belum Ada</span>
                                <?php
                            }
                            ?>
                        </td>
                        <td style="width: 120px; text-align: right; font-weight: 600;">Rp <?= number_format($t['total_bayar']) ?></td>
                        <td style="width: 100px;">
                            <span class="status <?= $st_class ?>">
                                <?= strtoupper($t['status_pesanan']) ?>
                            </span>
                        </td>
                        <td style="font-size: 12px;">
                            <form method="post" style="display:inline; margin-right: 5px;">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <select name="status" style="font-size: 12px; padding: 4px 6px;">
                                    <option value="Pending" <?= $t['status_pesanan'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Diproses" <?= $t['status_pesanan'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="Dikirim" <?= $t['status_pesanan'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                    <option value="Selesai" <?= $t['status_pesanan'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    <option value="Batal" <?= $t['status_pesanan'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
                                </select>
                                <button name="update_status" style="font-size: 12px; padding: 4px 10px;">Update</button>
                            </form>
                            <a class="action hapus" href="?hapus=<?= $t['id'] ?>" onclick="return confirm('Hapus?')" style="font-size: 12px; padding: 4px 8px; display: inline-block;">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk menampilkan bukti pembayaran -->
    <div id="buktiModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Bukti Pembayaran</h2>
                <span class="close-modal" onclick="closeBukti()">&times;</span>
            </div>
            <div class="modal-body">
                <img id="buktiImage" src="" alt="Bukti Pembayaran">
                <div class="bukti-info">
                    <p><strong>Dari:</strong> <span id="buktiFrom">-</span></p>
                    <p><strong>Status:</strong> <span id="buktiStatus" style="padding: 4px 8px; border-radius: 4px; color: white;">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showBukti(imagePath, status, userName) {
            const modal = document.getElementById('buktiModal');
            const buktiImage = document.getElementById('buktiImage');
            const buktiFrom = document.getElementById('buktiFrom');
            const buktiStatus = document.getElementById('buktiStatus');

            buktiImage.src = '../' + imagePath;
            buktiFrom.textContent = userName;
            buktiStatus.textContent = status.charAt(0).toUpperCase() + status.slice(1);

            // Set status styling
            buktiStatus.style.background = status === 'menunggu' ? '#f59e0b' : 
                                           status === 'diterima' ? '#16a34a' : '#dc2626';

            modal.style.display = 'block';
        }

        function closeBukti() {
            document.getElementById('buktiModal').style.display = 'none';
        }

        // Close modal ketika klik di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('buktiModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>