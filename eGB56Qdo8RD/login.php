<?php 
include '../init.php';

if(isset($_GET['act'])){
	if($_GET['act'] == 'login'){
		$username = trim($_POST['user']);
		$password = md5(md5(trim($_POST['pw']))."886");
		$img_code = md5(strtolower(@$_POST['imgcode']));
		$sessionCode = $_SESSION["vframe_verify"];
		if($sessionCode != $img_code){
			message('温馨提示',"验证码错误",'index.php');
			return;
		}
		$r = $database->get(DB_PREFIX."manage","*",array('AND'=>array('username'=>$username,'password'=>$password)));
		if($r){
			
			$_SESSION['card_admin'] = array('username'=>$username, 'level'=>$r['level'],'password'=>$r['password'], 'nickname'=>$r['nickname']);

			
			$database->update(DB_PREFIX."manage",array('lastlogin'=>date('Y-m-d H:i:s'),'last_ip'=>get_client_ip()),array('username'=>$r['username']));
		    $database->insert(DB_PREFIX.'log',array('user_name'=>$username,'login_time'=>date('Y-m-d H:i:s'),'login_ip'=>get_client_ip()));
			
			message('温馨提示',"登录成功,即将进入管理界面",'index.php');
			
			return;
			
		}else{
			message('温馨提示',"用户名或者密码错误",'index.php');
			return;
		}
		
	}
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>订单系统</title>
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="lib/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="stylesheets/theme.css">
    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.css">
    <script src="lib/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="lib/bootstrap/js/bootstrap.js"></script>

    <!-- Demo page code -->
    <style type="text/css">
        .brand { font-family: georgia, serif; }
        .brand .first {
            color: #ccc;
            font-style: italic;
        }
        .brand .second {
            color: #fff;
            font-weight: bold;
        }
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="lib/html5.js"></script>
    <![endif]-->

  </head>

  <!--[if lt IE 7 ]> <body class="ie ie6"> <![endif]-->
  <!--[if IE 7 ]> <body class="ie ie7 "> <![endif]-->
  <!--[if IE 8 ]> <body class="ie ie8 "> <![endif]-->
  <!--[if IE 9 ]> <body class="ie ie9 "> <![endif]-->
  <!--[if (gt IE 9)|!(IE)]><!--> 
  <body class=""> 
  <!--<![endif]-->
    
    <div class="navbar">
        <div class="navbar-inner">
                <ul class="nav pull-right">                    
                </ul>
                <a class="brand" href="javascript:;"><span class="second">订单系统</span></a>
        </div>
    </div>    
        <div class="row-fluid">
			<div class="dialog">
				<div class="block">
					<p class="block-heading">系统登录</p>
					<div class="block-body">
						<form id="logform" name="logform" method="POST" action='login.php?act=login'>
							<label>用户名</label>
							<input id="user" name="user" type="text"  class="span12">
							<label>密&nbsp;&nbsp;码</label>
							<input id="pw" name="pw" type="password"  class="span12">
							<label>验证码</label>
							<div class="form-inline">					
							<input type="text" id="imgcode" name="imgcode" class="span12" style="width:120px;">
							<img title="点击切换验证码"  style="cursor:pointer;margin-left:10px;" src="/include/vercode.php" onclick="this.src='core/vercode.php?r='+Math.random()" />
							</div>
							<a href="#" id="btnLogin" class="btn btn-primary pull-right">登&nbsp;&nbsp;录</a>
							<label class="remember-me"><input type="checkbox"> 记住我</label>
							<div class="clearfix"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
    
  </body>
  <script type="text/javascript">
		$(function() {
			
			document.onkeydown = function(evt){
		   　 var evt = window.event?window.event:evt;
			　if (evt.keyCode==13) {
				   document.forms["logform"].submit();
			　}
		   }
           
			$("#btnLogin").click(function(){
				if($("#user").val()==""){
					alert("用户名不能为空!");
					return;
				}
				
				if($("#pw").val()==""){
					alert("密码不能为空!");
					return;
				}
				
				$("#logform").submit();
			})
        });
  </script>
  
</html>


