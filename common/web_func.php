<?php

/**
 * format a array for a insert query string
 +-----------------------------------------
 * @param array $data
 * @param bool $multiple
 * @return string
 */
function insert_array($data, $multiple=false, $mukey='')
{
    if ($multiple || isset($data[0]))
    {
        $value = array();
        $sql = array();
        foreach ($data as $i => $v)
        {
            $rs = insert_array($v, false, $i+1);
            $value = array_merge($value, $rs['value']);
            $sql[] = $rs['sql'];
        }

        $key = array_keys($data[0]);
        $key = '(`'.implode('`,`', $key).'`)';

        return array('column'=>$key, 'sql'=>implode(',', $sql), 'value'=>$value);
    }
    else
    {
        $value = array();
        foreach ($data as $k => $v)
        {
            if($v === null)
            {
                $data[$k] = 'NULL';
            }
            else
            {
                $value[":{$k}{$mukey}"] = $v;
                $data[$k] = ":{$k}{$mukey}";
            }
        }

        $key = '';
        if ($mukey === '')
        {
            $key = array_keys($data);
            $key = '(`'.implode('`,`', $key).'`)';
        }

        return array('column'=>$key, 'sql'=>'('.implode(',', $data).')', 'value'=>$value);
    }
}


/**
 * format a array for a update query string
 +-----------------------------------------
 * @param array $data
 * @return string
 */
function update_array($data)
{
    $value = array();
    foreach ($data as $k => $v)
    {
        if ( $v === null )
        {
            $data[$k] = "`{$k}` = NULL";
        }
        else
        {
            $value[":{$k}"] = $v;
            $data[$k] = "`{$k}` = :{$k}";
        }
    }

    return array('sql'=>implode(',', $data), 'value'=>$value);
}


/**
 * use for mysql "ON DUPLICATE KEY UPDATE"
 +-----------------------------------------
 * @param array $keys
 * @return void
 */
function update_column($keys)
{
    $columns = array();
    foreach($keys as $c)
    {
        $columns[] = "`{$c}`=VALUES(`{$c}`)";
    }
    return implode(',', $columns);
}




/**
 * file_get_centents by curl
 +-----------------------------------------
 * @param string $url
 * @param mixed  $post
 * @param int    $timeout
 * @return void
 */
function curl_file_get_contents($url, $post=null, $header=array(), $timeout=5, $proxy=array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if ($header)
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    if ($proxy)
    {
        curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy['type']);
        curl_setopt($ch, CURLOPT_PROXY, $proxy['server']);
    }

    if ($post)
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($post) ? http_build_query($post) : $post);
    }
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);
    return $content;
}
