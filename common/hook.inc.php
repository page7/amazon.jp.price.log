<?php
// Load Hook
$hooks = array(
    'price',
    'status',
    'secondhand',
);

foreach ($hooks as $name)
{
    @include_once(PT_PATH.'hook/'.$name.'.hook.php');
}