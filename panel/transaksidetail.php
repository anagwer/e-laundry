<?php
// Ambil data transaksi yang akan dilihat
if (isset($_GET['id'])) {
    $id_transaksi = $_GET['id'];
    $query_transaksi = mysqli_query($koneksi, "SELECT t.*, p.nm_pelanggan, p.no_telp, p.alamat as alamat_pelanggan, 
                                               jl.jenis_layanan, jl.estimasi_waktu, jl.tarif,
                                               kl.nm_layanan as kategori_layanan,
                                               u.nm_user as nama_user
                                               FROM transaksi t 
                                               JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                                               JOIN jenis_layanan jl ON t.id_jns_layanan = jl.id_jns_layanan
                                               JOIN kategori_layanan kl ON jl.id_kategori_layanan = kl.id_kategori_layanan
                                               JOIN user u ON t.id_user = u.id_user
                                               WHERE t.id_transaksi = '$id_transaksi'");
    $data_transaksi = mysqli_fetch_assoc($query_transaksi);

    if (!$data_transaksi) {
        echo "<script>alert('Data transaksi tidak ditemukan'); window.location='index.php?page=transaksi';</script>";
        exit;
    }
} else {
    echo "<script>alert('ID transaksi tidak valid'); window.location='index.php?page=transaksi';</script>";
    exit;
}

// Fungsi untuk format status dengan warna
function getStatusBadge($status, $type = 'bayar')
{
    if ($type == 'bayar') {
        return ($status == 'Sudah') ? '<span class="badge badge-success">Sudah Bayar</span>' : '<span class="badge badge-warning">Belum Bayar</span>';
    } else {
        if ($status == 'Proses') {
            return '<span class="badge badge-info">Proses</span>';
        } elseif ($status == 'Selesai') {
            return '<span class="badge badge-success">Selesai</span>';
        } else {
            return '<span class="badge badge-primary">Diambil</span>';
        }
    }
}
?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none"><i class="fas fa-home"></i></a></li>
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Transaksi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">Detail Transaksi</h1>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Transaksi - Invoice: <strong>INV-001</strong></h5>
                        <div class="btn-group" role="group">
                            <a href="index.php?page=transaksi" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-4">
                        <!-- INFORMASI PELANGGAN -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Pelanggan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Nama</strong></td>
                                            <td>: <?= $data_transaksi['nm_pelanggan'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>No. Telepon</strong></td>
                                            <td>: <?= $data_transaksi['no_telp'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alamat</strong></td>
                                            <td>: <?= $data_transaksi['alamat_pelanggan'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Antar Jemput</strong></td>
                                            <td>: <span class="badge <?= ($data_transaksi['antar_jemput'] == 'Ya') ? 'badge-success' : 'badge-secondary' ?>"><?= $data_transaksi['antar_jemput'] ?></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Alamat Jemput</strong></td>
                                            <td>: <?= $data_transaksi['alamat_jemput'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMASI LAYANAN -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Informasi Layanan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Kategori</strong></td>
                                            <td>: <?= $data_transaksi['kategori_layanan'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Layanan</strong></td>
                                            <td>: <?= $data_transaksi['jenis_layanan'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estimasi</strong></td>
                                            <td>: <?= $data_transaksi['estimasi_waktu'] ?> hari</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tarif per kg</strong></td>
                                            <td>: Rp. <?= number_format($data_transaksi['tarif']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Berat</strong></td>
                                            <td>: <?= $data_transaksi['berat'] ?> kg</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Operator</strong></td>
                                            <td>: <?= $data_transaksi['nama_user'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <!-- INFORMASI WAKTU -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Informasi Waktu</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Tgl Transaksi</strong></td>
                                            <td>: <?= date('d/m/Y H:i', strtotime($data_transaksi['tgl_transaksi'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tgl Mulai</strong></td>
                                            <td>: <?= date('d/m/Y', strtotime($data_transaksi['tgl_mulai'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tgl Selesai</strong></td>
                                            <td>: <?= date('d/m/Y', strtotime($data_transaksi['tgl_selesai'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tgl Diambil</strong></td>
                                            <td>: <?= $data_transaksi['tgl_diambil'] ? date('d/m/Y H:i', strtotime($data_transaksi['tgl_diambil'])) : '-' ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- INFORMASI PEMBAYARAN -->
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Informasi Pembayaran</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Total</strong></td>
                                            <td>: <h4 class="text-success">Rp. <?= number_format($data_transaksi['total']) ?></h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Metode Bayar</strong></td>
                                            <td>: <?= $data_transaksi['metode_bayar'] ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Bayar</strong></td>
                                            <td>: <?= getStatusBadge($data_transaksi['status_bayar'], 'bayar') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status Ambil</strong></td>
                                            <td>: <?= getStatusBadge($data_transaksi['status_ambil'], 'ambil') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bukti Bayar</strong></td>
                                            <td>:
                                                <?php if ($data_transaksi['bukti']): ?>
                                                    <a href="assets/bukti/<?= $data_transaksi['bukti'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="la la-eye"></i> Lihat Bukti
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Tidak ada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($_SESSION['user']['level_akses'] == 'Admin'): 
    // Ambil jenis layanan dari data_transaksi
    $jenis_layanan = $data_transaksi['jenis_layanan'];
?>
    <!-- KEBUTUHAN BARANG -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-cubes me-2"></i>Kebutuhan Barang</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">

                        <?php if ($jenis_layanan == 'Cuci + Setrika') : ?>
                            <!-- Pewangi -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-flask text-primary" style="font-size: 48px;"></i>
                                        <h4>Pewangi</h4>
                                        <h3 class="text-primary"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Pelembut -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-tint text-success" style="font-size: 48px;"></i>
                                        <h4>Pelembut</h4>
                                        <h3 class="text-success"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Deterjen -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-soap text-warning" style="font-size: 48px;"></i>
                                        <h4>Deterjen</h4>
                                        <h3 class="text-warning"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($jenis_layanan == 'Hanya Cuci') : ?>

                            <!-- Pelembut -->
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-tint text-success" style="font-size: 48px;"></i>
                                        <h4>Pelembut</h4>
                                        <h3 class="text-success"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                            <!-- Deterjen -->
                            <div class="col-md-4 offset-md-2">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-soap text-warning" style="font-size: 48px;"></i>
                                        <h4>Deterjen</h4>
                                        <h3 class="text-warning"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                        <?php elseif ($jenis_layanan == 'Setrika') : ?>

                            <!-- Pewangi -->
                            <div class="col-md-4 offset-md-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <i class="la la-flask text-primary" style="font-size: 48px;"></i>
                                        <h4>Pewangi</h4>
                                        <h3 class="text-primary"><?= $data_transaksi['berat'] ?> Liter</h3>
                                    </div>
                                </div>
                            </div>

                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-info text-center">Tidak ada bahan diperlukan.</div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>