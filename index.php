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
use pt\tool\action as action;

// include your project common functions.
// this is a demo that have some useful functions.
include(COMMON_PATH.'web_func.php');

include(COMMON_PATH.'hook.inc.php');

// User id
if (empty($_SESSION['uid']))
    redirect('./login.php');

$uid = (int)$_SESSION['uid'];

// Hook method
if (!empty($_GET['hook']))
{
    $hook = $_GET['hook'];
    $method = $_GET['method'];
    call_user_func(array($hook, $method));
}


$method = empty($_GET['method']) ? 'default' : $_GET['method'];

switch ($method)
{
    // Status
    case 'status':
        if ($_POST)
        {
            $id = (int)$_POST['id'];
            $status = (int)$_POST['status'];

            $db = db::init();
            $db -> beginTrans();

            $rs = $db -> prepare("UPDATE `a_good` SET `disable`=:disable WHERE `id`=:id") -> execute(array(':id'=>$id, ':disable'=>$status));
            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 1, 'Operation failed.');
            }

            action::exec('status', $id, $status);

            if ($db -> commit())
                json_return(1);

            json_return(null, 9, 'Operation failed.');
        }
        break;



    // Delete
    case 'delete':
        if ($_POST)
        {
            $id = (int)$_POST['id'];

            $db = db::init();
            $db -> beginTrans();

            $rs = $db -> prepare("DELETE FROM `a_good` WHERE `id`=:id") -> execute(array(':id'=>$id));
            if (false === $rs)
            {
                $db -> rollback();
                json_return(null, 1, 'Operation failed.');
            }

            action::exec('delete', $id);

            if ($db -> commit())
                json_return(1);

            json_return(null, 9, 'Operation failed.');
        }
        break;



    // List page
    case 'default':
    default:
        $keyword = '';
        template::assign('keyword', $keyword);

        $db = db::init();

        $sql = "SELECT * FROM `a_good` WHERE `user`=:uid ORDER BY `id` DESC";
        $list = $db -> prepare($sql) -> execute(array(':uid'=>$uid));

        template::assign('list', $list);
        template::display('index');
}
