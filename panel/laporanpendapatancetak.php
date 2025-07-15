<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

include '../koneksi.php';

$dari = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

if ($dari && $sampai) {
    $query = mysqli_query($koneksi, "
        SELECT t.*, p.nm_pelanggan, u.nm_user, j.jenis_layanan, j.tarif, l.nm_layanan
        FROM transaksi t
        LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        LEFT JOIN user u ON t.id_user = u.id_user
        LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
        LEFT JOIN kategori_layanan l ON j.id_kategori_layanan = l.id_kategori_layanan
        WHERE DATE(t.tgl_transaksi) BETWEEN '$dari' AND '$sampai'
        ORDER BY t.tgl_transaksi DESC
    ");
    $periode = "Periode: " . date('d-m-Y', strtotime($dari)) . " s/d " . date('d-m-Y', strtotime($sampai));
} else {
    $query = mysqli_query($koneksi, "
        SELECT t.*, p.nm_pelanggan, u.nm_user, j.jenis_layanan, j.tarif, l.nm_layanan
        FROM transaksi t
        LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        LEFT JOIN user u ON t.id_user = u.id_user
        LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
        LEFT JOIN kategori_layanan l ON j.id_kategori_layanan = l.id_kategori_layanan
        ORDER BY t.tgl_transaksi DESC
    ");
    $periode = "Semua Tanggal";
}

$total = 0;
ob_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h2, h4 {
            text-align: center;
            margin: 0;
        }
        h4 {
            margin-bottom: 20px;
            font-weight: normal;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        thead {
            background-color: #f2f2f2;
        }
        th, td {
            padding: 6px;
            text-align: center;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>LAPORAN PENDAPATAN</h2>
<h4><?= $periode ?></h4>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Invoice</th>
            <th>Pelanggan</th>
            <th>Layanan</th>
            <th>Jenis</th>
            <th>Tarif</th>
            <th>Berat</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        while ($row = mysqli_fetch_assoc($query)) : ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d-m-Y', strtotime($row['tgl_transaksi'])) ?></td>
                <td><?= htmlspecialchars($row['invoice']) ?></td>
                <td><?= htmlspecialchars($row['nm_pelanggan']) ?></td>
                <td><?= htmlspecialchars($row['nm_layanan']) ?></td>
                <td><?= htmlspecialchars($row['jenis_layanan']) ?></td>
                <td>Rp <?= number_format($row['tarif'], 0, ',', '.') ?></td>
                <td><?= $row['berat'] ?> Kg</td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
            </tr>
            <?php $total += $row['total']; ?>
        <?php endwhile; ?>
        <tr>
            <td colspan="8" style="text-align:right;">Total Pendapatan</td>
            <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>

<?php
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan_pendapatan.pdf", ["Attachment" => false]);
exit;
