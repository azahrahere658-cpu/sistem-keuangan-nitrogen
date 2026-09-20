<?php
session_start();
if (!isset($_SESSION['admin'])) { exit(); }
include "../config/database.php";

$tgl_mulai = $_GET['tgl_mulai'] ?? date('Y-m-01');
$tgl_selesai = $_GET['tgl_selesai'] ?? date('Y-m-t');

header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Keuangan_Nitrogen_$tgl_mulai_sd_$tgl_selesai.xls");

$query = "SELECT * FROM transaksi WHERE tanggal BETWEEN '$tgl_mulai' AND '$tgl_selesai' ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);
?>

<h2>Laporan Keuangan Nitrogen</h2>
<p>Periode: <?= $tgl_mulai; ?> s/d <?= $tgl_selesai; ?></p>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Jenis</th>
            <th>Jumlah (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while($r = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $r['tanggal']; ?></td>
            <td><?= $r['keterangan']; ?></td>
            <td><?= ucfirst($r['jenis']); ?></td>
            <td><?= $r['jumlah']; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
