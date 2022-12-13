<?php

class Profile_lib extends Base_app
{
    public $id = 0;
    public $firstname = '';
    public $lastname = '';
    public $email = '';
    public $address = '';
    public $thumbnail = '';
    public $mobile_no = '';
    public $user_type = '';
    public $status = 0;
    public $password = '';

    public $restaurant_name = '';
    public $restaurant_type_id = 0;
    public $restaurant_address = 0;
    public $restaurant_thumbnail = 0;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->get_profile();
    }

    public function is_login()
    {
        $is_login = $this->app->session_lib->get('is_login', false);
        if ($is_login == true || $is_login == 1) {
            return true;
        }

        return false;
    }

    public function get_profile($email = null)
    {
        (!empty($email)) or $email = $this->app->session_lib->get('email');

        $query = $this->app->database_lib->query(" SELECT * FROM user WHERE email='{$email}' ");
        $result = $query->row();

        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $this->{$key} = $val;
            }
        }

        return $this;
    }

    public function get_profile_by_id($id = 0)
    {
        $query = $this->app->database_lib->query("SELECT * FROM user WHERE id={$id}");
        $result = $query->row();

        if (!empty($result)) {
            foreach ($result as $key => $val) {
                $this->{$key} = $val;
            }
        }

        return $this;
    }

    // ตรวจสอบว่ามีอีเมล์ในระบบ database อยู่หรือไม่
    public function check_email_exists($email = null)
    {
        $query = $this->app->database_lib->query("SELECT * FROM user WHERE email='{$email}'");

        return (!empty($query->result())) ? true : false;
    }
}
