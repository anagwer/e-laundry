<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$kecamatan = $_GET['kecamatan'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

if (!$kecamatan) {
    die("Kecamatan tidak ditemukan.");
}

// Buat kondisi WHERE
$where = "p.kecamatan = '$kecamatan'";
$judulTanggal = "Semua Tanggal";

if ($dari && $sampai) {
    $where .= " AND DATE(t.tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
    $judulTanggal = "Periode: " . date('d-m-Y', strtotime($dari)) . " s.d " . date('d-m-Y', strtotime($sampai));
}

// Query transaksi berdasarkan kecamatan dan tanggal
$transaksi = mysqli_query($koneksi, "
    SELECT t.id_transaksi, t.tgl_transaksi, p.nm_pelanggan, p.kecamatan, t.total
    FROM transaksi t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    WHERE $where
    AND t.status_ambil = 'Selesai'
    ORDER BY t.tgl_transaksi ASC
");

if (mysqli_num_rows($transaksi) === 0) {
    echo "<h3>Tidak ada transaksi di kecamatan $kecamatan pada periode tersebut.</h3>";
    exit;
}

ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Transaksi Wilayah</title>
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
    <h2>Laporan Transaksi per Wilayah</h2>
    <h4>Kecamatan: <?= htmlspecialchars($kecamatan) ?> | <?= $judulTanggal ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Nama Pelanggan</th>
                <th>Tanggal Transaksi</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $totalAll = 0;
            while ($row = mysqli_fetch_assoc($transaksi)) :
                $totalAll += $row['total'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['id_transaksi'] ?></td>
                    <td><?= $row['nm_pelanggan'] ?></td>
                    <td><?= date('d-m-Y', strtotime($row['tgl_transaksi'])) ?></td>
                    <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="4" style="text-align:right"><strong>Total</strong></td>
                <td><strong>Rp <?= number_format($totalAll, 0, ',', '.') ?></strong></td>
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

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$filename = "Laporan_Transaksi_Wilayah_" . $kecamatan . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
exit;
