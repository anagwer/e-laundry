<div class="page-heading">
    <h1 class="page-title">Data Pelanggan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">Pelanggan</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Pelanggan</div>
            <div class="ibox-tools">
                <a href="index.php?page=pelanggantambah" class="btn btn-success btn-sm text-white">
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
                            <th>Nama Pelanggan</th>
                            <th>No. Telepon</th>
                            <th>Kecamatan</th>
                            <th>Alamat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $iduser = $_SESSION['user']['id_user'];

                        if ($_SESSION['user']['level_akses'] == 'Admin') {
                            $query = mysqli_query($koneksi, "SELECT * FROM pelanggan ORDER BY id_pelanggan DESC");
                        } else {
                            $query = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_user = '$iduser' ORDER BY id_pelanggan DESC");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_pelanggan']); ?></td>
                                <td><?= htmlspecialchars($row['no_telp']); ?></td>
                                <td><?= htmlspecialchars($row['kecamatan']); ?></td>
                                <td><?= htmlspecialchars($row['alamat']); ?></td>
                                <td>

                                    <a href="index.php?page=pelangganedit&id=<?= $row['id_pelanggan']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="pelangganhapus.php?id=<?= $row['id_pelanggan']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>