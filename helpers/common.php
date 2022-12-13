<?php

function base_url($url = '')
{
    global $app;
    $base_url = $app->config_lib->base_url;
    if ($url == '/') {
        $url = '';
    }

    return $base_url.$url;
}

function array_value($array_data = null, $key = '', $default = '')
{
    return (!empty($array_data) && isset($array_data[$key])) ? $array_data[$key] : $default;
}

function active_menu($data = null, $index = null)
{
    return ($data == $index) ? 'active' : '';
}

function redirect($url = '/')
{
    header('Location: '.base_url($url));
}

function get_datetime($date = '0000-00-00 00:00:00', $format = 'Y-m-d H:i:s')
{
    return date_format(date_create($date), $format);
}

function now()
{
    return date('Y-m-d H:i:s');
}

function validation_errors()
{
    global $app;

    if ($app->form_validation_lib->error_status === true) {
        echo '<div class="alert alert-danger">'.$app->form_validation_lib->error_messages.'</div>';
    }
}

function action_messages()
{
    global $app;

    if ($app->form_validation_lib->action_status === 'success') {
        echo '<div class="alert alert-success">'.$app->form_validation_lib->action_messages.'</div>';
    }
}

function html_escape($var, $double_encode = true)
{
    if (empty($var)) {
        return $var;
    }

    if (is_array($var)) {
        foreach (array_keys($var) as $key) {
            $var[$key] = html_escape($var[$key], $double_encode);
        }

        return $var;
    }

    return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
}

function set_value($field, $default = '', $html_escape = true)
{
    global $app;

    $value = $app->input_lib->post($field, false);

    isset($value) or $value = $default;

    return ($html_escape) ? html_escape($value) : $value;
}

function admin_filter_search_html($name = 'filter_search')
{
    return '
    <div class="input-group">
        <input type="text" name="'.$name.'" id="search" value="'.set_value($name).'" class="form-control" onchange="this.form.submit();" />
        <button class="input-group-text" onclick="this.form.submit();">
            <i class="bi-search"></i>
        </button>
        <button class="input-group-text" onclick="document.getElementById(\'search\').value=\'\'; this.form.submit();">
            <i class="bi-backspace"></i>
        </button>
    </div>
    ';
}
