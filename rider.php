<?php
// ================================ BUSINESS LOGIC ===================
require_once 'libraries/app.php';
?>



<?php
// ================================ CONTROLLER =======================
$active_menu = 'index';
?>



<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->
<main>
    <div class="container">
        <div class="row">
            <!-- start: left menu -->
            <div class="col-lg-3">
                <div class="card mb-3 mt-3 d-none d-sm-flex">
                    <?php require_once 'rider_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->

            <!-- start: main content -->
            <div class="col-lg-9">
                <div class="page-header mt-3">
                    <h1>สำหรับผู้ส่งอาหาร (Rider)</h1>
                    <div>
                        <img src="/assets/img/rider_index.png" width="100%" />
                    </div>
                </div>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>