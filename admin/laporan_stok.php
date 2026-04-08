<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
   AMBIL DATA STOK PRODUK
====================== */
$dataProduk = mysqli_query($conn, "
  SELECT id, nama, harga, stok, created_at
  FROM produk
  ORDER BY stok ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Produk</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            background: #f5f7fb;
        }

        /* ===== LAYOUT ===== */
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
            margin-bottom: 6px;
        }

        .content p {
            color: #6b7280;
            margin-bottom: 25px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #1e293b;
            color: #fff;
            padding: 14px;
            font-size: 14px;
            text-align: left;
        }

        td {
            padding: 13px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        tr:hover {
            background: #f1f5f9;
        }

        /* ===== BADGE STOK ===== */
        .badge {
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
            display: inline-block;
        }

        .aman {
            background: #16a34a;
        }

        .menipis {
            background: #f59e0b;
        }

        .habis {
            background: #dc2626;
        }

        /* ===== TEXT ===== */
        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- ===== SIDEBAR ===== -->
        <div class="sidebar">
            <h2>ADMIN PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php">Kelola User</a>
            <a href="petugas.php">Kelola Petugas</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php">Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php" class="active">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="content">
            <h1>Laporan Stok Produk</h1>
            <p>Monitoring ketersediaan stok produk secara real-time</p>

            <div class="card">
                <table>
                    <tr>
                        <th>No</th>
                        <th>Nama Produk</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Stok</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>

                    <?php
                    $no = 1;
                    while ($p = mysqli_fetch_assoc($dataProduk)) {
                        if ($p['stok'] == 0) {
                            $status = "<span class='badge habis'>Habis</span>";
                        } elseif ($p['stok'] <= 5) {
                            $status = "<span class='badge menipis'>Menipis</span>";
                        } else {
                            $status = "<span class='badge aman'>Aman</span>";
                        }
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['nama']) ?></td>
                            <td class="text-right">Rp <?= number_format($p['harga']) ?></td>
                            <td class="text-right"><?= $p['stok'] ?></td>
                            <td><?= $status ?></td>
                            <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

    </div>

</body>

</html>