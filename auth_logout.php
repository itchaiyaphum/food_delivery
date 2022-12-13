<?php
// ================================ BUSINESS LOGIC ===================
require_once 'libraries/app.php';
?>

<?php
// ================================ CONTROLLER =======================
$app->session_lib->destroy();
redirect('/');

?>