<?php
// ================================ BUSINESS LOGIC ===================
require_once 'libraries/app.php';

function auth_login($email = '', $password = '')
{
    global $app;

    // นำ รหัสผ่านที่กรอก มาเข้ารหัส md5 ก่อน เพื่อใช้สำหรับตรวจสอบกับ database
    $hash_password = md5($password);

    // ดึงค่ามาจาก database ทั้งหมด
    $query = $app->database_lib->query('SELECT * FROM user');
    $results = $query->result();

    foreach ($results as $row) {
        if ($row['email'] == $email && $row['password'] == $hash_password) {
            // หากยังไม่มีการอนุมัติการใช้งาน
            if ($row['status'] == 0) {
                $app->form_validation_lib->set_error("อีเมล์ {$row['email']} ลงทะเบียนเรียบร้อยแล้ว แต่ยังไม่ได้รับอนุญาติให้เข้าใช้งาน, กรุณารอผู้ดูแลระบบอนุมัติสักครู่!");

                return false;
            }
            // หากถูกระงับการใช้งาน
            elseif ($row['status'] == (-1)) {
                $app->form_validation_lib->set_error("อีเมล์ {$row['email']} ถูกระงับการใช้งานชั่วคราว, กรุณาติดต่อผู้ดูแลระบบ!");

                return false;
            }

            // หากอนุญาติให้เข้าใช้งานแล้ว, ให้ดำเนินการ set ค่า login ได้เลย
            $app->session_lib->set('is_login', true);
            $app->session_lib->set('profile_id', $row['id']);
            $app->session_lib->set('email', $row['email']);
            $app->session_lib->set('firstname', $row['firstname']);
            $app->session_lib->set('lastname', $row['lastname']);
            $app->session_lib->set('status', $row['status']);
            $app->session_lib->set('user_type', $row['user_type']);

            return true;
        }
    }

    // หาก อีเมล์ หรือ รหัสผ่าน ไม่ถูกต้อง, จะส่งข้อมูลแจ้งเตือนให้ทราบ
    $app->form_validation_lib->set_error('อีเมล์ หรือ รหัสผ่าน ไม่ถูกต้อง, กรุณาลองใหม่อีกครั้ง!');

    return false;
}

?>

<?php
// ================================ CONTROLLER =======================

$email = $app->input_lib->post('email');
$password = $app->input_lib->post('password');

$app->form_validation_lib->set_rules('email', 'อีเมล์', 'required');
$app->form_validation_lib->set_rules('password', 'รหัสผ่าน', 'required');

// run validation
if ($app->form_validation_lib->run()) {
    // check login
    if (auth_login($email, $password)) {
        if ($app->session_lib->get('user_type') == 'admin') {
            redirect('/admin.php');

            return true;
        } elseif ($app->session_lib->get('user_type') == 'customer') {
            redirect('/customer.php');

            return true;
        } elseif ($app->session_lib->get('user_type') == 'rider') {
            redirect('/rider.php');

            return true;
        } elseif ($app->session_lib->get('user_type') == 'restaurant') {
            redirect('/restaurant.php');

            return true;
        }
        redirect('/');
    }
}

?>
<!-- ============================== VIEW ============================= -->
<?php require_once 'header.php'; ?>
<?php require_once 'nav.php'; ?>
<!-- start: main body -->
<div class="container mt-5 mb-5">
    <div class="row text-center">
        <div class="col">
            <h2>ลงชื่อเข้าสู่ระบบ</h2>
        </div>
    </div>

    <div class="row mt-5 justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <?php echo validation_errors(); ?>
                    <form id="mainForm" method="post">
                        <div class="mb-3">
                            <label for="inputEmail" class="form-label">อีเมล์</label>
                            <input type="email" class="form-control" id="inputEmail" name="email"
                                value="<?php echo $email; ?>" />
                        </div>
                        <div class="mb-3">
                            <label for="inputPassword" class="form-label">รหัสผ่าน</label>
                            <input type="password" class="form-control" id="inputPassword" name="password"
                                value="<?php echo $password; ?>" />
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            ลงชื่อเข้าสู่ระบบ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end: main body -->
<?php require_once 'footer.php'; ?>