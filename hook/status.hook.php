<?php
use pt\framework\db as db;
use pt\tool\filter as filter;
use pt\tool\action as action;

class status
{
    public function __construct()
    {
        action::add('columns', array($this, 'columns'), 2);
        action::add('list',    array($this, 'load'), 1, 2);

        filter::add('refresh', array($this, 'refresh'), 2, 2);
    }



    // Columns
    public function columns($cols)
    {
        echo '<th>Status</th>';
    }


    // List Data
    public function load($v)
    {
        echo '<td>';

        switch ($v['status'])
        {
            case 0:
                if (is_null($v['status']))
                    echo '<span class="label label-default">not recorded</span>';
                else
                    echo '<span class="label label-info" title="Date:'.date('Y-m-d', $v['release']).'">presell</span>';
                break;
            case 1:
                echo '<span class="label label-success">on sale</span>';
                break;
            case -1:
                echo '<span class="label label-warning">stop selling</span>';
                break;
            case -2:
                echo '<span class="label label-danger">unknow</span>';
                break;
        }

        echo '</td>';
    }



    // Refresh
    public function refresh($product, $html)
    {
        // preg product's price
        preg_match('/<div id="availability".*>([\s\S]+?)<\/span>/i', $html, $status);

        if ($status)
        {
            $txt = strip_tags($status[1]);

            if (false !== strpos($txt, '発売予定日'))
            {
                $product['status'] = 0;
                preg_match('/([0-9]+)年([0-9]+)月([0-9]+)日/i', $txt, $date);
                $product['release'] = (int)strtotime("{$date[1]}-{$date[2]}-{$date[3]} 00:00:00");
            }
            else if (false !== strpos($txt, '在庫'))
            {
                $product['status'] = 1;
                $product['release'] = 0;
            }
            else
            {
                $product['status'] = -1;
                $product['release'] = 0;
            }
        }
        else
        {
            $product['status'] = -2;
            $product['release'] = 0;
        }

        return $product;
    }



}

new status();