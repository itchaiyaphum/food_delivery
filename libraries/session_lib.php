<?php

class Session_lib extends Base_app
{
    public function get($key = null)
    {
        if (isset($key)) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        }

        return [];
    }

    public function set($data, $value = '')
    {
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $_SESSION[$key] = $val;
            }

            return;
        }

        $_SESSION[$data] = $value;
    }

    public function destroy($key = null)
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                unset($_SESSION[$k]);
            }

            return;
        }
        if (empty($key)) {
            foreach ($_SESSION as $k => $v) {
                unset($_SESSION[$k]);
            }

            return;
        }
        unset($_SESSION[$key]);
    }
}
