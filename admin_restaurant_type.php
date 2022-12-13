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

    // filter: search
    if ($filter_search != '') {
        $filter_search_value = $filter_search;
        $wheres[] = "(title LIKE '%{$filter_search_value}%')";
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

// ดึงข้อมูลจาก restaurant_type ใน database ขึ้นมาทั้งหมด
function func_items()
{
    global $app;
    $where = get_query_where();
    $sql = "SELECT * FROM `restaurant_type` WHERE {$where}";
    $query = $app->database_lib->query($sql);
    $items = $query->result();

    return $items;
}

// ดึงข้อมูลจาก restaurant_type ใน database ขึ้นมาเฉพาะ id นั้นๆ
function func_item($id = 0)
{
    global $app;
    $sql = "SELECT * FROM restaurant_type WHERE id={$id}";
    $query = $app->database_lib->query($sql);
    $item = $query->row();

    return $item;
}

// เพิ่ม/แก้ไข ข้อมูล restaurant_type ใน database
function func_save($form_data = null)
{
    global $app;

    $thumbnail = '/storage/no-thumbnail.png';

    // หากค่า id ไม่เท่ากับ 0 แสดงว่าคือการอัพเดต
    if ($form_data['id'] != 0) {
        // ดึงข้อมูลเดิมมาจาก database
        $sql = "SELECT * FROM `restaurant_type` WHERE `id`={$form_data['id']}";
        $data_db = $app->database_lib->query($sql)->row();

        if (empty($data_db)) {
            return false;
        }

        $thumbnail = $data_db['thumbnail'];
    }

    // หากมีการอัพโหลดรูปภาพ เข้ามาใหม่ ให้อัพโหลดรูปภาพใหม่
    if (isset($_FILES['thumbnail'])) {
        // upload thumbnail
        $upload_config = [
            'upload_path' => 'storage',
        ];
        $thumbnail_data = $app->upload_lib->do_upload('thumbnail', $upload_config);
        if ($thumbnail_data['status']) {
            $thumbnail = '/storage/'.$thumbnail_data['new_file_name'];
        }
    }

    // หากค่า id=0 แสดงว่าคือ การเพิ่ม
    if ($form_data['id'] == 0) {
        // เตรียมข้อมูลสำหรับบันทึกลงใน database
        $data = [
            'title' => $form_data['title'],
            'thumbnail' => $thumbnail,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $app->database_lib->insert('restaurant_type', $data);
    }
    // หากไม่ใช่ ก็คือการ อัพเดต
    else {
        // เตรียมข้อมูลสำหรับบันทึกลงใน database
        $data = [
            'title' => $form_data['title'],
            'thumbnail' => $thumbnail,
            'updated_at' => now(),
        ];
        $where = "`id`={$form_data['id']}";

        return $app->database_lib->update('restaurant_type', $data, $where);
    }
}

// ลบข้อมูล restaurant_type ใน database
function func_delete($id = 0)
{
    global $app;
    $where = "`id`={$id}";

    return $app->database_lib->delete('restaurant_type', $where);
}
?>

<?php
// ================================ CONTROLLER =======================

// ดำเนินการเรียก controller function ตามค่าที่รับมาจาก action
$action = $app->input_lib->get_post('action', 'index');
if ($action == 'edit') {
    controller_edit();
} elseif ($action == 'delete') {
    controller_delete();
}

// เพิ่ม/แก้ไขข้อมูล
function controller_edit()
{
    global $app;
    $form_data = $app->input_lib->post();

    // set rules for validation data
    $app->form_validation_lib->set_rules('title', 'ประเภทร้านอาหาร', 'required');

    // run validation
    if ($app->form_validation_lib->run()) {
        func_save($form_data);
        redirect('/admin_restaurant_type.php');
    }
}

// ลบข้อมูล
function controller_delete()
{
    global $app;
    $id = $app->input_lib->get_post('id');
    func_delete($id);
    redirect('/admin_restaurant_type.php');
}

// เตรียมค่าที่ส่งเข้าไปที่ view
$active_menu = 'restaurant_type';
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
                        <h1>จัดการประเภทร้านอาหาร (Restaurant Type)</h1>
                    </div>
                </div>
                <form id="adminForm" method="post">
                    <div class="card mb-3" id="mainSection">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12 col-sm-5 mb-2">
                                    <?php echo admin_filter_search_html('filter_search'); ?>
                                </div>
                                <div class="col-12 col-sm-7 mb-2">
                                    <div class="d-flex justify-content-end">
                                        <a href="/admin_restaurant_type.php?action=edit&id=0"
                                            class="btn btn-primary">เพิ่ม</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                for ($i = 0; $i < count($items); ++$i) {
                                    $row = $items[$i];
                                    $edit_link = "/admin_restaurant_type.php?action=edit&id={$row['id']}";
                                    $delete_link = "/admin_restaurant_type.php?action=delete&id={$row['id']}";
                                    ?>
                                <div class="col-4">
                                    <div class="card mb-4">
                                        <img src="<?php echo $row['thumbnail']; ?>" width="100%" class="card-img-top">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?php echo $row['title']; ?></h5>
                                            <a href="<?php echo $edit_link; ?>" class="btn btn-primary">แก้ไข</a>
                                            <a href="<?php echo $delete_link; ?>" class="btn btn-danger">ลบ</a>
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

<!-- ================================= VIEW=ADD/EDIT ================================= -->
<?php } elseif ($action == 'edit') {?>
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
                <!-- start: page header -->
                <form id="adminForm" method="post" enctype="multipart/form-data">
                    <div class="page-header mt-3">
                        <div class="d-flex justify-content-between">
                            <h1>จัดการประเภทร้านอาหาร [แก้ไข]</h1>
                            <div>
                                <a class="btn btn-outline-secondary align-self-end mb-2"
                                    href="/admin_restaurant_type.php">
                                    <i class="bi-x me-1"></i> ยกเลิก
                                </a>
                                <button class="btn btn-primary align-self-end mb-2" type="submit">
                                    <i class="bi-save me-1"></i> บันทึกข้อมูล
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- end: page header -->
                    <div class="card mb-5">
                        <div class="card-body">
                            <?php echo validation_errors(); ?>
                            <!-- field: title -->
                            <div class="row mb-4">
                                <label for="titleLabel" class="col-sm-3 col-form-label form-label">ประเภทร้านอาหาร *
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="title" id="titleLabel"
                                        value="<?php echo array_value($item, 'title'); ?>" required />
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-3 col-form-label form-label">รูปภาพประกอบ
                                </label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="thumbnail" />
                                    <img src="<?php echo array_value($item, 'thumbnail'); ?>"
                                        class="w-50 mt-2">
                                </div>
                            </div>
                            <!-- End field: title -->

                            <input type="hidden" name="id"
                                value="<?php echo array_value($item, 'id', 0); ?>" />
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