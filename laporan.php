<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit();
}

include "../config/database.php";

$admin = $_SESSION['admin'];

// Filter Tanggal
$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-t');

$where = "WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'";

// Query Laporan
$query = "SELECT * FROM transaksi $where ORDER BY tanggal DESC, id DESC";
$result = mysqli_query($conn, $query);

// Total Rekap Periode
$q_rekap = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN jenis='pemasukan' THEN jumlah ELSE 0 END) AS total_masuk,
    SUM(CASE WHEN jenis='pengeluaran' THEN jumlah ELSE 0 END) AS total_keluar
    FROM transaksi WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai'");
$rekap = mysqli_fetch_assoc($q_rekap);

$total_masuk = $rekap['total_masuk'] ?? 0;
$total_keluar = $rekap['total_keluar'] ?? 0;
$saldo_periode = $total_masuk - $total_keluar;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Nitrogen</title>
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
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 28px; margin-bottom: 7px; }
        .header p { color: #777; font-size: 14px; }
        .box { background: white; border-radius: 14px; padding: 22px; border: 1px solid #eee; box-shadow: 0 3px 12px rgba(0,0,0,0.04); margin-bottom: 25px; }
        .filter-grid { display: flex; gap: 15px; align-items: end; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 12px; font-weight: bold; margin-bottom: 6px; color: #444; }
        input { padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .btn { background: #C62828; color: white; border: none; padding: 11px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 13px; display: inline-block; }
        .btn-print { background: #333; }
        .btn:hover { opacity: 0.9; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 20px; }
        .summary-card { background: white; padding: 15px; border-radius: 10px; border: 1px solid #eee; text-align: center; }
        .summary-card span { font-size: 11px; color: #777; display: block; margin-bottom: 5px; }
        .summary-card strong { font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #999; font-size: 11px; padding: 12px 8px; border-bottom: 1px solid #eee; }
        td { padding: 15px 8px; font-size: 13px; border-bottom: 1px solid #f2f2f2; }
        .income { color: #2e7d32; font-weight: bold; }
        .expense { color: #C62828; font-weight: bold; }
        .empty { text-align: center; color: #aaa; padding: 35px; }
        /* Style untuk Tombol Aksi */
        .btn-action { text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; }
        @media print { .sidebar, .filter-box, .header button, th:last-child, td:last-child { display: none !important; } .main { margin-left: 0 !important; padding: 0 !important; } .box { box-shadow: none !important; border: none !important; } }
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
        <a href="pengeluaran.php">💸 <span>Pengeluaran</span></a>
        <a href="laporan.php" class="active">📑 <span>Laporan</span></a>
    </div>
    <div class="logout menu">
        <a href="../auth/logout.php">🚪 <span>Logout</span></a>
    </div>
</div>
<div class="main">
    <div class="header">
        <div>
            <h1>Laporan Keuangan</h1>
            <p>Rekapitulasi seluruh transaksi keuangan usaha.</p>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Laporan</button>
        </div>
    </div>
    <div class="box filter-box">
        <form method="GET" class="filter-grid">
            <div class="form-group">
                <label>Dari Tanggal</label>
                <input type="date" name="tgl_mulai" value="<?= $tgl_mulai; ?>">
            </div>
            <div class="form-group">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" value="<?= $tgl_selesai; ?>">
            </div>
            <button type="submit" class="btn">Filter Data</button>
            <a href="laporan.php" class="btn" style="background:#888;">Reset</a>
        </form>
    </div>
    <div class="summary-grid">
        <div class="summary-card">
            <span>Total Pemasukan</span>
            <strong class="income">Rp<?= number_format($total_masuk, 0, ',', '.'); ?></strong>
        </div>
        <div class="summary-card">
            <span>Total Pengeluaran</span>
            <strong class="expense">Rp<?= number_format($total_keluar, 0, ',', '.'); ?></strong>
        </div>
        <div class="summary-card">
            <span>Saldo Bersih (Periode)</span>
            <strong style="color: #222;">Rp<?= number_format($saldo_periode, 0, ',', '.'); ?></strong>
        </div>
    </div>
    <div class="box">
        <table>
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>KETERANGAN</th>
                    <th>JENIS</th>
                    <th>JUMLAH</th>
                    <th>AKSI</th> <!-- Kolom Baru -->
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?= htmlspecialchars($row['keterangan']); ?></td>
                            <td>
                                <span class="<?= $row['jenis'] == 'pemasukan' ? 'income' : 'expense'; ?>">
                                    <?= ucfirst($row['jenis']); ?>
                                </span>
                            </td>
                            <td class="<?= $row['jenis'] == 'pemasukan' ? 'income' : 'expense'; ?>">
                                <?= $row['jenis'] == 'pemasukan' ? '+' : '-'; ?> Rp<?= number_format($row['jumlah'], 0, ',', '.'); ?>
                            </td>
                            <td>
                                <!-- Tombol Aksi Edit & Hapus -->
                                <a href="edit_transaksi.php?id=<?= $row['id']; ?>" class="btn-action" style="color:#1565c0;">Edit</a>
                                <a href="hapus_transaksi.php?id=<?= $row['id']; ?>" class="btn-action" style="color:#C62828;" onclick="return confirm('Yakin hapus transaksi ini, Cok?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">Tidak ada data transaksi pada periode ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
