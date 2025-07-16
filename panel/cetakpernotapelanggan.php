<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$tanggal = $_GET['tanggal'] ?? '';

if (!$tanggal) {
    die("Tanggal tidak ditemukan.");
}

// Ambil pelanggan yang daftar pada tanggal tersebut
$pelanggan = mysqli_query($koneksi, "
    SELECT  nm_user as nm_pelanggan
    FROM user
    WHERE tgl_daftar = '$tanggal'
");

$idList = [];
while ($p = mysqli_fetch_assoc($pelanggan)) {
    $idList[] = $p['nm_pelanggan'];
}

// Jika tidak ada pelanggan
if (count($idList) === 0) {
    echo "<h3>Tidak ada pelanggan yang mendaftar pada tanggal " . date('d-m-Y', strtotime($tanggal)) . "</h3>";
    exit;
}

$idIn = implode(',', $idList);

// Ambil transaksi dari pelanggan-pelanggan tersebut
$transaksi = mysqli_query($koneksi, "
    SELECT nm_user as nm_pelanggan, DATE(tgl_daftar) as tanggal, COUNT(*) as jumlah
    FROM user
    WHERE tgl_daftar = '$tanggal'
    ORDER BY tgl_daftar DESC
");

// Siapkan output HTML
ob_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Laporan Transaksi Pelanggan Baru</title>
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
    <h2>Laporan Data Pelanggan Baru</h2>
    <h4>Tanggal Pendaftaran: <?= date('d-m-Y', strtotime($tanggal)); ?></h4>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pelanggan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total = 0;
            while ($row = mysqli_fetch_assoc($transaksi)) :
                $total++;
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nm_pelanggan'] ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td style="text-align:right"><strong>Total</strong></td>
                <td><strong><?php echo $total ?></strong></td>
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

// Inisialisasi dan render DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Laporan_Pelanggan_Baru_$tanggal.pdf", array("Attachment" => false));
exit;
