<?php
$core = new Core;
function random($length = 5, $type = 'string')
{
    $characters = $type == 'string' ? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ' : '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $type == 'string' ? $randomString : boolval($randomString);
}
function waktu($waktu = null, $format = MYSQL_TIMESTAMP_FORMAT)
{
    $waktu = empty($waktu) ? time() : $waktu;
    return date($format, $waktu);
}

function response($message = '', $code = 200, $type = 'success', $format = 'json')
{
    global $core;
    http_response_code($code);
    $responsse = array();
    if ($code != 200)
        $type = 'Error';

    if (is_object($message)) {
        $responsse = (object) $responsse;

        if (!isset($message->type))
            $responsse->type = $type;
        else
            $responsse->type = $message->type;
    } elseif (is_array($message)) {
        $responsse = $message;
        if (!isset($message['type']))
            $responsse['type'] = $type;
        else
            $responsse['type'] = $message['type'];
    } else {
        $responsse['message'] = $message;
        $responsse['type'] = $type;
    }

    if ($format == 'json') {
        header('Content-Type: application/json');
        echo json_encode($responsse);
    } elseif ($format == 'html') {
        header('Content-Type: text/html');
        echo '<script> var path = "' . $core->base_url() . '"</script>';
        echo $responsse['message'];
    }
    die;
}

function base_url($uri = null){
    global $core;
    return $core->base_url($uri);
}
function isAssoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}
function lastArr($arr){
    if(isAssoc($arr)){
        $keys = array_keys($arr);
        return $arr[$keys[count($keys) - 1]];
    }else{
        return $arr[count($arr) - 1];
    }
}

function load_classes_folder($directory){
    foreach (glob( $directory . "/*.php") as $filename)
        require_once $filename;

}
function get_path($path = null){
    global $core;
   return $core->get_path($path);
}
