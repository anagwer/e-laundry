<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$dari   = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

if (!$dari || !$sampai) {
    die("Tanggal tidak valid.");
}

// Ambil data pelanggan berdasarkan periode
$query = mysqli_query($koneksi, "
    SELECT DATE(tgl_daftar) as tanggal, COUNT(*) as jumlah
    FROM pelanggan
    WHERE DATE(tgl_daftar) BETWEEN '$dari' AND '$sampai'
    GROUP BY DATE(tgl_daftar)
    ORDER BY tanggal ASC
");

// Siapkan HTML untuk cetak
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pelanggan Baru</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2,
        h4 {
            text-align: center;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>

    <h2>Laporan Pelanggan Baru</h2>
    <h4>Periode: <?= date('d-m-Y', strtotime($dari)) ?> s/d <?= date('d-m-Y', strtotime($sampai)) ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Daftar</th>
                <th>Jumlah Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $grandTotal = 0;
            while ($row = mysqli_fetch_assoc($query)) :
                $grandTotal += $row['jumlah'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= $row['jumlah'] ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="2"><strong>Total Keseluruhan</strong></td>
                <td><strong><?= $grandTotal ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: <?= date('d-m-Y H:i') ?>
    </div>

</body>

</html>

<?php
$html = ob_get_clean();

// Render PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Pelanggan_Baru_{$dari}_sd_{$sampai}.pdf", ["Attachment" => false]);
exit;
