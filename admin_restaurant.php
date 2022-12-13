<?php
// ================================ BUSINESS LOGIC ===================
require_once 'libraries/app.php';

function get_query_where()
{
    global $app;

    $filter_search = $app->input_lib->get_post('filter_search');

    $wheres = [];

    // filter: status
    $wheres[] = 'status IN(0,1)';
    $wheres[] = "user_type='restaurant'";

    // filter: search
    if ($filter_search != '') {
        $filter_search_value = $filter_search;
        $wheres[] = "(firstname LIKE '%{$filter_search_value}%' OR lastname LIKE '%{$filter_search_value}%')";
    }

    // render query
    $result = '';
    if (count($wheres) >= 2) {
        $result = implode(' AND ', $wheres);
    } else {
        $result = implode(' ', $wheres);
    }

    return $result;
}

// ดึงข้อมูลร้านอาหารทั้งหมด
function func_items()
{
    global $app;
    $where = get_query_where();
    $sql = "SELECT * FROM `user` WHERE {$where}";
    $query = $app->database_lib->query($sql);
    $items = $query->result();

    return $items;
}

// ดึงข้อมูลร้านอาหารทั้งหมดเฉพาะ id นั้นๆ
function func_item($id = 0)
{
    global $app;
    $sql = "SELECT * FROM user WHERE id={$id}";
    $query = $app->database_lib->query($sql);
    $item = $query->row();

    return $item;
}

// อนุญาติให้ใช้งาน
function func_approve($id = 0)
{
    global $app;
    $data = [
        'status' => 1,
    ];
    $where = "`id`={$id}";

    return $app->database_lib->update('user', $data, $where);
}

// อนุญาติให้ใช้งาน
function func_cancel($id = 0)
{
    global $app;
    $data = [
        'status' => 0,
    ];
    $where = "`id`={$id}";

    return $app->database_lib->update('user', $data, $where);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = $app->input_lib->get_post('action', 'index');
if ($action == 'approve') {
    controller_approve();
} elseif ($action == 'cancel') {
    controller_cancel();
}

// เพิ่ม/แก้ไขข้อมูล
function controller_approve()
{
    global $app;
    $id = $app->input_lib->get_post('id');
    func_approve($id);
    redirect('/admin_restaurant.php');
}

// ลบข้อมูล
function controller_cancel()
{
    global $app;
    $id = $app->input_lib->get_post('id');
    func_cancel($id);
    redirect('/admin_restaurant.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'restaurant';
$items = func_items();

$id = $app->input_lib->get_post('id');
$item = func_item($id);
?>


<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->

<!-- ================================= VIEW=INDEX ================================= -->
<?php if ($action == 'index') {?>
<main>
    <div class="container">
        <div class="row">
            <!-- start: left menu -->
            <div class="col-lg-3">
                <div class="card mb-3 mt-3 d-none d-sm-flex">
                    <?php require_once 'admin_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->

            <!-- start: main content -->
            <div class="col-lg-9">
                <div class="page-header mt-3">
                    <div class="d-flex justify-content-between">
                        <h1>จัดการร้านอาหาร (Restaurant)</h1>
                    </div>
                </div>
                <form id="adminForm" method="post">
                    <div class="card mb-3" id="mainSection">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-sm-5 mb-2">
                                    <?php echo admin_filter_search_html('filter_search'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h2>รออนุมัติ</h2>
                            <div class="row">
                                <?php
                                for ($i = 0; $i < count($items); ++$i) {
                                    $row = $items[$i];
                                    $approve_link = "/admin_restaurant.php?action=approve&id={$row['id']}";

                                    if ($row['status'] != 0) {
                                        continue;
                                    }
                                    ?>
                                <div class="col-4">
                                    <div class="card mb-4">
                                        <img src="<?php echo $row['restaurant_thumbnail']; ?>" width="100%"
                                            class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?php echo $row['restaurant_name']; ?></h5>
                                            <a href="<?php echo $approve_link; ?>" class="btn btn-primary">อนุมัติ</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                }
    ?>
                            </div>

                            <br /><br />
                            <h2>อนุมัติแล้ว</h2>
                            <div class="row">
                                <?php
                        for ($i = 0; $i < count($items); ++$i) {
                            $row = $items[$i];
                            $cancel_link = "/admin_restaurant.php?action=cancel&id={$row['id']}";

                            if ($row['status'] != 1) {
                                continue;
                            }
                            ?>
                                <div class="col-4">
                                    <div class="card mb-4">
                                        <img src="<?php echo $row['restaurant_thumbnail']; ?>" width="100%"
                                            class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?php echo $row['restaurant_name']; ?></h5>
                                            <a href="<?php echo $cancel_link; ?>" class="btn btn-danger">ยกเลิก</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                        }
    ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- end: main content -->
        </div>
    </div>
</main>
<?php }?>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>