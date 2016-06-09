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
use pt\tool\filter as filter;

// include your project common functions.
// this is a demo that have some useful functions.
include(COMMON_PATH.'web_func.php');

include(COMMON_PATH.'hook.inc.php');


// User id
if (empty($_SESSION['uid']))
    json_return(null, 1, 'Please Login.');

$uid = (int)$_SESSION['uid'];

// set timeout seconds
set_time_limit(config('web.refresh_timeout'));


$db = db::init();

if (!empty($_POST['id']))
{
    // Get product data from db
    $product = $db -> prepare("SELECT * FROM `a_good` WHERE `id`=:id AND `user`=:uid") -> execute(array(':id'=>$_POST['id'], ':uid'=>$uid));
    if (!$product)
        json_return(null, 1, 'Not Found any product.');

    $product = $product[0];

}
else if (!empty($_POST['url']))
{
    // Request a url for create a new product.
    $url = trim($_POST['url']);
    $pos = strpos($url, '/dp/B');
    $len = 4;

    if ($pos === false)
    {
        $pos = strpos($url, '/gp/product/B');
        $len = 12;
        if ($pos === false)
        {
            json_return(null, 1, 'URL is wrong.');
        }
    }

    $pcode = substr($url, $pos+$len, 10);

    $product = array(
        'code'   => $pcode,
        'url'    => '',
        'cover'  => 0,
        'title'  => '',
        'star'   => '',
        'user'   => $uid,
        'disable'=> 0,
    );

    list($column, $sql, $value) = array_values(insert_array($product));
    $rs = $db -> prepare("INSERT INTO `a_good` {$column} VALUES {$sql};") -> execute($value);

    if (!$rs)
        json_return(null, 1, 'Save failed.');

    $product['id'] = (int)$rs;
}

$header = array(
    'Cache-Control: no-cache',
    'Connection: keep-alive',
    'Pragma: no-cache',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36',
);

$cookies = array();
$cookie_file = PT_PATH.'log/amz_cookie.log';
if (file_exists($cookie_file))
    $cookies = include $cookie_file;

$_cookie = '';
foreach ($cookies as $k => $v)
    $_cookie .= "{$k}={$v}; ";

$header[] = 'Cookie: '.trim($_cookie);


$proxy = array();
if (config('web.proxy'))
{
    $proxy = array(
        'type'    => config('web.proxy_type'),
        'server'  => config('web.proxy_server'),
    );
}

// load page
if (!$product['url'])
{
    $html = curl_file_get_contents('http://www.amazon.co.jp/dp/'.$product['code'], null, $header, config('web.refresh_timeout'), $proxy, true);

    sleep(1);

    if (preg_match('/Location: (.*)/i', $html, $url))
    {
        preg_match_all('/Set-cookie: (.*)/i', $html, $setcookies);
        if (!empty($setcookies[1]))
        {
            foreach ($setcookies[1] as $value)
            {
                $value = explode(';', $value);
                foreach ($value as $v)
                {
                    list($key, $val) = explode('=', trim($v), 2);
                    $cookies[$key] = $val;
                    break;
                }
            }

            file_put_contents($cookie_file, '<?php return ' . var_export($cookies, true) . ';');

            $_cookie = '';
            foreach ($cookies as $k => $v)
                $_cookie .= "{$k}={$v}; ";

            array_pop($header);
            $header[] = 'Cookie: '.trim($_cookie);
        }

        $product['url'] = trim($url[1]);
        $html = curl_file_get_contents($product['url'], null, $header, config('web.refresh_timeout'), $proxy, true);
    }
}
else
{
    $html = curl_file_get_contents($product['url'], null, $header, config('web.refresh_timeout'), $proxy);
}

if (!$html)
    json_return(null, 1, 'Load page\'s data fail, please retry.');

if (!$product['cover'] || !$product['title'])
{
    // preg product's title
    preg_match('/<span id="productTitle".*?>(.*?)<\/span>/is', $html, $title);

    if (!empty($title[1]))
        $product['title'] = html_entity_decode(trim($title[1]));

    // preg product's cover
    preg_match('/data\:image\/jpeg;base64,(.+)/i', $html, $cover);

    if (!empty($cover[0]))
    {
        $product['cover'] = 1;//trim($cover[0]);

        // save picture
        @file_put_contents(PT_PATH.'picture/'.$product['id'].'.jpg', base64_decode($cover[1]));
    }
}

// preg product's star
if(preg_match('/5つ星のうち ([0-9.]+)/i', $html, $star))
{
    $product['star'] = $star[1];
}


$db -> beginTrans();

$product = filter::apply('refresh', $product, $html);

// Save
$id = $product['id'];
unset($product['id']);

list($sql, $value) = array_values(update_array($product));
$value[':id'] = $id;
$rs = $db -> prepare("UPDATE `a_good` SET {$sql} WHERE `id`=:id") -> execute($value);
if ($rs === false)
{
    $db -> rollback();
    json_return(null, 1, 'Load page\'s data fail, please retry.');
}

// Commit
if (!$db -> commit())
{
    $db -> rollback();
    json_return(null, 9, 'Load page\'s data fail, please retry.');
}

$product['id'] = $id;
template::assign('v', $product);
$html = template::fetch('_tr');

json_return($html);