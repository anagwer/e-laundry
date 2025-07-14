<?php
$dari = $sampai = '';
$where = '';
$showCetak = false;

if (isset($_POST['filter'])) {
    $dari = $_POST['dari'];
    $sampai = $_POST['sampai'];
    $where = "WHERE DATE(tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
    $showCetak = true;
}

// Data transaksi per tanggal & jenis layanan
$harian = mysqli_query($koneksi, "
    SELECT DATE(tgl_transaksi) as tanggal, j.jenis_layanan, COUNT(*) as total
    FROM transaksi t
    LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
    $where
    GROUP BY DATE(tgl_transaksi), j.jenis_layanan
    ORDER BY tanggal DESC
");

// Grafik 5 bulan terakhir
$bulanan = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(tgl_transaksi, '%Y-%m') as bulan, COUNT(*) as total 
    FROM transaksi
    WHERE tgl_transaksi >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
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
    <h1 class="page-title">Laporan Transaksi per Jenis Layanan</h1>
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

            <?php if ($showCetak): ?>
                <form method="post" action="laporanberjenislayanancetak.php" target="_blank" class="mt-3">
                    <input type="hidden" name="dari" value="<?= $dari ?>">
                    <input type="hidden" name="sampai" value="<?= $sampai ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-print"></i> Cetak PDF
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Grafik Transaksi (5 Bulan Terakhir)</div>
                </div>
                <div class="ibox-body">
                    <canvas id="chartTransaksi" height="200"></canvas>
                    <div class="alert alert-info mt-3 text-center p-2">
                        Total 5 bulan: <strong><?= $totalSemua ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Data Transaksi per Tanggal & Jenis Layanan</div>
                </div>
                <div class="ibox-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal Transaksi</th>
                                    <th>Jenis Layanan</th>
                                    <th>Total Transaksi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = mysqli_fetch_assoc($harian)) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($row['jenis_layanan']) ?></td>
                                        <td><?= $row['total'] ?></td>
                                        <td>
                                            <a href="cetakpernotajenis.php?tanggal=<?= $row['tanggal'] ?>&jenis=<?= urlencode($row['jenis_layanan']) ?>" target="_blank" class="btn btn-sm btn-primary">
                                                Cetak Nota
                                            </a>

                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-center">Total Transaksi</th>
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
    const ctx = document.getElementById('chartTransaksi');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode($bulanLabels) ?>,
            datasets: [{
                label: 'Total Transaksi',
                data: <?= json_encode($bulanData) ?>,
                backgroundColor: [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.formattedValue + ' transaksi';
                        }
                    }
                }
            }
        }
    });
</script>