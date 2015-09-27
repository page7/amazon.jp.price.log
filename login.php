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

if ($_POST)
{
    if (empty($_POST['username']) || empty($_POST['password']))
        json_return(null, 1, 'Username / Password can\'t be empty.');

    $db = db::init();
    $user = $db -> prepare("SELECT `id`,`username`,`password`,`md` FROM `a_user` WHERE BINARY `username`=:user") -> execute(array(':user'=>$_POST['username']));

    if (!$user || $user[0]['password'] != md5(md5($_POST['password']).$user[0]['md']))
    {
        json_return(null, 1, 'Incorrect password.');
    }
    else
    {
        $_SESSION['uid'] = $user[0]['id'];
        json_return(1);
    }
}

template::display('login');
