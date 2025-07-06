<div class="page-heading">
    <h1 class="page-title">Data User</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">User</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data User</div>
            <div class="ibox-tools">
                <a href="index.php?page=usertambah" class="btn btn-success btn-sm text-white">
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
                            <th>Nama User</th>
                            <th>Username</th>
                            <th>Tanggal Daftar</th>
                            <th>Level Akses</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY id_user DESC");
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nm_user']); ?></td>
                                <td><?= htmlspecialchars($row['username']); ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_daftar'])); ?></td>
                                <td><?= htmlspecialchars($row['level_akses']); ?></td>
                                <td>
                                    <?php if ($row['level_akses'] == 'Admin'): ?>
                                        <a href="index.php?page=useredit&id=<?= $row['id_user']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <?php endif; ?>
                                    <a href="userhapus.php?id=<?= $row['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>