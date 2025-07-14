<?php
$filter = '';
$where = '';
$line_labels = [];
$line_data = [];
$totalPendapatan = 0;

if (isset($_POST['filter'])) {
    $dari = $_POST['dari'];
    $sampai = $_POST['sampai'];
    $filter = "Dari: " . date('d-m-Y', strtotime($dari)) . " s/d " . date('d-m-Y', strtotime($sampai));
    $where = "WHERE DATE(t.tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
    $periode = "AND DATE(tgl_transaksi) BETWEEN '$dari' AND '$sampai'";
} else {
    $periode = "AND MONTH(tgl_transaksi) = MONTH(CURDATE()) AND YEAR(tgl_transaksi) = YEAR(CURDATE())";
}

// Query data pendapatan
$query = mysqli_query($koneksi, "
    SELECT t.*, p.nm_pelanggan, u.nm_user, j.jenis_layanan, j.tarif, l.nm_layanan
    FROM transaksi t
    LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    LEFT JOIN user u ON t.id_user = u.id_user
    LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
    LEFT JOIN kategori_layanan l ON j.id_kategori_layanan = l.id_kategori_layanan
    $where
    ORDER BY t.tgl_transaksi DESC
");

// Data pendapatan harian untuk line chart
$pendapatanHarian = mysqli_query($koneksi, "
    SELECT DATE(tgl_transaksi) AS tanggal, SUM(total) AS total
    FROM transaksi
    WHERE 1=1 $periode
    GROUP BY DATE(tgl_transaksi)
    ORDER BY tanggal ASC
");

while ($row = mysqli_fetch_assoc($pendapatanHarian)) {
    $line_labels[] = date('d-m-Y', strtotime($row['tanggal']));
    $line_data[] = (float)$row['total'];
}
?>

<div class="page-heading">
    <h1 class="page-title">Laporan Pendapatan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item">Laporan</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Filter Laporan</div>
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
            <?php if (isset($_POST['filter'])): ?>
                <form method="post" action="laporanpendapatancetak.php" target="_blank" class="mt-3">
                    <input type="hidden" name="dari" value="<?= $dari ?>">
                    <input type="hidden" name="sampai" value="<?= $sampai ?>">
                    <button type="submit" class="btn btn-danger"><i class="fa fa-print"></i> Cetak PDF</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <div class="row">
        <div class="col md-4">
            <!-- Chart Pendapatan -->
            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Grafik Pendapatan Harian <?= $filter ? '(' . $filter . ')' : '' ?></div>
                </div>
                <div class="ibox-body">
                    <canvas id="chartPendapatanHarian" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col md-4">

            <div class="ibox">
                <div class="ibox-head">
                    <div class="ibox-title">Data Pendapatan <?= $filter ? '(' . $filter . ')' : '' ?></div>
                </div>
                <div class="ibox-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Invoice</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Layanan</th>
                                    <th>Jenis</th>
                                    <th>Tarif</th>
                                    <th>Berat</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = mysqli_fetch_assoc($query)) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['tgl_transaksi'])) ?></td>
                                        <td><?= htmlspecialchars($row['invoice']) ?></td>
                                        <td><?= htmlspecialchars($row['nm_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($row['nm_layanan']) ?></td>
                                        <td><?= htmlspecialchars($row['jenis_layanan']) ?></td>
                                        <td>Rp <?= number_format($row['tarif'], 0, ',', '.') ?></td>
                                        <td><?= $row['berat'] ?> Kg</td>
                                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php $totalPendapatan += $row['total']; ?>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="8" class="text-center">Total Pendapatan</th>
                                    <th colspan="1">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartPendapatanHarian');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($line_labels) ?>,
            datasets: [{
                label: 'Total Pendapatan',
                data: <?= json_encode($line_data) ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw.toLocaleString('id-ID');
                            return 'Rp ' + value;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
</script>