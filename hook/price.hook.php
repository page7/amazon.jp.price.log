<?php
use pt\framework\db as db;
use pt\tool\filter as filter;
use pt\tool\action as action;

class price
{
    public function __construct()
    {
        action::add('columns', array($this, 'columns'), 1);
        action::add('list',    array($this, 'load'), 1, 1);
        action::add('footer',  array($this, 'script'), 1, 1);

        filter::add('refresh', array($this, 'refresh'), 1, 2);
        action::add('delete',  array($this, 'delete'), 1, 1);
    }



    // Columns
    public function columns($cols)
    {
        echo '<th width="150">Price</th><th width="100">OFF</th>';
    }


    // List Data
    public function load($v)
    {
        echo '<td><abbr class="price" data-id="'.(int)$v['id'].'">'. number_format($v['price']/100, 2, '.', '') .'</abbr> ';

        $db = db::init();
        $pt = $db -> prepare("SELECT MAX(`price`) AS `max`, MIN(`price`) AS `min` FROM `a_price` WHERE `product`=:id") -> execute(array(':id'=>$v['id']));

        if ($v['prevprice'])
        {
            if ($v['price'] == $pt[0]['min'])
            {
                echo '<a class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-heart"></span></a>';
            }
            else if ($v['prevtime'] > NOW - 86400 * 2)
            {
                if ($v['price'] > $v['prevprice'])
                    echo '<a class="btn btn-warning btn-xs"><span class="glyphicon glyphicon-arrow-up"></span></a>';
                else
                    echo '<a class="btn btn-success btn-xs"><span class="glyphicon glyphicon-arrow-down"></span></a>';
            }
        }

        echo '</td><td>';

        if ($v['oriprice'])
        {
            echo round(($v['oriprice'] - $v['price']) / $v['oriprice'] * 100).'%';
        }
        else if (!empty($pt) && $pt[0]['max'])
        {
            echo round(($pt[0]['max'] - $v['price']) / $pt[0]['max'] * 100).'% <span class="glyphicon glyphicon-question-sign" style="font-size:12px; color:#999;" title="OFF comes from the highest price in history"></span>';
        }

        echo '</td>';
    }


    // js
    public function script()
    {
?>

<!-- Include amcharts -->
<script src="//cdn.bootcss.com/amcharts/3.13.0/amcharts.js"></script>
<script src="//cdn.bootcss.com/amcharts/3.13.0/serial.js"></script>

<style>
.popover { max-width:400px; }
.popover .amcharts-chart-div { overflow:visible!important; }
.popover .amcharts-chart-div a { top:0px!important; }
</style>

<script>
$(function(){
    var price_data = {},
        popover = function(obj){
            obj.popover({
                content     : function(){
                    var id = $(obj).data("id"),
                        catch_data = $(obj).data("popover"),
                        chart_build = function(data){
                            var chart = AmCharts.makeChart("chart-"+id, {
                                "theme": "none",
                                "type": "serial",
                                "autoMarginOffset": 20,
                                "dataProvider": data,
                                "pathToImages": "http://cdn.bootcss.com/amcharts/3.13.0/images/",
                                "valueAxes": [{
                                    "id": "v1",
                                    "axisAlpha": 0.1
                                }],
                                "graphs": [{
                                    "type": "step",
                                    "above": true,
                                    "balloonText": "[[value]]",
                                    "bullet": "round",
                                    "bulletBorderAlpha": 1,
                                    "bulletBorderColor": "#FFFFFF",
                                    "hideBulletsCount": 20,
                                    "lineThickness": 1.5,
                                    "lineColor": "#FF8000",
                                    "valueField": "price"
                                }],
                                "valueAxes": [{
                                    "inside": true,
                                    "fontSize": 0,
                                    "gridAlpha": 0,
                                    "axisAlpha": 0
                                }],
                                "chartCursor": {
                                    "pan": false,
                                    "valueLineEnabled": false,
                                    "valueLineBalloonEnabled": false,
                                    "categoryBalloonDateFormat": "YYYY-MM-DD",
                                    "zoomable": false
                                },
                                "chartScrollbar": {
                                    "dragIcon": "dragIcon",
                                    "oppositeAxis": false,
                                    "scrollbarHeight": 3,
                                    "backgroundAlpha": 0.1,
                                    "backgroundColor": "#868686",
                                    "selectedBackgroundColor": "#337ab7",
                                    "selectedBackgroundAlpha": 0.5
                                },
                                "categoryField": "time",
                                "categoryAxis": {
                                    "parseDates": true,
                                    "minPeriod": "mm",
                                    "axisAlpha": 0,
                                    "minHorizontalGap": 60,
                                    "minorGridEnabled": true,
                                    "dateFormats": [{period:'fff',format:'JJ:NN:SS'},{period:'ss',format:'JJ:NN:SS'},{period:'mm',format:'JJ:NN'},{period:'hh',format:'JJ:NN'},{period:'DD',format:'MM-DD'},{period:'WW',format:'MM-DD'},{period:'MM',format:'MM'},{period:'YYYY',format:'YYYY'}]
                                }
                            });

                            return chart;
                        };
                    if (!catch_data) {
                        $.post("./index.php?hook=price&method=chart", {id:id}, function(data){
                            var div = $("#chart-"+id);
                            if (data.s == 0){
                                div.html("");
                                obj.data("popover", data.rs);
                                chart_build(data.rs);
                            }else{
                                div.children("p").html("").append("<b>Error:</b>").append(data.err);
                            }
                        }, "json");
                    } else {
                        setTimeout(function(){ chart_build(catch_data); }, 100);
                    }
                    return '<div id="chart-'+id+'" style="width:360px; height:200px;"><p>Loading..</p></div>';
                },
                placement   : "left",
                trigger     : "manual",
                html        : true
            });

            return obj;
        };

        $(".table").on("mouseover", ".price", function(){
            var abbr = $(this),
                td = abbr.parent();
            if (!abbr.data("popover")) {
                popover(abbr).popover("show");
                td.hover(function(){ abbr.popover("show"); }, function(){ abbr.popover("hide"); });
            }
        });

});
</script>
<?php
    }



    // Chart
    static function chart()
    {
        $id = (int)$_POST['id'];

        $db = db::init();
        $prices = $db -> prepare("SELECT * FROM `a_price` WHERE `product`=:id ORDER BY `time` ASC") -> execute(array(':id'=>$id));

        $data = array();
        foreach($prices as $v)
        {
            $data[] = array('price'=>$v['price']/100, 'time'=>date('Y-m-d H:i:s', $v['time']));
        }

        json_return($data);
    }



    // Refresh
    public function refresh($product, $html)
    {
        // preg product's price
        preg_match('/<span id="priceblock_ourprice".*>￥ ([0-9.,]+?)<\/span>/i', $html, $price);

        if ($price)
        {
            $product['price'] = (int)str_replace(',', '', $price[1]) * 100;

            $db = db::init();
            $last = $db -> prepare("SELECT * FROM `a_price` WHERE `product`=:id ORDER BY `id` DESC LIMIT 0,1;") -> execute(array(':id'=>$product['id']));
            if (!$last || ($last[0]['price'] != $product['price'] && $last[0]['time'] < NOW - 60))
            {
                $db -> prepare("INSERT INTO `a_price` (`price`,`product`,`time`) VALUES (:price, :id, :time);") -> execute(array(':id'=>$product['id'], ':price'=>$product['price'], ':time'=>NOW));

                if ($last)
                {
                    $db -> prepare("UPDATE `a_good` SET `prevprice`=:price, `prevtime`=:time WHERE `id`=:id") -> execute(array(':id'=>$product['id'], ':price'=>$last[0]['price'], ':time'=>$last[0]['time']));
                    $product['prevprice'] = $last[0]['price'];
                    $product['prevtime']  = $last[0]['time'];
                }
            }
        }
        else
        {
            $product['price'] = 0;
        }

        // product's oriprice
        preg_match('/参考価格:<\/td>\s+<td.+>￥ ([0-9,.]+?)<\/td>/i', $html, $oriprice);
        if ($oriprice)
        {
            $product['oriprice'] = (int)str_replace(',', '', $oriprice[1]) * 100;
        }
        else
        {
            $product['oriprice'] = 0;
        }

        return $product;
    }



    // Delete
    static function delete($product)
    {
        $db = db::init();

        $rs = $db -> prepare("DELETE FROM `a_price` WHERE `product`=:id") -> execute(array(':id'=>$product));
        if (false === $rs)
        {
            $db -> rollback();
            json_return(null, 2, 'Operation failed.');
        }
    }


}

new price();