<?php
$filter = '';
$whereMasuk = '';
$whereKeluar = '';
$showCetak = false;

if (isset($_POST['filter'])) {
    $dari = $_POST['dari'];
    $sampai = $_POST['sampai'];
    $filter = "Periode: " . date('d-m-Y', strtotime($dari)) . " s/d " . date('d-m-Y', strtotime($sampai));
    $whereMasuk = "WHERE DATE(tgl_masuk) BETWEEN '$dari' AND '$sampai'";
    $whereKeluar = "WHERE DATE(tgl_keluar) BETWEEN '$dari' AND '$sampai'";
    $showCetak = true;
}

// Ambil data barang
$barang = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY nm_barang");

// Tampilkan tabel gabungan
$dataBarang = [];
while ($b = mysqli_fetch_assoc($barang)) {
    $id_barang = $b['id_barang'];
    $nm_barang = $b['nm_barang'];
    $stok = $b['stock'];

    // Ambil tanggal masuk pertama dan jumlah total masuk
    $qMasuk = mysqli_query(
        $koneksi,
        "
        SELECT MIN(tgl_masuk) as tgl_masuk, SUM(jml_masuk) as total_masuk 
        FROM barang_masuk 
        WHERE id_barang = '$id_barang' 
        " . ($whereMasuk ? "AND " . substr($whereMasuk, 6) : "")
    );
    $masuk = mysqli_fetch_assoc($qMasuk);

    // Ambil tanggal keluar pertama dan jumlah total keluar
    $qKeluar = mysqli_query(
        $koneksi,
        "
        SELECT MIN(tgl_keluar) as tgl_keluar, SUM(jml_keluar) as total_keluar 
        FROM barang_keluar 
        WHERE id_barang = '$id_barang' 
        " . ($whereKeluar ? "AND " . substr($whereKeluar, 6) : "")
    );
    $keluar = mysqli_fetch_assoc($qKeluar);

    $dataBarang[] = [
        'nama' => $nm_barang,
        'tgl_masuk' => $masuk['tgl_masuk'] ?? '-',
        'total_masuk' => $masuk['total_masuk'] ?? 0,
        'tgl_keluar' => $keluar['tgl_keluar'] ?? '-',
        'total_keluar' => $keluar['total_keluar'] ?? 0,
        'stok' => $stok
    ];
}
?>

<div class="page-heading">
    <h1 class="page-title">Laporan Stok Barang</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item">Laporan</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Filter Periode</div>
        </div>
        <div class="ibox-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-4">
                        <label>Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control" value="<?= $dari ?? '' ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="<?= $sampai ?? '' ?>" required>
                    </div>
                    <div class="col-md-4 pt-4">
                        <button type="submit" name="filter" class="btn btn-primary mt-2">Tampilkan</button>
                    </div>
                </div>
            </form>

            <?php if ($showCetak): ?>
                <form method="post" action="laporanstokcetak.php" target="_blank" class="mt-3">
                    <input type="hidden" name="dari" value="<?= $dari ?>">
                    <input type="hidden" name="sampai" value="<?= $sampai ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-print"></i> Cetak PDF
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Stok Barang <?= $filter ? "($filter)" : '' ?></div>
        </div>
        <div class="ibox-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Barang</th>
                            <th>Tanggal Masuk</th>
                            <th>Jumlah Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Jumlah Keluar</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        $ttlmasuk =0;
                        $ttlkeluar =0;
                        $ttlstok =0;
                        foreach ($dataBarang as $row): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= $row['tgl_masuk'] ? date('d-m-Y', strtotime($row['tgl_masuk'])) : '-' ?></td>
                                <td><?= $row['total_masuk'] ?></td>
                                <td><?= $row['tgl_keluar'] ? date('d-m-Y', strtotime($row['tgl_keluar'])) : '-' ?></td>
                                <td><?= $row['total_keluar'] ?></td>
                                <td><?= $row['stok'] ?></td>
                                <td>
                                    <a href="cetakpernotastok.php?barang=<?= urlencode($row['nama']) ?>&dari=<?= $dari ?? '' ?>&sampai=<?= $sampai ?? '' ?>" class="btn btn-sm btn-primary" target="_blank">
                                        Cetak Nota
                                    </a>
                                </td>
                            </tr>
                        <?php 
                        $ttlkeluar = $ttlkeluar +$row['total_masuk'];
                        $ttlmasuk = $ttlmasuk + $row['total_keluar'];
                        $ttlstok = $ttlstok + $row['stok'];
                        endforeach ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-center">Total Masuk</th>
                            <th colspan="1"><?php echo $ttlmasuk; ?></th>
                            <th colspan="1" class="text-center">Total Keluar</th>
                            <th colspan="1"><?php echo $ttlkeluar; ?></th>
                            <th colspan="1"><?php echo $ttlstok; ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>