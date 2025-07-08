<?php
require '../vendor/autoload.php';
require '../koneksi.php'; // Pastikan koneksi DB sudah disiapkan

use Dompdf\Dompdf;
use Dompdf\Options;

$tanggal = $_GET['tanggal'] ?? '';
$jenis = $_GET['jenis'] ?? '';

if (!$tanggal || !$jenis) {
    die("Tanggal dan jenis layanan diperlukan.");
}

// Ambil data transaksi sesuai tanggal & jenis layanan
$query = mysqli_query($koneksi, "
    SELECT t.*, p.nm_pelanggan, j.jenis_layanan 
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
    WHERE DATE(t.tgl_transaksi) = '$tanggal' AND j.jenis_layanan = '$jenis'
");

$html = '
<style>
    body { font-family: sans-serif; font-size: 12px; }
    h2 { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; text-align: left; }
</style>

<h2>Laporan Transaksi - ' . htmlspecialchars($jenis) . '<br> Tanggal: ' . date('d-m-Y', strtotime($tanggal)) . '</h2>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Invoice</th>
            <th>Nama Pelanggan</th>
            <th>Berat</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
$totalAkhir = 0;
while ($row = mysqli_fetch_assoc($query)) {
    $html .= '<tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($row['invoice']) . '</td>
        <td>' . htmlspecialchars($row['nm_pelanggan']) . '</td>
        <td>' . $row['berat'] . ' Kg</td>
        <td>Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
    </tr>';
    $totalAkhir += $row['total'];
}

$html .= '
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" style="text-align:right">Total</th>
            <th>Rp ' . number_format($totalAkhir, 0, ',', '.') . '</th>
        </tr>
    </tfoot>
</table>';

// PDF render
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("nota_{$jenis}_" . date('Ymd', strtotime($tanggal)) . ".pdf", ["Attachment" => false]);
