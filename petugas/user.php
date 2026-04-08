<?php
include __DIR__ . "/../config/database.php";
include __DIR__ . "/../session/admin_session.php";

/* ======================
   TAMBAH USER
====================== */
if (isset($_POST['tambah'])) {
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = $_POST['role'];

    mysqli_query(
        $conn,
        "INSERT INTO users (nama,email,role)
     VALUES ('$nama','$email','$role')"
    );

    header("Location: user.php");
    exit;
}

/* ======================
   UPDATE USER
====================== */
if (isset($_POST['update'])) {
    $id    = $_POST['id'];
    $nama  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = $_POST['role'];

    mysqli_query(
        $conn,
        "UPDATE users SET
     nama='$nama',
     email='$email',
     role='$role'
     WHERE id=$id"
    );

    header("Location: user.php");
    exit;
}

/* ======================
   HAPUS USER
====================== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: user.php");
    exit;
}

/* ======================
   EDIT USER
====================== */
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $editData = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM users WHERE id=$id")
    );
}

/* ======================
   DATA USER
====================== */
$dataUser = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola User</title>

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

        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 20px;
        }

        /* ===== CARD ===== */
        .card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        /* ===== FORM ===== */
        form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input,
        select,
        button {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background: #2563eb;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .08);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #1e293b;
            color: #fff;
            text-align: left;
        }

        /* ===== ACTION ===== */
        .action {
            padding: 6px 10px;
            border-radius: 6px;
            color: #fff;
            font-size: 13px;
            text-decoration: none;
        }

        .edit {
            background: #16a34a;
        }

        .hapus {
            background: #dc2626;
        }

        .batal {
            background: #6b7280;
        }

        /* ===== ROLE STYLING ===== */
        .role-admin {
            background: #1e3a8a;
            color: white;
            padding: 4px 8px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 500;
        }

        .role-user {
            background: #047857;
            color: white;
            padding: 4px 8px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 500;
        }

        /* Styling khusus untuk link logout */
        .sidebar a.logout-btn {
            color: #ef4444;
            /* Warna merah (Tailwind Red 500) */
            font-weight: 600;
            margin-top: 20px;
            /* Memberi jarak agar terpisah dari menu utama */
            border: 1px solid transparent;
        }

        /* Efek saat mouse diarahkan ke tombol */
        .sidebar a.logout-btn:hover {
            background: #fee2e2;
            /* Background merah sangat muda */
            color: #b91c1c;
            /* Teks merah lebih gelap */
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- ===== SIDEBAR ===== -->
        <div class="sidebar">
            <h2>PETUGAS PANEL</h2>
            <a href="dashboard.php">Dashboard</a>
            <a href="user.php" class="active">Kelola User</a>
            <a href="produk.php">Kelola Produk</a>
            <a href="transaksi.php">Transaksi</a>
            <a href="laporan_transaksi.php"> Laporan Transaksi</a>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
            <a href="laporan_stok.php">Laporan Stok</a>
            <a href="backup_restore.php">Backup & Restore</a>
            <a href="logout.php" class="logout-btn" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                Logout
            </a>
        </div>

        <!-- ===== CONTENT ===== -->
        <div class="content">

            <h1>Kelola User</h1>

            <!-- FORM -->
            <div class="card">
                <form method="post">
                    <input type="hidden" name="id" value="<?= $editData['id'] ?? '' ?>">

                    <input type="text" name="nama" placeholder="Nama"
                        value="<?= $editData['nama'] ?? '' ?>" required>

                    <input type="email" name="email" placeholder="Email"
                        value="<?= $editData['email'] ?? '' ?>" required>

                    <select name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="admin" <?= ($editData['role'] ?? '') == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= ($editData['role'] ?? '') == 'user' ? 'selected' : '' ?>>User</option>
                    </select>

                    <?php if ($editData) { ?>
                        <button name="update">Update</button>
                        <a href="user.php" class="action batal">Batal</a>
                    <?php } else { ?>
                        <button name="tambah">Tambah</button>
                    <?php } ?>
                </form>
            </div>

            <!-- TABLE -->
            <table>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th style="text-align: center;">Role</th>
                    <th>Aksi</th>
                </tr>

                <?php $no = 1;
                while ($u = mysqli_fetch_assoc($dataUser)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($u['nama']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td style="text-align: center;"><span class="role-<?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                        <td>
                            <a class="action edit" href="?edit=<?= $u['id'] ?>">Edit</a>
                            <a class="action hapus"
                                href="?hapus=<?= $u['id'] ?>"
                                onclick="return confirm('Hapus user ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

        </div>
    </div>

</body>

</html>