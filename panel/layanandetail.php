<?php
$id_kategori = $_GET['id'];

// Ambil nama kategori layanan
$kategori = mysqli_query($koneksi, "SELECT nm_layanan FROM kategori_layanan WHERE id_kategori_layanan = '$id_kategori'");
$kategori_data = mysqli_fetch_assoc($kategori);
?>

<div class="page-heading">
    <h1 class="page-title">Detail Jenis Layanan: <?= htmlspecialchars($kategori_data['nm_layanan']); ?></h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item"><a href="index.php?page=layanan">Kategori Layanan</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Daftar Jenis Layanan</div>
            <div class="ibox-tools">
                <a href="index.php?page=layanandetailtambah&id=<?= $id_kategori ?>" class="btn btn-success btn-sm text-white">
                    <i class="fa fa-plus"></i> Tambah
                </a>
            </div>
        </div>
        <div class="ibox-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="datatable" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Layanan</th>
                            <th>Estimasi Waktu (Hari)</th>
                            <th>Tarif Perkilo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM jenis_layanan WHERE id_kategori_layanan = '$id_kategori' ORDER BY id_jns_layanan DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['jenis_layanan']); ?></td>
                                <td><?= htmlspecialchars($row['estimasi_waktu']); ?> Hari</td>
                                <td>Rp <?= number_format($row['tarif'], 0, ',', '.'); ?></td>
                                <td>
                                    <a href="index.php?page=layanandetailedit&id=<?= $row['id_jns_layanan']; ?>&id_kategori=<?= $id_kategori ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="layanandetailhapus.php?id=<?= $row['id_jns_layanan']; ?>&id_kategori=<?= $id_kategori ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>