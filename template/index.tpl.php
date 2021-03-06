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
    <link rel="stylesheet" href="./template/css/main.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <?php \pt\tool\action::exec('header'); ?>

    <div class="container-fluid">
        <h1>Hi Amazon~!</h1>

        <div class="alert alert-danger" role="alert" style="display:none">数据保存错误</div>

        <div class="row operate operate-header" style="margin-top:20px;">
            <div class="col-xs-5 col-sm-6 col-md-6 col-lg-8 form-inline">
                <div class="btn-group">
                    <button type="button" class="btn btn-default"  data-toggle="modal" data-target="#add">Add New</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="refresh-all" href="javascript:;">Refresh All</a></li>
                        <li><a class="toggle-disabled" href="javascript:;">Toggle Disabled</a></li>
                    </ul>
                </div>
            </div>

            <div class="col-xs-7 col-sm-6 col-md-6 col-lg-4 text-right">
            </div>
        </div>

        <div class="table-responsive" style="margin-top:20px;">
            <table id="list" class="table table-striped">
                <thead>
                    <tr>
                        <th width="120">Product</th>
                        <th class="hidden-xs"></th>
                        <?php \pt\tool\action::exec('columns'); ?>
                        <th width="160"></th>
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

        <div class="row operate operate-footer" style="margin-top:20px; padding-bottom:30px;">
            <div class="col-xs-5 col-sm-6 col-md-6 col-lg-8 form-inline">
                <div class="btn-group">
                    <button type="button" class="btn btn-default"  data-toggle="modal" data-target="#add">Add New</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                        <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="refresh-all" href="javascript:;">Refresh All</a></li>
                        <li><a class="toggle-disabled" href="javascript:;">Toggle Disabled</a></li>
                    </ul>
                </div>
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

<!-- project script -->
<script>
$(function(){
    $(window).scroll(function(){
        var win = $(this),
            alert = $(".alert");
        if (win.scrollTop() > 50) {
            alert.addClass("alert-fixed");
        }else{
            alert.removeClass("alert-fixed");
        }
    });

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
            tbody.prepend("<tr id='temp'><td colspan='"+col+"' class='text-center'><span class='glyphicon glyphicon-refresh loading'></span>正在创建..</td></tr>");
            $.ajax({
                type : "POST",
                url  : "./refresh.php",
                data : {url:url.val()},
                success : function(data){
                    btn.button('reset');
                    if (data.s == 0){
                        url.val('');
                        var tr = $(data.rs);
                        $("#temp").after(tr).remove();
                        tr.trigger("create");
                    }else{
                        $(".alert").text(data.err).fadeIn(200).delay(5000).fadeOut(200);
                    }
                },
                dataType : "json",
                timeout : <?php echo config('web.refresh_timeout') * 1000; ?>
            });
        });
    }("#add .modal-footer .btn-primary", "#url", "#list");


    // Refresh
    !function(list, bindobj){
        var list = $(list);
        list.on("click", bindobj, function(){
            var btn = $(this),
                tr = btn.parents("tr").eq(0),
                id = tr.data("id");
            btn.prop("disabled", true).addClass("loading");
            $.ajax({
                type : "POST",
                url  : "./refresh.php",
                data : {id:id},
                complete : function(){
                    tr.removeClass("warning danger");
                    btn.prop("disabled", false).removeClass("loading");
                },
                success : function(data){
                    if (data.s == 0){
                        var _tr = $(data.rs);
                        tr.html(_tr.html()).trigger("refresh");
                    }else{
                        $(".alert").text(data.err).fadeIn(200).delay(5000).fadeOut(200);
                    }
                },
                dataType : "json",
                timeout : <?php echo config('web.refresh_timeout') * 1000; ?>
            });
        });
    }("#list", ".btn-refresh");


    // Delete
    !function(list, bindobj){
        var list = $(list);
        list.on("click", bindobj, function(){
            var btn = $(this),
                tr = btn.parents("tr").eq(0),
                id = tr.data("id");
            btn.prop("disabled", true).addClass("loading").html('<span class="glyphicon glyphicon-refresh"></span>');
            $.ajax({
                type : "POST",
                url  : "./index.php?method=delete",
                data : {id:id},
                success : function(data){
                    btn.prop("disabled", false).removeClass("loading").html('<span class="glyphicon glyphicon-trash"></span>');
                    if (data.s == 0){
                        tr.remove();
                    }else{
                        $(".alert").text(data.err).fadeIn(200).delay(5000).fadeOut(200);
                    }
                },
                dataType : "json"
            });
        });
    }("#list", ".btn-delete");


    // Status
    !function(list, bindobj){
        var list = $(list);
        list.on("click", bindobj, function(){
            var btn = $(this),
                tr = btn.parents("tr").eq(0),
                id = tr.data("id"),
                status = tr.is(".disable") ? 1 : 0;
            btn.prop("disabled", true).addClass("loading").html('<span class="glyphicon glyphicon-refresh"></span>');
            $.ajax({
                type : "POST",
                url  : "./index.php?method=status",
                data : {id:id, status:(status ? 0 : 1)},
                complete : function(){
                    btn.prop("disabled", false).removeClass("loading");
                },
                success : function(data){
                    if (data.s == 0){
                        if (status) {
                            tr.removeClass("disable");
                            btn.html('<span class="glyphicon glyphicon-eye-close"></span>');
                        }else{
                            tr.addClass("disable");
                            btn.html('<span class="glyphicon glyphicon-eye-open"></span>');
                        }
                    }else{
                        if (!status) {
                            btn.html('<span class="glyphicon glyphicon-eye-close"></span>');
                        }else{
                            btn.html('<span class="glyphicon glyphicon-eye-open"></span>');
                        }
                        $(".alert").text(data.err).fadeIn(200).delay(5000).fadeOut(200);
                    }
                },
                dataType : "json"
            });
        });
    }("#list", ".btn-status");


    // Sort
    !function(list, filter){
        var list = $(list);
        list.find(filter).append("<span class=\"glyphicon glyphicon-sort\"></span>");
        list.on("click", filter, function(){
            var th = $(this),
                index = th.prevAll().length,
                _tr = [], _data = [], _id = [];
            list.children("tbody").children("tr").each(function(i){
                var td = $(this).children("td").eq(index),
                    tr = td.parent(),
                    id = tr.data("id"),
                    data = td.data("sort");
                _data.push(data + "." + i);
                _id.push(id + "." + i);
                _tr.push(tr);
            });

            list.find(filter).not(th).attr("class", "sort");

            var sort = function(a, b){ return a-b; }

            if (th.is(".sort-up")) {
                _data.sort(sort).reverse();
                th.attr("class", "sort sort-down");
                th.children(".glyphicon").attr("class", "glyphicon glyphicon-sort-by-attributes-alt");
            } else if (th.is(".sort-down")) {
                _data = _id;
                _data.sort(sort).reverse();
                th.attr("class", "sort");
                th.children(".glyphicon").attr("class", "glyphicon glyphicon-sort");
            } else {
                _data.sort(sort);
                th.attr("class", "sort sort-up");
                th.children(".glyphicon").attr("class", "glyphicon glyphicon-sort-by-attributes");
            }

            for(x in _data){
                var d = _data[x].split("."), index = d[1];
                _tr[index].appendTo(list.children("tbody"));
            }
        });
    }("#list", ".sort");


    !function(btn, btns){
        var filter = btns;
        $(btn).click(function(){
            var btnlist = $(filter),
                refresh = function(btn){
                    var tr = btn.parents("tr").eq(0),
                        next = tr.nextAll("tr:not(.disable)").eq(0),
                        id = tr.data("id");
                    tr.addClass("warning");
                    btn.addClass("loading");
                    $.ajax({
                        type : "POST",
                        url  : "./refresh.php",
                        data : {id:id},
                        complete : function(){
                            tr.removeClass("warning danger");
                            btn.prop("disabled", false).removeClass("loading");
                            if (next.length) refresh(next.find(btnlist));
                        },
                        success : function(data){
                            if (data.s == 0){
                                var _tr = $(data.rs);
                                tr.html(_tr.html()).trigger("refresh");
                            }else{
                                tr.addClass("danger");
                            }
                        },
                        error : function(){
                            tr.addClass("danger");
                        },
                        dataType : "json",
                        timeout : <?php echo config('web.refresh_timeout') * 1000; ?>
                    });
                },
                first = btnlist.prop("disabled", true).eq(0);
            refresh(first);
        });
    }(".refresh-all", "#list tr:not(.disable) .btn-refresh");


    !function(btn, li){
        $(btn).click(function(){
            $(li).toggle();
        });
    }(".toggle-disabled", "tr.disable");
});
</script>

    <?php \pt\tool\action::exec('footer'); ?>

</body>
</html>