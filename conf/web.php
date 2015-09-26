<?php
/**
 * project web config
 +-----------------------------------------
 * @author      page7 <zhounan0120@gmail.com>
 * @version     $Id$
 */

return array(
    'build_dir_secure'      => true,       // Auto create index.html in dir
    'time_zone'             => 'PRC',      // Time zone
    'autoload_path'         => '',         // Autoload class path, use "," to set multiple dir
    'ajax_var'              => 'ajax',
    'reflesh_var'           => 'r',
    'i18n'                  => false,

    'refresh_timeout'       => 60,
    'proxy'                 => true,
    'proxy_type'            => CURLPROXY_SOCKS5,
    'proxy_server'          => '127.0.0.1:1080',
);

