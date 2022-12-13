<?php
// ================================ BUSINESS LOGIC ===================
require_once 'libraries/app.php';
function func_order_items()
{
    global $app;
    $profile = $app->profile_lib->get_profile();
    $profile_id = $profile->id;

    $where = "o.rider_id={$profile_id}";
    $sql = "SELECT o.*
                , u.restaurant_name as `restaurant_name` 
                , u.thumbnail as `restaurant_thumbnail` 
                FROM `order` as o
                LEFT JOIN `user` as u ON(o.restaurant_id=u.id) WHERE {$where} ORDER BY o.created_at DESC";
    $query = $app->database_lib->query($sql);
    $items = $query->result();

    return $items;
}

function func_food_items()
{
    global $app;
    $profile = $app->profile_lib->get_profile();
    $profile_id = $profile->id;

    $where = "oi.order_id IN(SELECT `id` FROM `order` WHERE `rider_id`={$profile_id})";
    $sql = "SELECT oi.*
                , fm.title as `food_name` 
                , fm.price as `food_price` 
                , fm.discount_percent as `food_discount_percent` 
                , fm.thumbnail as `food_thumbnail` 
                FROM `order_item` as oi
                LEFT JOIN `food_menu` as fm ON(oi.food_id=fm.id) WHERE {$where}";
    $query = $app->database_lib->query($sql);
    $items = $query->result();

    return $items;
}

function func_delivery_complete($id = 0)
{
    global $app;
    // เตรียมข้อมูลก่อนจะบันทึกลง database
    $data = [
        'status' => 3,
        'updated_at' => now(),
    ];
    $where = "id={$id}";

    // บันทึกข้อมูลลงใน database
    return $app->database_lib->update('order', $data, $where);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = $app->input_lib->get_post('action', 'index');
if ($action == 'delivery_complete') {
    controller_delivery_complete();
}

function controller_delivery_complete()
{
    global $app;
    $order_id = $app->input_lib->get_post('id');

    func_delivery_complete($order_id);

    redirect('/rider_history_delivery.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$restaurant_id = $app->input_lib->get_post('restaurant_id', 0);

$active_menu = 'history_delivery';
$order_items = func_order_items();
$food_items = func_food_items();

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
                    <?php require_once 'rider_menu.php'; ?>
                </div>
            </div>
            <!-- end: left menu -->
            <div class="col-lg-9 mt-3">
                <?php
                    if (count($order_items) <= 0) {
                        echo '<h6 class="text-center p-4">ไม่มีข้อมูลสำหรับแสดงผล...</h6>';
                    } else {
                        for ($i = 0; $i < count($order_items); ++$i) {
                            $row = $order_items[$i];
                            $delivery_completed_link = "/rider_history_delivery.php?action=delivery_complete&id={$row['id']}";
                            $customer_detail_link = "/rider_customer_detail.php?id={$row['customer_id']}";

                            $order_status_text = 'รอยืนยันการสั่งซื้อ';
                            $order_status_bg = 'text-bg-secondary';
                            if ($row['status'] == 1) {
                                $order_status_text = 'รับออร์เดอร์เรียบร้อยแล้ว...กำลังจัดเตรียมอาหาร';
                                $order_status_bg = 'text-bg-primary';
                            } elseif ($row['status'] == 2) {
                                $order_status_text = 'กำลังนำอาหารไปส่งลูกค้า... รอสักครู่';
                                $order_status_bg = 'text-bg-warning';
                            } elseif ($row['status'] == 3) {
                                $order_status_text = 'นำส่งอาหารและได้รับชำระเงิน เรียบร้อยแล้ว';
                                $order_status_bg = 'text-bg-success';
                            }
                            ?>
                <div class="border mb-3">
                    <div class="row g-0">
                        <div class="col-6">
                            <div class="<?php echo $order_status_bg; ?> p-2">ร้าน:
                                <?php echo $row['restaurant_name']; ?> /
                                สั่งซื้อเมื่อ: <?php echo $row['created_at']; ?>
                                / รหัสสั่งซื้อ #<?php echo $row['id']; ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="<?php echo $order_status_bg; ?> p-2 h-100"><?php echo $order_status_text; ?>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0">
                        <div class="col-12">
                            <div class="text-bg-light p-2">
                                <div class="row">
                                    <?php
                                        for ($j = 0; $j < count($food_items); ++$j) {
                                            $row2 = $food_items[$j];
                                            if ($row2['order_id'] == $row['id']) {
                                                ?>
                                    <div class="col-2 mt-2">
                                        <img src="<?php echo $row2['food_thumbnail']; ?>" width="100%">
                                    </div>
                                    <div class="col-10">
                                        <h5><?php echo $row2['food_name']; ?></h5>
                                        <div>x<?php echo $row2['food_amount']; ?></div>
                                    </div>
                                    <?php
                                            }
                                        }
                            ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-0 border">
                        <div class="col-8">
                            <div class="text-bg-light p-2 d-flex justify-content-between">
                                <?php
                                    if ($row['status'] == 2) {
                                        ?>
                                <div>
                                    <a href="<?php echo $delivery_completed_link; ?>" class="btn btn-warning">ยืนยันการชำระเงินและการส่งอาหารเสร็จสิ้น</a>
                                    <a href="<?php echo $customer_detail_link; ?>" class="btn btn-secondary">แสดงที่อยู่ลูกค้า</a>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 h-100">
                                <h5>ยอดคําสั่งซื้อทั้งหมด: <?php echo number_format($row['total_price']); ?> บาท
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                        }
                    }
    ?>
            </div>
        </div>
    </div>
</main>
<?php } ?>
<!-- end: main body -->

<?php require_once 'footer.php'; ?>