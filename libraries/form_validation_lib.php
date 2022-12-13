<?php

class Form_validation_lib extends Base_app
{
    public $check_vars = [];
    public $error_status = false;
    public $error_messages = '';

    public $action_status = '';
    public $action_messages = '';

    public function set_rules($var_name = '', $var_label = '', $rule = 'required')
    {
        array_push($this->check_vars, ['var_name' => $var_name, 'var_label' => $var_label, 'rule' => $rule]);
    }

    public function run()
    {
        $this->_reset();
        if (empty($_POST)) {
            return false;
        } else {
            foreach ($this->check_vars as $item) {
                if ($item['rule'] == 'required') {
                    $this->_check_required($item);
                }
            }
            if ($this->error_status == true) {
                return false;
            }
        }

        return true;
    }

    public function set_error($error = '')
    {
        $this->error_status = true;
        $this->error_messages .= "<li>{$error}</li>";
    }

    public function set_message($status = null, $message = '')
    {
        $this->action_status = $status;
        $this->action_messages .= "<li>{$message}</li>";
    }

    public function _check_required($item = [])
    {
        $var_value = $this->app->input_lib->get_post($item['var_name']);
        if (empty($var_value)) {
            $this->error_status = true;
            $this->error_messages .= "<li>กรุณากรอกข้อมูล {$item['var_label']}.</li>";
        }
    }

    private function _reset()
    {
        $this->error_status = false;
        $this->error_messages = '';

        $this->action_status = '';
        $this->action_messages = '';
    }
}
