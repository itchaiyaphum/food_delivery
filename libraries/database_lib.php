<?php

class Database_lib extends Base_app
{
    public $db = null;
    public $result = null;

    public function __construct($app = null)
    {
        parent::__construct($app);
        $this->connect_db();
    }

    public function connect_db()
    {
        // Create connection
        $conn = new mysqli($this->app->config_lib->db_hostname,
            $this->app->config_lib->db_username,
            $this->app->config_lib->db_password,
            $this->app->config_lib->db_database_name);
        $conn->set_charset($this->app->config_lib->db_charset);

        // Check connection
        if ($conn->connect_error) {
            exit('ไม่สามารถเชื่อต่อฐานข้อมูลได้: '.$conn->connect_error);
        }

        $this->db = $conn;
    }

    public function query($sql = '')
    {
        // get result from database
        $result = $this->db->query($sql);
        $this->result = $result;

        return $this;
    }

    public function result()
    {
        if (empty($this->result) || !isset($this->result)) {
            return [];
        }

        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    public function row()
    {
        if (empty($this->result) || !isset($this->result)) {
            return [];
        }

        return $this->result->fetch_array(MYSQLI_ASSOC);
    }

    // insert data
    public function insert($table_name = null, $data = null)
    {
        if (empty($table_name) || empty($data)) {
            exit('table / data ตั้งค่าไม่ครบถ้วน!');
        }

        // preparing data
        $data_keys = [];
        $data_values = [];
        foreach ($data as $key => $value) {
            array_push($data_keys, "`{$key}`");
            array_push($data_values, $this->_escape($value));
        }
        $data_keys_sql = implode(',', $data_keys);
        $data_values_sql = implode(',', $data_values);

        // insert data to database
        $sql = "INSERT INTO `{$table_name}` ({$data_keys_sql}) VALUES ({$data_values_sql})";

        // print_r($sql);
        // exit;

        return $this->db->query($sql);
    }

    public function get_insert_id()
    {
        return $this->db->insert_id;
    }

    // update data
    public function update($table_name = null, $data = null, $where = null)
    {
        if (empty($table_name) || empty($data) || empty($where)) {
            exit('table / data / where ตั้งค่าไม่ครบถ้วน!');
        }

        // preparing data
        $data_array = [];
        foreach ($data as $key => $value) {
            array_push($data_array, "`{$key}`={$this->_escape($value)}");
        }
        $data_to_sql = implode(',', $data_array);

        // update data to database
        $sql = "UPDATE `{$table_name}` SET {$data_to_sql} WHERE {$where}";
        if ($this->db->query($sql) === true) {
            return true;
        }

        // print_r($sql);
        // exit;

        return false;
    }

    // delete data
    public function delete($table_name = null, $where = null)
    {
        if (empty($table_name) || empty($where)) {
            exit('table / where ตั้งค่าไม่ครบถ้วน!');
        }
        // delete data to database
        $sql = "DELETE FROM {$table_name} WHERE {$where}";
        if ($this->db->query($sql) === true) {
            return true;
        }

        // print_r($sql);
        // exit;

        return false;
    }

    private function _escape($str)
    {
        if (is_string($str)) {
            return "'".mysqli_real_escape_string($this->db, $str)."'";
        } elseif (is_bool($str)) {
            return ($str === false) ? 0 : 1;
        } elseif ($str === null) {
            return 'NULL';
        }

        return $str;
    }
}
