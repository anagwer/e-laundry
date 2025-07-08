<?php
require '../vendor/autoload.php'; // pastikan dompdf sudah diinstall
use Dompdf\Dompdf;
use Dompdf\Options;

include '../koneksi.php';

$dari = $_POST['dari'];
$sampai = $_POST['sampai'];

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

$total = 0;
ob_start();
?>

<h2 style="text-align:center;">Laporan Pendapatan</h2>
<p>Periode: <?= date('d-m-Y', strtotime($dari)) ?> s/d <?= date('d-m-Y', strtotime($sampai)) ?></p>
<table border="1" cellpadding="5" cellspacing="0" width="100%">
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
                <td><?= $row['invoice'] ?></td>
                <td><?= $row['nm_pelanggan'] ?></td>
                <td><?= $row['nm_layanan'] ?></td>
                <td><?= $row['jenis_layanan'] ?></td>
                <td>Rp <?= number_format($row['tarif'], 0, ',', '.') ?></td>
                <td><?= $row['berat'] ?> Kg</td>
                <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
            </tr>
            <?php $total += $row['total']; ?>
        <?php endwhile; ?>
        <tr>
            <td colspan="8" style="text-align:right;"><strong>Total Pendapatan</strong></td>
            <td><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
        </tr>
    </tbody>
</table>

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
?>