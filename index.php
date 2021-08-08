<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once './constant.php';
require_once './helpers/utils.php';
include_once './autoload.php';

require_once './helpers/core.php';
require_once './helpers/query_builder.php';

$core->parse_url();