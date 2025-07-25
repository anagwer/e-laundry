<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;
include '../koneksi.php';

$dari = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

$query = mysqli_query($koneksi, "
    SELECT DATE(tgl_transaksi) AS tanggal, COUNT(*) AS total
    FROM transaksi
    " . (!empty($dari) && !empty($sampai) ? "WHERE DATE(tgl_transaksi) BETWEEN '$dari' AND '$sampai'" : "") . "
    GROUP BY DATE(tgl_transaksi)
    ORDER BY tanggal ASC
");

$html = '
<h3 style="text-align:center;">LAPORAN TOTAL ORDER</h3>
<h4 style="text-align:center;font-weight:bold;">';

if (!empty($dari) && !empty($sampai)) {
    $html .= 'Periode: ' . date('d-m-Y', strtotime($dari)) . ' s/d ' . date('d-m-Y', strtotime($sampai));
} else {
    $html .= 'Semua Tanggal';
}

$html .= '</h4>
<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Total Transaksi</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
        <td>' . $row['total'] . '</td>
    </tr>';
}

$html .= '</tbody></table>';

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('laporan-total-order.pdf', ['Attachment' => false]);
exit;
