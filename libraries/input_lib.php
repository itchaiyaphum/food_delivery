<?php

class Input_lib extends Base_app
{
    // get input type get
    public function get($index = null, $default = null)
    {
        // หากไม่ส่ง index มา แสดงว่าต้องการดึงค่าทั้งหมดใน $_GET
        if (empty($index)) {
            return $_GET;
        }

        // หาก index ไม่มี ให้ return ค่า default กลับไป
        if (!isset($_GET[$index])) {
            return $default;
        }

        // หากมีการส่ง index เข้ามา และมีค่าใน index
        return $_GET[$index];
    }

    // get input type post
    public function post($index = null, $default = null)
    {
        // หากไม่ส่ง index มา แสดงว่าต้องการดึงค่าทั้งหมดใน $_POST
        if (empty($index)) {
            return $_POST;
        }

        // หาก index ไม่มี ให้ return ค่า default กลับไป
        if (!isset($_POST[$index])) {
            return $default;
        }

        // หากมีการส่ง index เข้ามา และมีค่าใน index
        return $_POST[$index];
    }

    // get input type get,post
    public function get_post($index = null, $default = null)
    {
        $result_get = $this->get($index, $default);
        $result_post = $this->post($index, $default);

        if (!empty($result_get)) {
            return $result_get;
        } elseif (!empty($result_post)) {
            return $result_post;
        }

        return $default;
    }
}
