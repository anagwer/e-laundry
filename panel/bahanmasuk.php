<div class="page-heading">
    <h1 class="page-title">Data Bahan Masuk</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">Bahan Masuk</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Bahan Masuk</div>
            <div class="ibox-tools">
                <a href="index.php?page=bahanmasuktambah" class="btn btn-success btn-sm text-white">
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
                            <th>Nama Barang</th>
                            <th>Tanggal Masuk</th>
                            <th>Jumlah Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "
                            SELECT bm.id_barang_masuk, bm.tgl_masuk, bm.jml_masuk, 
                                   b.nm_barang 
                            FROM barang_masuk bm 
                            JOIN barang b ON bm.id_barang = b.id_barang 
                            ORDER BY bm.id_barang_masuk DESC
                        ");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_barang']); ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_masuk'])); ?></td>
                                <td><?= $row['jml_masuk']; ?></td>
                                <td>
                                    <a href="index.php?page=bahanmasukedit&id=<?= $row['id_barang_masuk']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="bahanmasukhapus.php?id=<?= $row['id_barang_masuk']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>