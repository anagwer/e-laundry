<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;

include '../koneksi.php';

$tanggal = $_GET['tanggal'];

$query = mysqli_query($koneksi, "
    SELECT t.*, p.nm_pelanggan, j.jenis_layanan, j.tarif
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN jenis_layanan j ON t.id_jenis_layanan = j.id_jns_layanan
    WHERE DATE(t.tgl_transaksi) = '$tanggal'
    ORDER BY t.tgl_transaksi ASC
");

$html = '
<h3 style="text-align:center;">NOTA TRANSAKSI</h3>
<p>Tanggal: ' . date('d-m-Y', strtotime($tanggal)) . '</p>
<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Invoice</th>
            <th>Nama Pelanggan</th>
            <th>Jenis Layanan</th>
            <th>Tarif</th>
            <th>Berat</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . $row['invoice'] . '</td>
        <td>' . htmlspecialchars($row['nm_pelanggan']) . '</td>
        <td>' . htmlspecialchars($row['jenis_layanan']) . '</td>
        <td>Rp ' . number_format($row['tarif'], 0, ',', '.') . '</td>
        <td>' . $row['berat'] . ' Kg</td>
        <td>Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('nota-transaksi-' . $tanggal . '.pdf', ['Attachment' => false]);
exit;
