<?php

// session start
define("SESSION_ON", true);

// define project's config
define("CONFIG", '/conf/web.php');

// debug switch
define("DEBUG", true);

// include framework entrance file
include('./common.php');

// simplify use class
use pt\framework\debug\console as debug;
use pt\framework\template as template;
use pt\framework\db as db;

// include your project common functions.
// this is a demo that have some useful functions.
include(COMMON_PATH.'web_func.php');

include(COMMON_PATH.'hook.inc.php');

if (!empty($_GET['hook']))
{
    $hook = $_GET['hook'];
    $method = $_GET['method'];
    call_user_func(array($hook, $method));
}

$keyword = '';
template::assign('keyword', $keyword);

$db = db::init();

$sql = "SELECT * FROM `a_good` ORDER BY `id` DESC";
$list = $db -> prepare($sql) -> execute();

template::assign('list', $list);
template::display('index');