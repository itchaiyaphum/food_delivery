<?php

class Upload_lib extends Base_app
{
    public $upload_path = 'storage';

    public function get_ext($file_name = '')
    {
        $file_name_parts = explode('.', $file_name);

        return end($file_name_parts);
    }

    public function do_upload($field = 'userfile')
    {
        // upload status
        $status = true;
        $error = '';

        // Is $_FILES[$field] set? If not, no reason to continue.
        if (isset($_FILES[$field])) {
            $_file = $_FILES[$field];
        } else {
            return false;
        }

        if (!isset($_file)) {
            $error = 'ไม่มีเลือกไฟล์สำหรับการอัพโหลด';
            $status = false;
        }

        $file_temp = $_file['tmp_name'];
        $file_size = $_file['size'];
        $file_name = $_file['name'];
        $file_ext = $this->get_ext($file_name);
        $new_file_name = md5($file_name).'.'.$file_ext;
        $upload_file_path = PATH.$this->upload_path.DS.$new_file_name;

        if (!@copy($file_temp, $upload_file_path)) {
            if (!@move_uploaded_file($file_temp, $upload_file_path)) {
                $error = 'อัพโหลดไฟล์รูปภาพไม่สำเร็จ';
                $status = false;
            }
        }

        $data = [
            'file_name' => $file_name,
            'file_size' => $file_size,
            'new_file_name' => $new_file_name,
            'full_path' => $upload_file_path,
            'status' => $status,
            'error' => $error,
        ];

        return $data;
    }
}
