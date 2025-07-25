<?php
$dari = $sampai = '';
$where = '';
$showCetak = false;

if (isset($_POST['filter'])) {
    $dari = $_POST['dari'];
    $sampai = $_POST['sampai'];
    $where = "WHERE DATE(tgl_daftar) BETWEEN '$dari' AND '$sampai'";
    $showCetak = true;
}

$harian = mysqli_query($koneksi, "
    SELECT DATE(tgl_daftar) as tanggal, COUNT(*) as jumlah
    FROM user
    $where
    GROUP BY DATE(tgl_daftar)
    ORDER BY tanggal DESC
");

$bulanan = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan, COUNT(*) as total 
    FROM user
    WHERE tgl_daftar >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
    GROUP BY bulan
    ORDER BY bulan ASC
");

$bulanLabels = [];
$bulanData = [];
$totalSemua = 0;
while ($b = mysqli_fetch_assoc($bulanan)) {
    $bulanLabels[] = date('F Y', strtotime($b['bulan'] . '-01'));
    $bulanData[] = $b['total'];
    $totalSemua += $b['total'];
}
?>

<div class="page-heading">
    <h1 class="page-title">Laporan Pelanggan Baru</h1>
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
                        <input type="date" name="dari" class="form-control" value="<?= $dari ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>" required>
                    </div>
                    <div class="col-md-4 pt-4">
                        <button type="submit" name="filter" class="btn btn-primary mt-2">
                            <i class="fa fa-search"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <form method="post" action="laporanpelangganbarucetak.php" target="_blank" class="mt-3">
                <?php if (isset($_POST['filter'])): ?>
                <input type="hidden" name="dari" value="<?= $dari ?>">
                <input type="hidden" name="sampai" value="<?= $sampai ?>">
                <?php endif; ?>
                <button type="submit" class="btn btn-danger">
                    <i class="fa fa-print"></i> Cetak PDF
                </button>
            </form>

        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Grafik Pelanggan Baru (5 Bulan Terakhir)</div>
                </div>
                <div class="ibox-body">
                    <canvas id="chartPelanggan" height="200"></canvas>
                    <div class="alert alert-info mt-3 text-center p-2">
                        5 bulan terakhir total pelanggan baru: <strong><?= $totalSemua ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Data Pelanggan Baru per Tanggal</div>
                </div>
                <div class="ibox-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Jumlah Pelanggan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = mysqli_fetch_assoc($harian)) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= $row['jumlah'] ?></td>
                                        <td>
                                            <a href="cetakpernotapelanggan.php?tanggal=<?= $row['tanggal'] ?>" target="_blank" class="btn btn-sm btn-primary">
                                                Cetak Nota
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-center">Total Pelanggan</th>
                                    <th colspan="1"><?php echo $totalSemua; ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartPelanggan');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($bulanLabels) ?>,
            datasets: [{
                label: 'Jumlah Pelanggan',
                data: <?= json_encode($bulanData) ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>