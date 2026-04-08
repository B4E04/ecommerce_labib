<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
    FILTER TANGGAL
====================== */
$dari   = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';

$where_clause = "";
if ($dari && $sampai) {
    // DISESUAIKAN: Menggunakan kolom 'tanggal_pesan'
    $where_clause = "WHERE DATE(pesanan.tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}

/* ======================
    DATA TRANSAKSI
====================== */
$dataTransaksi = mysqli_query($conn, "SELECT pesanan.*, users.nama AS nama_akun 
     FROM pesanan 
     LEFT JOIN users ON pesanan.user_id = users.id 
     $where_clause
     ORDER BY pesanan.id DESC");

/* ======================
    TOTAL PENDAPATAN
====================== */
$where_total = "WHERE status_pesanan='Selesai'";
if ($dari && $sampai) {
    $where_total .= " AND DATE(tanggal_pesan) BETWEEN '$dari' AND '$sampai'";
}
$totalQuery = mysqli_query($conn, "SELECT SUM(total_bayar) AS total_rp FROM pesanan $where_total");
$res_total = mysqli_fetch_assoc($totalQuery);
$pendapatan = $res_total['total_rp'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi | Petugas</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fb 0%, #e9ecf1 100%);
            min-height: 100vh;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
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
            padding: 40px;
        }

        .content h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 30px;
        }

        /* ===== FILTER CARD ===== */
        .card-filter {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            border: 1px solid #e5e7eb;
        }

        .card-filter label {
            font-size: 13px;
            font-weight: 600;
            color: #4b5563;
        }

        input[type="date"] {
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        input[type="date"]:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .card-filter button {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .card-filter button:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .card-filter a {
            color: #2563eb;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .card-filter a:hover {
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* ===== TOTAL BOX ===== */
        .total-box {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #86efac;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.1);
        }

        .total-box p {
            font-size: 13px;
            color: #15803d;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .total-box span {
            display: block;
            color: #16a34a;
            font-size: 26px;
            font-weight: 700;
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
            min-width: 900px;
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

        /* ===== BUTTONS ===== */
        button {
            transition: all 0.3s ease;
        }

        button[name="update_status"] {
            padding: 6px 14px;
            border-radius: 6px;
            border: none;
            background: #2563eb;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        button[name="update_status"]:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
        }

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

        /* Modal Styles */
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
        <div class="sidebar">
            <h2>PETUGAS PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php" class="active">Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>

        <div class="content">
            <h1>Laporan Transaksi</h1>
            <div class="card-filter">
                <form method="get" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <input type="date" name="dari" value="<?= $dari ?>" required>
                    <span>s/d</span>
                    <input type="date" name="sampai" value="<?= $sampai ?>" required>
                    <button type="submit">Filter Laporan</button>
                    <a href="laporan_transaksi.php">Reset</a>
                </form>
            </div>

            <div class="total-box">Total Pendapatan (Selesai): <span>Rp <?= number_format($pendapatan) ?></span></div>

            <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pesanan</th>
                    <th>Nama Akun</th>
                    <th style="width: 200px;">Produk</th>
                    <th>Metode</th>
                    <th style="text-align: right; width: 120px;">Total Bayar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1;
                while ($t = mysqli_fetch_assoc($dataTransaksi)) :
                    $st_class = strtolower(str_replace(' ', '', $t['status_pesanan'] ?? 'pending'));
                ?>
                    <tr>
                        <td style="width: 40px; text-align: center;"><?= $no++ ?></td>
                        <td style="width: 110px;"><strong>#CTX-<?= $t['id'] ?></strong></td>
                        <td style="width: 140px;"><?= htmlspecialchars($t['nama_akun'] ?? 'User Terhapus') ?></td>
                        <td style="width: 200px; max-width: 200px; word-break: break-word;">
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
                        <td style="width: 100px; font-size: 12px;"><?= htmlspecialchars($t['metode_bayar'] ?? '-') ?></td>
                        <td style="width: 120px; text-align: right; font-weight: 600;">Rp <?= number_format($t['total_bayar']) ?></td>
                        <td style="width: 100px;"><span class="status <?= $st_class ?>"><?= strtoupper($t['status_pesanan']) ?></span></td>
                        <td style="width: 90px;">
                            <?php
                            // MENGGUNAKAN: tanggal_pesan
                            if (!empty($t['tanggal_pesan'])) {
                                echo date('d-m-Y', strtotime($t['tanggal_pesan']));
                            } else {
                                echo "-";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</body>

</html>