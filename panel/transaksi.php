<div class="page-heading">
    <h1 class="page-title">Data Transaksi</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="index.php?page=dashboard"><i class="la la-home font-20"></i></a>
        </li>
        <li class="breadcrumb-item">Transaksi</li>
    </ol>
</div>

<div class="page-content fade-in-up">
    <div class="ibox">
        <div class="ibox-head">
            <div class="ibox-title">Data Transaksi</div>
            <div class="ibox-tools">
                <a href="index.php?page=transaksitambah" class="btn btn-success btn-sm text-white">
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
                            <th>Tanggal Transaksi</th>
                            <th>Tanggal Masuk</th>
                            <th>Invoice</th>
                            <th>Nama</th>
                            <th>Layanan</th>
                            <th>Total Bayar</th>
                            <th>Status Bayar</th>
                            <th>Status Proses</th>
                            <th>Bukti Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if ($_SESSION['user']['level_akses'] == 'Admin') {
                            $query = mysqli_query($koneksi, "
                            SELECT t.*, p.nm_pelanggan, l.nm_layanan, j.jenis_layanan
                            FROM transaksi t
                            LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                            LEFT JOIN kategori_layanan l ON t.id_jns_layanan = l.id_kategori_layanan
                            LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
                            ORDER BY t.id_transaksi DESC
                        ");
                        } else {
                            $pelanggan = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_user = '{$_SESSION['user']['id_user']}'");
                            $pelanggandata = mysqli_fetch_assoc($pelanggan);
                            $idpelanggan = $pelanggandata['id_pelanggan'] ?? '';

                            $query = mysqli_query($koneksi, "
                            SELECT t.*, p.nm_pelanggan, l.nm_layanan, j.jenis_layanan
                            FROM transaksi t
                            LEFT JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
                            LEFT JOIN kategori_layanan l ON t.id_jns_layanan = l.id_kategori_layanan
                            LEFT JOIN jenis_layanan j ON t.id_jns_layanan = j.id_jns_layanan
                            WHERE t.id_pelanggan = '$idpelanggan'
                            ORDER BY t.id_transaksi DESC
                        ");
                        }
                        while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_transaksi'])); ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_mulai'])); ?></td>
                                <td><?= htmlspecialchars($row['invoice']); ?></td>
                                <td><?= htmlspecialchars($row['nm_pelanggan']); ?></td>
                                <td><?= htmlspecialchars($row['jenis_layanan']); ?></td>
                                <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                                <td><?= htmlspecialchars($row['status_bayar']); ?></td>
                                <td><?= htmlspecialchars($row['status_ambil']); ?></td>
                                <td>
                                    <?php if (!empty($row['bukti'])): ?>
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#buktiModal<?= $row['id_transaksi']; ?>">
                                            Lihat
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="buktiModal<?= $row['id_transaksi']; ?>" tabindex="-1" role="dialog" aria-labelledby="buktiModalLabel<?= $row['id_transaksi']; ?>" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="buktiModalLabel<?= $row['id_transaksi']; ?>">Bukti Pembayaran</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <img src="../assets/uploads/<?= $row['bukti']; ?>" alt="Bukti Pembayaran" class="img-fluid rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php else: ?>
                                        <span class="text-muted">Belum ada</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>
                                        <a href="index.php?page=transaksiedit&id=<?= $row['id_transaksi']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <?php endif; ?>
                                    <a href="index.php?page=transaksidetail&id=<?= $row['id_transaksi']; ?>" class="btn btn-primary btn-sm">Detail</a>
                                    <a href="transaksicetak.php?id=<?= $row['id_transaksi']; ?>" class="btn btn-primary btn-sm" target="_blank">Cetak</a>
                                    <?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>
                                        <a href="transaksihapus.php?id=<?= $row['id_transaksi']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="btn btn-danger btn-sm">Hapus</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>