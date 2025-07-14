<?php


$tgl_hari_ini = date('Y-m-d');
$bln_ini = date('Y-m');

// Pelanggan
$pelanggan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE DATE(tgl_daftar) = '$tgl_hari_ini'"))['total'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user"))['total'];

// Pendapatan
$pendapatan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tgl_transaksi) = '$tgl_hari_ini'"))['total'] ?? 0;
$pendapatan_bln_ini = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(total) as total FROM transaksi WHERE DATE_FORMAT(tgl_transaksi, '%Y-%m') = '$bln_ini'"))['total'] ?? 0;

// Transaksi per jenis layanan
$qLayanan = mysqli_query($koneksi, "SELECT jl.jenis_layanan, COUNT(t.id_transaksi) as total FROM transaksi t JOIN jenis_layanan jl ON t.id_jns_layanan = jl.id_jns_layanan GROUP BY jl.jenis_layanan");
$jenis_layanan = $total_layanan = [];
while ($row = mysqli_fetch_assoc($qLayanan)) {
    $jenis_layanan[] = $row['jenis_layanan'];
    $total_layanan[] = $row['total'];
}

// Pelanggan per kecamatan
$qKecamatan = mysqli_query($koneksi, "SELECT kecamatan, COUNT(*) as total FROM pelanggan GROUP BY kecamatan");
$kecamatan = $total_kecamatan = [];
while ($row = mysqli_fetch_assoc($qKecamatan)) {
    $kecamatan[] = $row['kecamatan'];
    $total_kecamatan[] = $row['total'];
}

// Transaksi 7 hari terakhir
$bar_labels = $bar_data = [];
for ($i = 30; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i day"));
    $label = date('d M', strtotime($tgl));
    $bar_labels[] = $label;
    $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE DATE(tgl_transaksi) = '$tgl'");
    $bar_data[] = (int)mysqli_fetch_assoc($res)['total'];
}

// Line chart pendapatan harian bulan ini
$line_labels = $line_data = [];
$days_in_month = date('t');
for ($i = 1; $i <= $days_in_month; $i++) {
    $tgl = date("$bln_ini-" . str_pad($i, 2, '0', STR_PAD_LEFT));
    $line_labels[] = $i;
    $res = mysqli_query($koneksi, "SELECT SUM(total) as total FROM transaksi WHERE DATE(tgl_transaksi) = '$tgl'");
    $line_data[] = (float)(mysqli_fetch_assoc($res)['total'] ?? 0);
}

// Pelanggan baru per hari bulan ini
$chart_pelanggan_labels = $chart_pelanggan_data = [];
for ($i = 1; $i <= $days_in_month; $i++) {
    $tgl = date("$bln_ini-" . str_pad($i, 2, '0', STR_PAD_LEFT));
    $chart_pelanggan_labels[] = $i;
    $res = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM user WHERE DATE(tgl_daftar) = '$tgl'");
    $chart_pelanggan_data[] = (int)mysqli_fetch_assoc($res)['total'];
}

?>

<?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>

    <div class="page-content fade-in-up">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-success color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong"><?= $pelanggan_hari_ini ?></h2>
                        <div class="m-b-5">Pelanggan Hari Ini</div><i class="ti-user widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-info color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong"><?= $total_pelanggan ?></h2>
                        <div class="m-b-5">Total Pelanggan</div><i class="ti-bar-chart widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-warning color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">Rp <?= number_format($pendapatan_hari_ini) ?></h2>
                        <div class="m-b-5">Pendapatan Hari Ini</div><i class="ti-wallet widget-stat-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="ibox bg-danger color-white widget-stat">
                    <div class="ibox-body">
                        <h2 class="m-b-5 font-strong">Rp <?= number_format($pendapatan_bln_ini) ?></h2>
                        <div class="m-b-5">Pendapatan Bulan Ini</div><i class="ti-wallet widget-stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Total Transaksi Berdasarkan Jenis Layanan</div>
                    </div>
                    <div class="ibox-body">
                        <a href="index.php?page=laporanberjenislayanan">
                        <div style="width:100%; height:250px">
                            <canvas id="chartLayanan"></canvas>
                        </div></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Pelanggan Berdasarkan Kecamatan</div>
                    </div>
                    <div class="ibox-body">
                        <a href="index.php?page=laporanberwilayah">
                        <div style="width:100%; height:250px">
                            <canvas id="chartKecamatan"></canvas>
                        </div></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title"> Total Transaksi order Bulan Ini</div>
                    </div>
                    <div class="ibox-body">
                        <a href="index.php?page=laporantotalorder">
                        <div style="width:100%; height:250px">
                            <canvas id="chartTransaksi"></canvas>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Total Pendapatan Harian Bulan Ini</div>
                    </div>
                    <div class="ibox-body">
                        <a href="index.php?page=laporanpendapatan">
                        <div style="width:100%; height:250px">
                            <canvas id="chartPendapatan"></canvas>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-head">
                        <div class="ibox-title">Total Pelanggan Baru Bulan Ini</div>
                    </div>
                    <div class="ibox-body">
                        <a href="index.php?page=laporanpelangganbaru">
                        <div style="width:100%; height:300px;">
                            <canvas id="chartPelangganBaru"></canvas>
                        </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

<?php else: ?>

    <div class="page-content fade-in-up">
        <div class="text-center mb-4">
            <h4><strong>SELAMAT DATANG DI MW LAUNDRY </strong></h4>
            <em>Cuci kilat, hasil memukau! Coba layanan laundry kami sekarang</em>
            <hr style="width: 50%; margin: 10px auto;">
        </div>

        <div class="container-fluid mb-4">
            <div class="row justify-content-center">
                <div class="col-md-6 p-2">
                    <div style="height: 400px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        <img src="../assets/uploads/l1.jpg" alt="Foto Laundry 1" class="img-fluid w-100 h-100" style="object-fit: cover; border-radius: 10px;">
                    </div>
                </div>
                <div class="col-md-6 p-2">
                    <div style="height: 400px; overflow: hidden; display: flex; justify-content: center; align-items: center;">
                        <img src="../assets/uploads/l2.jpg" alt="Foto Laundry 2" class="img-fluid w-100 h-100" style="object-fit: cover; border-radius: 10px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-5">
            <strong>Alamat : Jalan Fatmawati Soekarno, Taba Lestari, Kec. Lubuklinggau Timur I, Kota. Lubuklinggau, Sumatera Selatan</strong>
        </div>
    </div>



<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const chartLayanan = new Chart(document.getElementById('chartLayanan'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($jenis_layanan) ?>,
            datasets: [{
                data: <?= json_encode($total_layanan) ?>,
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de']
            }]
        }
    });

    const chartKecamatan = new Chart(document.getElementById('chartKecamatan'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($kecamatan) ?>,
            datasets: [{
                data: <?= json_encode($total_kecamatan) ?>,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
            }]
        }
    });

    const chartTransaksi = new Chart(document.getElementById('chartTransaksi'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($bar_labels) ?>,
            datasets: [{
                label: 'Jumlah Transaksi',
                data: <?= json_encode($bar_data) ?>,
                backgroundColor: '#17a2b8'
            }]
        }
    });

    const chartPendapatan = new Chart(document.getElementById('chartPendapatan'), {
        type: 'line',
        data: {
            labels: <?= json_encode($line_labels) ?>,
            datasets: [{
                label: 'Pendapatan',
                data: <?= json_encode($line_data) ?>,
                borderColor: '#007bff',
                fill: false
            }]
        }
    });

    const chartPelangganBaru = new Chart(document.getElementById('chartPelangganBaru'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_pelanggan_labels) ?>,
            datasets: [{
                label: 'Pelanggan Baru',
                data: <?= json_encode($chart_pelanggan_data) ?>,
                backgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>