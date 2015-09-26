<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hi Amazon.co.jp</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <?php \pt\tool\action::exec('header'); ?>

    <div class="container">
        <h1>Hi Amazon~!</h1>

        <div class="row" style="margin-top:20px;">
            <div class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#add">Add New</button>
            </div>

            <div class="col-xs-8 col-sm-6 col-md-6 col-lg-4 text-right">
                <div class="input-group hidden-xs hidden-sm">
                    <input type="text" name="keyword" class="form-control" value="<?php echo $keyword; ?>" />
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                    </span>
                </div>
            </div>
        </div>

        <style>
        .thumb {width:90px; height:90px; background-size:contain; background-repeat:no-repeat; background-position:center center; }
        .table>tbody>tr>td { vertical-align:middle; }
        </style>

        <div class="table-responsive">
            <table id="list" class="table table-striped" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th width="120">Product</th>
                        <th></th>
                        <?php \pt\tool\action::exec('columns'); ?>
                        <th width="10%">Refresh</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($list as $v) {
                    include dirname(__FILE__).'/_tr.tpl.php';
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="row" style="margin-top:20px; padding-bottom:30px;">
            <div class="col-xs-4 col-sm-6 col-md-6 col-lg-8 form-inline">
                <button type="button" class="btn btn-default" data-toggle="modal" data-target="#add">Add New</button>
            </div>

        </div>

    </div>


    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addLabel">Add new product url</h4>
                </div>
                <form class="modal-body">
                    <div class="form-group">
                        <label for="url">URL</label>
                        <input type="text" name="url" class="form-control" id="url" placeholder="http://www.amazon.co.jp/..." value="" />
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-loading-text="Saving..">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script>
    $(function(){
        !function(btn, url, list){
            var btn = $(btn),
                url = $(url),
                list = $(list),
                th = list.find("thead th"),
                tbody = list.children("tbody"),
                col = 0;
            th.each(function(){
                if (c = $(this).attr("colspan"))
                    c = parseInt(c, 10);
                else
                    c = 1;
                col += c;
            });

            btn.click(function(){
                btn.button('loading');
                tbody.prepend("<tr id='temp'><td colspan='"+col+"' class='text-center'>正在创建..</td></tr>");
                $.ajax({
                    type : "POST",
                    url  : "./refresh.php",
                    data : {url:url.val()},
                    success : function(data){
                        btn.button('reset');
                        if (data.s == 0){
                            url.val('');
                            $("#temp").after(data.rs).remove();
                        }else{

                        }
                    },
                    dataType : "json",
                    timeout : <?php echo config('web.refresh_timeout') * 1000; ?>
                });
            });
        }("#add .modal-footer .btn-primary", "#url", "#list");

        !function(list, bindobj){
            var list = $(list);
            list.on("click", bindobj, function(){
                var btn = $(this),
                    tr = btn.parents("tr").eq(0),
                    id = tr.data("id");
                btn.prop("disabled", true);
                $.ajax({
                    type : "POST",
                    url  : "./refresh.php",
                    data : {id:id},
                    success : function(data){
                        btn.prop("disabled", false);
                        if (data.s == 0){
                            var _tr = $(data.rs);
                            tr.html(_tr.html());
                        }else{

                        }
                    },
                    dataType : "json",
                    timeout : <?php echo config('web.refresh_timeout') * 1000; ?>
                });
            });
        }("#list", ".btn-refresh");

    });
    </script>

    <?php \pt\tool\action::exec('footer'); ?>

</body>
</html>