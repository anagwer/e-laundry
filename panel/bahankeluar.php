<div class="page-heading">
    <h1 class="page-title">Data Bahan Keluar</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">Bahan Keluar</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Bahan Keluar</div>
            <div class="ibox-tools">
                <a href="index.php?page=bahankeluartambah" class="btn btn-success btn-sm text-white">
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
                            <th>Invoice Transaksi</th>
                            <th>Tanggal Keluar</th>
                            <th>Jumlah Keluar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "
                            SELECT bk.id_barang_keluar, bk.tgl_keluar, bk.jml_keluar, 
                                   b.nm_barang, t.invoice 
                            FROM barang_keluar bk 
                            JOIN barang b ON bk.id_barang = b.id_barang 
                            LEFT JOIN transaksi t ON bk.id_transaksi = t.id_transaksi
                            ORDER BY bk.id_barang_keluar DESC
                        ");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_barang']); ?></td>
                                <td><?= $row['invoice'] ? $row['invoice'] : '-'; ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_keluar'])); ?></td>
                                <td><?= $row['jml_keluar']; ?></td>
                                <td>
                                    <a href="index.php?page=bahankeluaredit&id=<?= $row['id_barang_keluar']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="bahankeluarhapus.php?id=<?= $row['id_barang_keluar']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>