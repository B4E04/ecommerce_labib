<?php
session_start();
include "../config/database.php";

// Ambil data pesanan
// Gunakan COALESCE atau pengecekan jika tanggal_pesan belum ada, 
// tapi sebaiknya jalankan SQL di atas dulu.
$query = "SELECT pesanan.*, users.nama AS nama_akun FROM pesanan LEFT JOIN users ON pesanan.user_id = users.id ORDER BY pesanan.id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Admin - Daftar Pesanan Masuk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table-wrapper {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th,
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 13px;
            line-height: 1.4;
        }

        th {
            background: #1e293b;
            color: white;
            font-weight: 600;
            white-space: nowrap;
            position: sticky;
            top: 0;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .menungguverifikasi {
            background: #dbeafe;
            color: #1e40af;
        }

        .diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .dikirim {
            background: #e9d5ff;
            color: #6b21a8;
        }

        .selesai {
            background: #dcfce7;
            color: #166534;
        }

        .batal {
            background: #fee2e2;
            color: #991b1b;
        }

        .diterima {
            background: #dcfce7;
            color: #166534;
        }

        .ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn {
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 6px;
            color: white;
            background: #2563eb;
            font-size: 12px;
            display: inline-block;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #1d4ed8;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2><i class="fa fa-list"></i> Pesanan Role User</h2>
        <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th style="width: 70px;">ID</th>
                    <th style="width: 150px;">Nama Pembeli</th>
                    <th style="width: 180px;">HP / Alamat</th>
                    <th style="width: 200px;">Produk</th>
                    <th style="width: 120px;">Metode</th>
                    <th style="width: 120px; text-align: right;">Total</th>
                    <th style="width: 130px;">Status</th>
                    <th style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td style="width: 70px;">#<?= $row['id'] ?></td>
                        <td style="width: 150px;"><strong><?= htmlspecialchars($row['nama_penerima']) ?></strong></td>
                        <td style="width: 180px; font-size: 12px;">
                            <small>WA: <?= $row['hp'] ?? '-' ?></small><br>
                            <small>Alamat: <?= $row['alamat'] ?? '-' ?></small>
                        </td>
                        <td style="width: 200px; max-width: 200px; word-break: break-word; font-size: 12px;">
                            <?php
                            // Ambil produk yang dipesan
                            $produk_query = mysqli_query($conn, "SELECT produk.nama FROM pesanan_detail 
                                LEFT JOIN produk ON pesanan_detail.produk_id = produk.id 
                                WHERE pesanan_detail.pesanan_id = '{$row['id']}'\n                                ORDER BY pesanan_detail.id ASC");
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
                        <td style="width: 120px; font-size: 12px;"><?= htmlspecialchars($row['metode_bayar'] ?? '-') ?></td>
                        <td style="width: 120px; text-align: right; font-weight: 600;">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                        <td style="width: 130px;">
                            <span class="status-badge <?= strtolower(str_replace(' ', '', $row['status_pesanan'] ?? 'pending')) ?>">
                                <?= ucfirst($row['status_pesanan']) ?>
                            </span>
                        </td>
                        <td style="width: 80px;">
                            <a href="admin_detail.php?id=<?= $row['id'] ?>" class="btn">Detail</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    </div>

</body>

</html>