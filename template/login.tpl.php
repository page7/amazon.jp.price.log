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

</style>
</head>
<body>

    <div class="container" style="max-width:330px;">

        <form class="form-signin" role="form" method="POST">
            <h2 class="form-signin-heading">Please sign in</h2>
            <div class="alert alert-danger" role="alert" style="display:none;"></div>
            <input type="text" name="username" class="form-control" placeholder="Username" autocomplete="off" required autofocus>
            <input type="password" name="password" class="form-control" placeholder="Password" required data-enter="login()">
            <button class="btn btn-lg btn-primary btn-block" type="button" onclick="login()">Sign in</button>
        </form>

    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <script>
    function login(){
        var postdata = $(".form-signin").serialize();
        $.post("./login.php", postdata, function(data){
            if(data.s == 0){
                location.href = "./index.php";
            }else{
                $(".alert").text(data.err).slideDown(200);
            }
        }, "json");
    }
    </script>

</body>
</html>
