<?php
if (isset($_POST['simpan'])) {
    $nm_barang = mysqli_real_escape_string($koneksi, $_POST['nm_barang']);
    $stock = intval($_POST['stock']);

    if (!empty($nm_barang) && $stock >= 0) {
        $query = "INSERT INTO barang (nm_barang, stock) VALUES ('$nm_barang', '$stock')";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            echo "<script>alert('Data berhasil disimpan'); window.location='index.php?page=stokbahan';</script>";
        } else {
            echo "<div class='alert alert-danger'>Gagal menyimpan data.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Harap isi semua field dengan benar.</div>";
    }
}

if (isset($_POST['ubah'])) {
    $id_barang = intval($_POST['id_barang']);
    $nm_barang = mysqli_real_escape_string($koneksi, $_POST['nm_barang']);
    $stock = intval($_POST['stock']);

    if (!empty($nm_barang)) {
        $query = "UPDATE barang SET nm_barang='$nm_barang', stock='$stock' WHERE id_barang='$id_barang'";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            echo "<script>alert('Data berhasil diubah'); window.location='index.php?page=stokbahan';</script>";
        } else {
            echo "<div class='alert alert-danger'>Gagal mengubah data.</div>";
        }
    } else {
        echo "<div class='alert alert-warning'>Harap isi semua field dengan benar.</div>";
    }
}

if (isset($_GET['hapus'])) {
    $id_barang = intval($_GET['hapus']);
    $query = "DELETE FROM barang WHERE id_barang='$id_barang'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=stokbahan';</script>";
    } else {
        echo "<div class='alert alert-danger'>Gagal menghapus data.</div>";
    }
}
?>

<div class="page-heading">
    <h1 class="page-title">Data Stok Bahan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a></li>
        <li class="breadcrumb-item">Stok Bahan</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Stok Bahan</div>
            <button class="btn btn-success btn-sm text-white" data-toggle="modal" data-target="#modalTambah"><i class="fa fa-plus"></i> Tambah Data</button>
        </div>
        <div class="ibox-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="datatable" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bahan</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM barang ORDER BY id_barang DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_barang']); ?></td>
                                <td><?= htmlspecialchars($row['stock']); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalEdit<?= $row['id_barang'] ?>"><i class="fa fa-edit"></i></button>
                                    <a href="?page=stokbahan&hapus=<?= $row['id_barang'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit<?= $row['id_barang'] ?>">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post">
                                            <div class="modal-header">
                                                <h4 class="modal-title">Ubah Bahan</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                                                <div class="form-group">
                                                    <label>Nama Bahan</label>
                                                    <input type="text" class="form-control" name="nm_barang" value="<?= htmlspecialchars($row['nm_barang']) ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Stok</label>
                                                    <input type="number" class="form-control" name="stock" value="<?= htmlspecialchars($row['stock']) ?>" min="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" name="ubah" class="btn btn-primary">Simpan</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Bahan Baru</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Bahan</label>
                        <input type="text" class="form-control" name="nm_barang" placeholder="Nama Bahan" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" class="form-control" name="stock" placeholder="Stok" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>