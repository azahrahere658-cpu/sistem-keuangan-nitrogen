<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/database.php";

$admin = $_SESSION['admin'];
$success = "";
$error = "";

// Proses Tambah Data Pengeluaran
if (isset($_POST['tambah'])) {
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $jumlah = mysqli_real_escape_string($conn, $_POST['jumlah']);

    if (!empty($tanggal) && !empty($keterangan) && !empty($jumlah)) {
        $query = "INSERT INTO transaksi (tanggal, keterangan, jenis, jumlah) VALUES ('$tanggal', '$keterangan', 'pengeluaran', '$jumlah')";
        if (mysqli_query($conn, $query)) {
            $success = "Data pengeluaran berhasil ditambahkan!";
        } else {
            $error = "Gagal menyimpan data: " . mysqli_error($conn);
        }
    } else {
        $error = "Semua kolom wajib diisi.";
    }
}

// Ambil Data Pengeluaran
$q_pengeluaran = mysqli_query($conn, "SELECT * FROM transaksi WHERE jenis='pengeluaran' ORDER BY tanggal DESC, id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran - Nitrogen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f5f5f5; color: #222; }
        .sidebar { position: fixed; left: 0; top: 0; width: 250px; height: 100vh; background: #C62828; padding: 25px 18px; color: white; }
        .logo { display: flex; align-items: center; gap: 12px; padding: 0 10px; margin-bottom: 40px; }
        .logo-icon { width: 45px; height: 45px; background: white; color: #C62828; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 21px; font-weight: bold; }
        .logo-text h2 { font-size: 18px; }
        .logo-text span { font-size: 11px; color: #ffcdd2; }
        .menu-title { font-size: 11px; color: #ffcdd2; margin: 20px 12px 10px; font-weight: bold; text-transform: uppercase; }
        .menu a { display: flex; align-items: center; gap: 13px; padding: 13px 14px; margin-bottom: 5px; border-radius: 9px; color: #ffebee; text-decoration: none; font-size: 14px; transition: 0.2s; }
        .menu a:hover, .menu a.active { background: #A61B1B; color: white; }
        .logout { position: absolute; bottom: 20px; left: 18px; right: 18px; }
        .main { margin-left: 250px; padding: 30px 35px; }
        .header { margin-bottom: 30px; }
        .header h1 { font-size: 28px; margin-bottom: 7px; }
        .header p { color: #777; font-size: 14px; }
        .box { background: white; border-radius: 14px; padding: 22px; border: 1px solid #eee; box-shadow: 0 3px 12px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .box h3 { font-size: 16px; margin-bottom: 20px; }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr) auto; gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 12px; font-weight: bold; margin-bottom: 6px; color: #444; }
        input { padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        button { background: #C62828; color: white; border: none; padding: 11px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        button:hover { background: #A61B1B; }
        .alert { padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .alert-error { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #999; font-size: 11px; padding: 12px 8px; border-bottom: 1px solid #eee; }
        td { padding: 15px 8px; font-size: 13px; border-bottom: 1px solid #f2f2f2; }
        .expense { color: #C62828; font-weight: bold; }
        .empty { text-align: center; color: #aaa; padding: 35px; }
        @media (max-width: 900px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo">
        <div class="logo-icon">N</div>
        <div class="logo-text">
            <h2>NITROGEN</h2>
            <span>Financial System</span>
        </div>
    </div>
    <div class="menu-title">Menu Utama</div>
    <div class="menu">
        <a href="dashboard.php">📊 <span>Dashboard</span></a>
        <a href="pemasukan.php">💰 <span>Pemasukan</span></a>
        <a href="pengeluaran.php" class="active">💸 <span>Pengeluaran</span></a>
        <a href="laporan.php">📑 <span>Laporan</span></a>
    </div>
    <div class="logout menu">
        <a href="../auth/logout.php">🚪 <span>Logout</span></a>
    </div>
</div>
<div class="main">
    <div class="header">
        <h1>Kelola Pengeluaran</h1>
        <p>Catat dan atur pengeluaran operasional bisnis nitrogen.</p>
    </div>
    <?php if ($success): ?><div class="alert alert-success"><?= $success; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= $error; ?></div><?php endif; ?>
    <div class="box">
        <h3>Tambah Pengeluaran Baru</h3>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label>Keterangan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Beli Tabung/Listrik" required>
                </div>
                <div class="form-group">
                    <label>Jumlah (Rp)</label>
                    <input type="number" name="jumlah" placeholder="50000" required>
                </div>
                <button type="submit" name="tambah">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
    <div class="box">
        <h3>Riwayat Pengeluaran</h3>
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>KETERANGAN</th>
                    <th>JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($q_pengeluaran) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($q_pengeluaran)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td class="expense">- Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="empty">Belum ada data pengeluaran.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
