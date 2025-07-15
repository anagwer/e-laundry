<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil parameter POST
$dari = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

$where = '';
$judulPeriode = "Semua Tanggal";

if ($dari && $sampai) {
    $where = "WHERE DATE(t.tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
    $judulPeriode = "Periode: " . date('d-m-Y', strtotime($dari)) . " s.d " . date('d-m-Y', strtotime($sampai));
}

// Ambil data rekap transaksi berdasarkan kecamatan
if($where == ''){
$query = "
    SELECT p.kecamatan, COUNT(DISTINCT t.id_pelanggan) as total_pelanggan
    FROM transaksi t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    GROUP BY p.kecamatan
    ORDER BY total_pelanggan DESC
";
}else{
    $query = "
        SELECT p.kecamatan, COUNT(DISTINCT t.id_pelanggan) as total_pelanggan
        FROM transaksi t
        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
        $where
        GROUP BY p.kecamatan
        ORDER BY total_pelanggan DESC
    ";
}
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo "<h3>Tidak ada data transaksi untuk dicetak.</h3>";
    exit;
}

// Mulai buffer output
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Rekap Wilayah</title>
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
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>

<body>
    <h2>Laporan Pelanggan Berdasarkan Wilayah</h2>
    <h4><?= $judulPeriode ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kecamatan</th>
                <th>Total Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $grandTotal = 0;
            while ($row = mysqli_fetch_assoc($result)) :
                $grandTotal += $row['total_pelanggan'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['kecamatan']) ?></td>
                    <td><?= $row['total_pelanggan'] ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="2" style="text-align:right"><strong>Total Keseluruhan</strong></td>
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

// Konfigurasi DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$filename = "Laporan_Pelanggan_Wilayah.pdf";
$dompdf->stream($filename, ["Attachment" => false]);
exit;
