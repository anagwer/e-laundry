<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil parameter dari URL
$barang = $_GET['barang'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$filter = '';
$namaBarang = '-';
$stok = 0;
$masuk = ['awal' => null, 'akhir' => null, 'total' => 0];
$keluar = ['awal' => null, 'akhir' => null, 'total' => 0];

// Ambil data barang
$qBarang = mysqli_query($koneksi, "SELECT * FROM barang WHERE nm_barang = '$barang'");
$dataBarang = mysqli_fetch_assoc($qBarang);

if ($dataBarang) {
    $id_barang = $dataBarang['id_barang'];
    $namaBarang = $dataBarang['nm_barang'];
    $stok = $dataBarang['stock'];

    if ($dari && $sampai) {
        $filter = "Periode: " . date('d-m-Y', strtotime($dari)) . " s/d " . date('d-m-Y', strtotime($sampai)) . "";
        $whereTanggalMasuk = "AND DATE(tgl_masuk) BETWEEN '$dari' AND '$sampai'";
        $whereTanggalKeluar = "AND DATE(tgl_keluar) BETWEEN '$dari' AND '$sampai'";

        // Barang masuk
        $qMasuk = mysqli_query($koneksi, "
            SELECT 
                MIN(tgl_masuk) as awal,
                MAX(tgl_masuk) as akhir,
                SUM(jml_masuk) as total
            FROM barang_masuk 
            WHERE id_barang = '$id_barang' $whereTanggalMasuk
        ");
        $masukData = mysqli_fetch_assoc($qMasuk);
        if ($masukData) $masuk = $masukData + $masuk;

        // Barang keluar
        $qKeluar = mysqli_query($koneksi, "
            SELECT 
                MIN(tgl_keluar) as awal,
                MAX(tgl_keluar) as akhir,
                SUM(jml_keluar) as total
            FROM barang_keluar 
            WHERE id_barang = '$id_barang' $whereTanggalKeluar
        ");
        $keluarData = mysqli_fetch_assoc($qKeluar);
        if ($keluarData) $keluar = $keluarData + $keluar;
    }
}

// Mulai tampilan HTML
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Nota Stok Barang</title>
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
    <h2>Nota Stok Barang</h2>
    <h4><?= htmlspecialchars($namaBarang) ?></h4>
    <h4><?= $filter ?></h4>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Tgl Masuk</th>
                <th>Jumlah Masuk</th>
                <th>Tgl Keluar</th>
                <th>Jumlah Keluar</th>
                <th>Stok Sekarang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= htmlspecialchars($namaBarang) ?></td>
                <td>
                    <?= $masuk['awal'] ? date('d-m-Y', strtotime($masuk['awal'])) : '-' ?>
                    <?= ($masuk['awal'] && $masuk['akhir'] && $masuk['awal'] !== $masuk['akhir']) ? ' s/d ' . date('d-m-Y', strtotime($masuk['akhir'])) : '' ?>
                </td>
                <td><?= $masuk['total'] ?? 0 ?></td>
                <td>
                    <?= $keluar['awal'] ? date('d-m-Y', strtotime($keluar['awal'])) : '-' ?>
                    <?= ($keluar['awal'] && $keluar['akhir'] && $keluar['awal'] !== $keluar['akhir']) ? ' s/d ' . date('d-m-Y', strtotime($keluar['akhir'])) : '' ?>
                </td>
                <td><?= $keluar['total'] ?? 0 ?></td>
                <td><?= $stok ?? 0 ?></td>
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

// Generate PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Nota_Stok_{$namaBarang}.pdf", ["Attachment" => false]);
exit;
