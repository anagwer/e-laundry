<?php
require '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
include '../koneksi.php';

$dari = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

$where = '';
if (!empty($dari) && !empty($sampai)) {
    $where = "WHERE DATE(tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
}

$query = mysqli_query($koneksi, "
    SELECT DATE(tgl_transaksi) as tanggal, j.jenis_layanan, COUNT(*) as total
    FROM transaksi t
    LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
    $where
    GROUP BY DATE(tgl_transaksi), j.jenis_layanan
    ORDER BY tanggal DESC
");

ob_start();
?>

<h2 style="text-align:center;">Laporan Transaksi per Jenis Layanan</h2>
<p style="text-align:center;">
    <?php if (!empty($dari) && !empty($sampai)) : ?>
        Periode: <?= date('d-m-Y', strtotime($dari)) ?> s/d <?= date('d-m-Y', strtotime($sampai)) ?>
    <?php else : ?>
        Semua Tanggal
    <?php endif; ?>
</p>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Jenis Layanan</th>
            <th>Total Transaksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        $totalSemua = 0;
        while ($row = mysqli_fetch_assoc($query)) :
            $totalSemua += $row['total'];
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                <td><?= htmlspecialchars($row['jenis_layanan']) ?></td>
                <td><?= $row['total'] ?></td>
            </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="3" style="text-align:right;"><strong>Total Transaksi</strong></td>
            <td><strong><?= $totalSemua ?></strong></td>
        </tr>
    </tbody>
</table>

<?php
$html = ob_get_clean();

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("laporan_transaksi_jenis_layanan.pdf", ["Attachment" => false]);
exit;
