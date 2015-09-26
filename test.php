<?php
$json = '
[
    {
        "price": 7920,
        "time": "2015-05-31"
    },
    {
        "price": 8480,
        "time": "2015-06-20"
    },
    {
        "price": 8000,
        "time": "2015-06-23"
    },
    {
        "price": 8480,
        "time": "2015-07-04"
    },
    {
        "price": 8600,
        "time": "2015-07-06"
    },
    {
        "price": 8500,
        "time": "2015-07-10"
    },
    {
        "price": 8630,
        "time": "2015-07-11"
    },
    {
        "price": 8500,
        "time": "2015-07-13"
    },
    {
        "price": 7580,
        "time": "2015-07-14"
    },
    {
        "price": 8486,
        "time": "2015-07-21"
    },
    {
        "price": 8483,
        "time": "2015-07-23"
    },
    {
        "price": 8449,
        "time": "2015-07-25"
    },
    {
        "price": 8448,
        "time": "2015-07-26"
    },
    {
        "price": 8380,
        "time": "2015-08-28"
    },
    {
        "price": 8199,
        "time": "2015-09-03"
    },
    {
        "price": 7900,
        "time": "2015-09-04"
    },
    {
        "price": 7700,
        "time": "2015-09-08"
    },
    {
        "price": 5060,
        "time": "2015-09-09"
    },
    {
        "price": 6980,
        "time": "2015-09-10"
    },
    {
        "price": 6960,
        "time": "2015-09-11"
    },
    {
        "price": 7400,
        "time": "2015-09-13"
    },
    {
        "price": 7699,
        "time": "2015-09-14"
    },
    {
        "price": 7200,
        "time": "2015-09-15"
    },
    {
        "price": 7560,
        "time": "2015-09-18"
    },
    {
        "price": 8160,
        "time": "2015-09-23"
    }
]';

$pid = 8;
$data = json_decode($json, true);

foreach ( $data as $k => $v )
{
    $s = array('id'=>$k+1, 'product'=>$pid, 'price'=>$v['price']*100, 'time'=>strtotime($v['time'].' 00:00:00'));
    echo "INSERT INTO `a_price` (`id`,`product`,`price`,`time`) VALUES ({$s['id']}, {$s['product']}, {$s['price']}, {$s['time']});\n";
}


