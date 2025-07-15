<?php
require_once '../koneksi.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil data dari POST
$dari = $_POST['dari'] ?? '';
$sampai = $_POST['sampai'] ?? '';

$wheremasuk = '';
$wherekeluar = '';
// Filter laporan
$filter = 'Semua Data';
if ($dari && $sampai) {
    $filter = "Periode: " . date('d-m-Y', strtotime($dari)) . " s/d " . date('d-m-Y', strtotime($sampai));
    $wheremasuk = "WHERE DATE(bm.tgl_masuk) BETWEEN '$dari' AND '$sampai'";
    $wherekeluar = "WHERE DATE(bk.tgl_keluar) BETWEEN '$dari' AND '$sampai'";
}

// Query Barang Masuk
$masukList = [];
$qMasuk = mysqli_query($koneksi, "
    SELECT bm.tgl_masuk, bm.jml_masuk, b.nm_barang 
    FROM barang_masuk bm 
    JOIN barang b ON bm.id_barang = b.id_barang 
    $wheremasuk
    ORDER BY bm.tgl_masuk ASC
");
while ($row = mysqli_fetch_assoc($qMasuk)) {
    $masukList[] = $row;
}

// Query Barang Keluar
$keluarList = [];
$qKeluar = mysqli_query($koneksi, "
    SELECT bk.tgl_keluar, bk.jml_keluar, b.nm_barang 
    FROM barang_keluar bk 
    JOIN barang b ON bk.id_barang = b.id_barang 
    $wherekeluar
    ORDER BY bk.tgl_keluar ASC
");
while ($row = mysqli_fetch_assoc($qKeluar)) {
    $keluarList[] = $row;
}

// Query Stok Saat Ini
$stokList = [];
$qStok = mysqli_query($koneksi, "SELECT nm_barang, stock FROM barang ORDER BY nm_barang");
while ($row = mysqli_fetch_assoc($qStok)) {
    $stokList[] = $row;
}

// Mulai output buffering
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Barang</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2, h4 { text-align: center; margin: 0; }
        .section { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: center; }
        .footer { margin-top: 20px; text-align: right; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Laporan Stok Barang</h2>
    <h4><?= $filter ?></h4>

    <div class="section">
        <h4>Barang Masuk</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Masuk</th>
                    <th>Jumlah Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($masukList as $m): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($m['nm_barang']) ?></td>
                        <td><?= date('d-m-Y', strtotime($m['tgl_masuk'])) ?></td>
                        <td><?= $m['jml_masuk'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h4>Barang Keluar</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Tanggal Keluar</th>
                    <th>Jumlah Keluar</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($keluarList as $k): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($k['nm_barang']) ?></td>
                        <td><?= date('d-m-Y', strtotime($k['tgl_keluar'])) ?></td>
                        <td><?= $k['jml_keluar'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h4>Stok Saat Ini</h4>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($stokList as $s): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($s['nm_barang']) ?></td>
                        <td><?= $s['stock'] ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Dicetak pada: <?= date('d-m-Y H:i') ?>
    </div>
</body>
</html>
<?php
// Simpan dan bersihkan buffer
$html = ob_get_clean();

// Inisialisasi DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Render PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Tampilkan ke browser
$dompdf->stream("Laporan_Stok_Barang.pdf", ["Attachment" => false]);
exit;