<?php

// init environment
session_start();
date_default_timezone_set('Asia/Bangkok');
define('DS', DIRECTORY_SEPARATOR);
define('PATH', realpath('./').DS);

if (preg_match('/dev./i', $_SERVER['SERVER_NAME'])) {
    error_reporting(-1);
    ini_set('display_errors', 1);
}

// load core library
require_once 'libraries/base_app.php';
require_once 'libraries/config_lib.php';
require_once 'libraries/database_lib.php';
require_once 'libraries/form_validation_lib.php';
require_once 'libraries/input_lib.php';
require_once 'libraries/session_lib.php';
require_once 'libraries/upload_lib.php';

$app = new Base_app();

$config_lib = new Config_lib($app);
$app->config_lib = $config_lib;

$database_lib = new Database_lib($app);
$app->database_lib = $database_lib;

$form_validation_lib = new Form_validation_lib($app);
$app->form_validation_lib = $form_validation_lib;

$input_lib = new Input_lib($app);
$app->input_lib = $input_lib;

$session_lib = new Session_lib($app);
$app->session_lib = $session_lib;

$upload_lib = new Upload_lib($app);
$app->upload_lib = $upload_lib;

// load core helpers
require_once 'helpers/common.php';

// load user library
require_once 'libraries/profile_lib.php';

$profile_lib = new Profile_lib($app);
$app->profile_lib = $profile_lib;
