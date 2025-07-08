<?php include '../koneksi.php'; ?>

<?php
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Silakan login terlebih dahulu');</script>";
    echo "<script>window.location = '../login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>E-Laundry</title>
    <!-- GLOBAL MAINLY STYLES-->
    <link href="../assets/admin/assets/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../assets/admin/assets/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="../assets/admin/assets/vendors/themify-icons/css/themify-icons.css" rel="stylesheet" />
    <!-- PLUGINS STYLES-->
    <link href="../assets/admin/assets/vendors/jvectormap/jquery-jvectormap-2.0.3.css" rel="stylesheet" />
    <!-- THEME STYLES-->
    <link href="../assets/admin/assets/vendors/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="../assets/admin/assets/css/main.min.css" rel="stylesheet" />

    <script src="../assets/admin/assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- PAGE LEVEL STYLES-->
    <style>
        button {
            cursor: pointer;
        }
    </style>
</head>

<body class="fixed-navbar">
    <div class="page-wrapper">
        <!-- START HEADER-->
        <header class="header">
            <div class="page-brand">
                <a class="link" href="index.php?page=dashboard">
                    <span class="brand">E-Laundry

                    </span>
                    <span class="brand-mini">ELD</span>
                </a>
            </div>
            <div class="flexbox flex-1">
                <!-- START TOP-LEFT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li>
                        <a class="nav-link sidebar-toggler js-sidebar-toggler"><i class="ti-menu"></i></a>
                    </li>
                </ul>
                <!-- END TOP-LEFT TOOLBAR-->
                <!-- START TOP-RIGHT TOOLBAR-->
                <ul class="nav navbar-toolbar">

                    <li class="dropdown dropdown-user">
                        <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                            <img src="../assets/uploads/avatar.png" />
                            <span></span><?= $_SESSION['user']['nm_user']; ?><i class="fa fa-angle-down m-l-5"></i></a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="index.php?page=profile"><i class="fa fa-user"></i>Profile</a>
                            <li class="dropdown-divider"></li>
                            <a class="dropdown-item" href="logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fa fa-power-off"></i>Logout</a>
                        </ul>
                    </li>
                </ul>
                <!-- END TOP-RIGHT TOOLBAR-->
            </div>
        </header>
        <!-- END HEADER-->
        <!-- START SIDEBAR-->
        <nav class="page-sidebar" id="sidebar">
            <div id="sidebar-collapse">
                <div class="admin-block d-flex">
                    <div>
                        <img src="../assets/uploads/avatar.png" width="45px" />
                    </div>
                    <div class="admin-info">
                        <div class="font-strong"><?= $_SESSION['user']['nm_user']; ?></div><small>Status : <?= $_SESSION['user']['level_akses']; ?></small>
                    </div>
                </div>
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
                ?>

                <ul class="side-menu metismenu">
                    <li>
                        <a class="<?= ($page == 'dashboard' || $page == '') ? 'active' : '' ?>" href="index.php?page=dashboard">
                            <i class="sidebar-item-icon fa fa-home"></i>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    </li>

                    <?php if ($_SESSION['user']['level_akses'] == 'Admin'): ?>
                        <li class="heading">Main Menu</li>
                        <li>
                            <?php
                            $dataMasterPages = ['layanan', 'layanantambah', 'layananedit', 'layanandetail', 'layanandetailtambah', 'layanandetailedit', 'transaksi', 'transaksitambah', 'transaksiedit', 'transaksidetail', 'stokbahan', 'bahanmasuk', 'bahanmasuktambah', 'bahankeluar', 'bahankeluartambah', 'pelanggan', 'pelanggantambah', 'pelangganedit', 'user', 'usertambah', 'useredit'];
                            $isDataMaster = in_array($page, $dataMasterPages);
                            ?>
                            <a href="javascript:;" class="<?= $isDataMaster ? 'active' : '' ?>">
                                <i class="sidebar-item-icon fa fa-database"></i>
                                <span class="nav-label">Data Master</span>
                                <i class="fa fa-angle-left arrow"></i>
                            </a>
                            <ul class="nav-2-level collapse <?= $isDataMaster ? 'in' : '' ?>">
                                <li><a class="<?= in_array($page, ['layanan', 'layanantambah', 'layananedit', 'layanandetail', 'layanandetailtambah', 'layanandetailedit']) ? 'active' : '' ?>" href="index.php?page=layanan"><i class="fa fa-cogs mr-2"></i>Data Layanan</a></li>
                                <li><a class="<?= in_array($page, ['transaksi', 'transaksitambah', 'transaksiedit', 'transaksidetail']) ? 'active' : '' ?>" href="index.php?page=transaksi"><i class="fa fa-exchange mr-2"></i>Data Transaksi</a></li>
                                <li><a class="<?= ($page == 'stokbahan') ? 'active' : '' ?>" href="index.php?page=stokbahan"><i class="fa fa-archive mr-2"></i>Data Stok Bahan</a></li>
                                <li><a class="<?= in_array($page, ['bahanmasuk', 'bahanmasuktambah', 'bahanmasukedit']) ? 'active' : '' ?>" href="index.php?page=bahanmasuk"><i class="fa fa-sign-in mr-2"></i>Data Bahan Masuk</a></li>
                                <li><a class="<?= in_array($page, ['bahankeluar', 'bahankeluartambah', 'bahankeluaredit']) ? 'active' : '' ?>" href="index.php?page=bahankeluar"><i class="fa fa-sign-out mr-2"></i>Data Bahan Keluar</a></li>
                                <li><a class="<?= in_array($page, ['pelanggan', 'pelanggantambah', 'pelangganedit']) ? 'active' : '' ?>" href="index.php?page=pelanggan"><i class="fa fa-users mr-2"></i>Data Pelanggan</a></li>
                                <li><a class="<?= in_array($page, ['user', 'usertambah', 'useredit']) ? 'active' : '' ?>" href="index.php?page=user"><i class="fa fa-user-circle mr-2"></i>Data User</a></li>
                            </ul>
                        </li>

                        <li class="heading">Laporan</li>
                        <li>
                            <?php
                            $laporanPages = ['laporanpendapatan', 'laporantotalorder', 'laporanberjenislayanan', 'laporanpelangganbaru', 'laporanberwilayah', 'laporanstok'];
                            $isLaporan = in_array($page, $laporanPages);
                            ?>
                            <a href="javascript:;" class="<?= $isLaporan ? 'active' : '' ?>">
                                <i class="sidebar-item-icon fa fa-file-text-o"></i>
                                <span class="nav-label">Laporan</span>
                                <i class="fa fa-angle-left arrow"></i>
                            </a>
                            <ul class="nav-2-level collapse <?= $isLaporan ? 'in' : '' ?>">
                                <li><a class="<?= $page == 'laporanpendapatan' ? 'active' : '' ?>" href="index.php?page=laporanpendapatan"><i class="fa fa-line-chart mr-2"></i>Laporan Pendapatan</a></li>
                                <li><a class="<?= $page == 'laporantotalorder' ? 'active' : '' ?>" href="index.php?page=laporantotalorder"><i class="fa fa-shopping-cart mr-2"></i>Total Order</a></li>
                                <li><a class="<?= $page == 'laporanberjenislayanan' ? 'active' : '' ?>" href="index.php?page=laporanberjenislayanan"><i class="fa fa-th-list mr-2"></i>Jenis Layanan</a></li>
                                <li><a class="<?= $page == 'laporanpelangganbaru' ? 'active' : '' ?>" href="index.php?page=laporanpelangganbaru"><i class="fa fa-user-plus mr-2"></i>Pelanggan Baru</a></li>
                                <li><a class="<?= $page == 'laporanberwilayah' ? 'active' : '' ?>" href="index.php?page=laporanberwilayah"><i class="fa fa-map-marker mr-2"></i>Wilayah</a></li>
                                <li><a class="<?= $page == 'laporanstok' ? 'active' : '' ?>" href="index.php?page=laporanstok"><i class="fa fa-cube mr-2"></i>Stok Bahan</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if ($_SESSION['user']['level_akses'] == 'Pelanggan'): ?>
                        <li class="heading">Main Menu</li>
                        <li>
                            <a class="<?= in_array($page, ['layanan', 'layanantambah', 'layananedit', 'layanandetail', 'layanandetailtambah', 'layanandetailedit']) ? 'active' : '' ?>" href="index.php?page=layanan"><i class="fa fa-cogs mr-2"></i>Data Layanan
                            </a>
                        </li>
                        <li>
                            <?php
                            $pelangganPages = ['transaksi', 'transaksitambah', 'transaksiedit', 'transaksidetail'];
                            $isPelangganMenu = in_array($page, $pelangganPages);
                            ?>
                            <a href="javascript:;" class="<?= $isPelangganMenu ? 'active' : '' ?>">
                                <i class="sidebar-item-icon fa fa-briefcase"></i>
                                <span class="nav-label">Transaksi</span>
                                <i class="fa fa-angle-left arrow"></i>
                            </a>
                            <ul class="nav-2-level collapse <?= $isPelangganMenu ? 'in' : '' ?>">
                                <li><a class="<?= $isPelangganMenu ? 'active' : '' ?>" href="index.php?page=transaksi"><i class="fa fa-exchange mr-2"></i>Data Transaksi</a></li>
                                <li><a class="<?= in_array($page, ['pelanggan', 'pelanggantambah', 'pelangganedit']) ? 'active' : '' ?>" href="index.php?page=pelanggan"><i class="fa fa-users mr-2"></i>Data Pelanggan</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </nav>
        <!-- END SIDEBAR-->
        <div class="content-wrapper">
            <!-- START PAGE CONTENT-->

            <?php
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'dashboard') {
                    include 'dashboard.php';
                } elseif ($_GET['page'] == 'layanan') {
                    include 'layanan.php';
                } elseif ($_GET['page'] == 'layanantambah') {
                    include 'layanantambah.php';
                } elseif ($_GET['page'] == 'layananedit') {
                    include 'layananedit.php';
                } elseif ($_GET['page'] == 'layanandetail') {
                    include 'layanandetail.php';
                } elseif ($_GET['page'] == 'layanandetailtambah') {
                    include 'layanandetailtambah.php';
                } elseif ($_GET['page'] == 'layanandetailedit') {
                    include 'layanandetailedit.php';
                } elseif ($_GET['page'] == 'transaksi') {
                    include 'transaksi.php';
                } elseif ($_GET['page'] == 'transaksitambah') {
                    include 'transaksitambah.php';
                } elseif ($_GET['page'] == 'transaksiedit') {
                    include 'transaksiedit.php';
                } elseif ($_GET['page'] == 'transaksidetail') {
                    include 'transaksidetail.php';
                } elseif ($_GET['page'] == 'stokbahan') {
                    include 'stokbahan.php';
                } elseif ($_GET['page'] == 'bahanmasuk') {
                    include 'bahanmasuk.php';
                } elseif ($_GET['page'] == 'bahanmasuktambah') {
                    include 'bahanmasuktambah.php';
                } elseif ($_GET['page'] == 'bahanmasukedit') {
                    include 'bahanmasukedit.php';
                } elseif ($_GET['page'] == 'bahankeluar') {
                    include 'bahankeluar.php';
                } elseif ($_GET['page'] == 'bahankeluartambah') {
                    include 'bahankeluartambah.php';
                } elseif ($_GET['page'] == 'bahankeluaredit') {
                    include 'bahankeluaredit.php';
                } elseif ($_GET['page'] == 'pelanggan') {
                    include 'pelanggan.php';
                } elseif ($_GET['page'] == 'pelanggantambah') {
                    include 'pelanggantambah.php';
                } elseif ($_GET['page'] == 'pelangganedit') {
                    include 'pelangganedit.php';
                } elseif ($_GET['page'] == 'user') {
                    include 'user.php';
                } elseif ($_GET['page'] == 'usertambah') {
                    include 'usertambah.php';
                } elseif ($_GET['page'] == 'useredit') {
                    include 'useredit.php';
                } elseif ($_GET['page'] == 'profile') {
                    include 'profile.php';
                } elseif ($_GET['page'] == 'laporanpendapatan') {
                    include 'laporanpendapatan.php';
                } elseif ($_GET['page'] == 'laporantotalorder') {
                    include 'laporantotalorder.php';
                } elseif ($_GET['page'] == 'laporanberjenislayanan') {
                    include 'laporanberjenislayanan.php';
                } elseif ($_GET['page'] == 'laporanpelangganbaru') {
                    include 'laporanpelangganbaru.php';
                } elseif ($_GET['page'] == 'laporanberwilayah') {
                    include 'laporanberwilayah.php';
                } elseif ($_GET['page'] == 'laporanstok') {
                    include 'laporanstok.php';
                }
            } else {
                include 'dashboard.php';
            }
            ?>

            <!-- END PAGE CONTENT-->
            <footer class="page-footer">
                <div class="font-13"><?= date('Y'); ?> © <b>E-Laundry</b> - All rights reserved.</div>
                <div class="to-top"><i class="fa fa-angle-double-up"></i></div>
            </footer>
        </div>
    </div>
    <!-- BEGIN THEME CONFIG PANEL-->

    <!-- END THEME CONFIG PANEL-->
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS-->
    <script src="../assets/admin/assets/vendors/popper.js/dist/umd/popper.min.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/metisMenu/dist/metisMenu.min.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
    <script src="../assets/admin/assets/vendors/chart.js/dist/Chart.min.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/jvectormap/jquery-jvectormap-2.0.3.min.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
    <script src="../assets/admin/assets/vendors/jvectormap/jquery-jvectormap-us-aea-en.js" type="text/javascript"></script>
    <!-- CORE SCRIPTS-->
    <script src="../assets/admin/assets/js/app.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script src="../assets/admin/assets/js/scripts/dashboard_1_demo.js" type="text/javascript"></script>
    <!-- PAGE LEVEL PLUGINS-->
    <script src="../assets/admin/assets/vendors/DataTables/datatables.min.js" type="text/javascript"></script>
    <!-- PAGE LEVEL SCRIPTS-->
    <script type="text/javascript">
        $(function() {
            $('#datatable').DataTable({
                pageLength: 10,
                //"ajax": './assets/demo/data/table_data.json',
                /*"columns": [
                    { "data": "name" },
                    { "data": "office" },
                    { "data": "extn" },
                    { "data": "start_date" },
                    { "data": "salary" }
                ]*/
            });
        })
    </script>
</body>

</html>