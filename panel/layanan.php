<div class="page-heading">
    <h1 class="page-title">Data Kategori Layanan</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">Kategori Layanan</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Kategori Layanan</div>
            <div class="ibox-tools">
                <a href="index.php?page=layanantambah" class="btn btn-success btn-sm text-white">
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
                            <th>Nama Layanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM kategori_layanan ORDER BY id_kategori_layanan DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_layanan']); ?></td>
                                <td>
                                    <a href="index.php?page=layanandetail&id=<?= $row['id_kategori_layanan']; ?>" class="btn btn-info btn-sm">Detail</a>
                                    <a href="index.php?page=layananedit&id=<?= $row['id_kategori_layanan']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <a href="layananhapus.php?id=<?= $row['id_kategori_layanan']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>