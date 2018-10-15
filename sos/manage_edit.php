<?php 
include 'global.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'add';

if(isset($_GET['act'])){
	if($_GET['act'] == 'edit'){
		$id = @$_POST['id'];
		
		$info = $database->get(DB_PREFIX.'manage','*',array('id'=>$id));
		if($info['level'] == 1){
			echo json_encode(array('stat'=>1));
			return;
		}
		$password=md5(md5(trim($_POST['password']))."886");
		if(empty($_POST['password'])){
			$upArr = array('nickname'=>@$_POST['nickname']);
		}else{
			$upArr = array('nickname'=>@$_POST['nickname'],'password'=>@$password);
		}
		
		
		$database->update(DB_PREFIX.'manage',$upArr,array('id'=>$id));
		
		echo json_encode(array('stat'=>0));
		
		return;
	}
	
	if($_GET['act'] == 'add'){
	
		$username = trim(@$_POST['username']);
	
		$isExist = $database->get(DB_PREFIX.'manage','*',array('username'=>$username));
		if($isExist){
			echo json_encode(array('stat'=>1));
			return ;
		}
		$password=md5(md5(trim($_POST['password']))."886");
		$database->insert(DB_PREFIX.'manage',array('level'=>2,'username'=>$username,'password'=>@$password,'nickname'=>@$_POST['nickname'],'lastlogin'=>date('Y-m-d H:i:s')));
		
		echo json_encode(array('stat'=>0));
		return;
	}
}

?>

<?php include 'base.php';?>

<!-- page start -->


<?php 
if($action == 'add'){
?>
<div class="content">
    <div class="header">
        <h1 class="page-title">添加操作员</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li><a href="manage.php">用户管理</a>  <span class="divider">/</span>
        </li>
        <li class="active">添加操作员</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">
		
			<form class="form-horizontal" onsubmit="return false;">
			  <div class="control-group">
				<label class="control-label"  for="username">用户名</label>
				<div class="controls">
				  <input type="text" id="username" value="">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="password">用户密码</label>
				<div class="controls">
				  <input type="text" id="password" value="">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="r_password">确认密码</label>
				<div class="controls">
				  <input type="text" id="r_password" value="">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="nickname">用户昵称</label>
				<div class="controls">
				  <input type="text" id="nickname" value="">
				</div>
			  </div>			  
			  <div class="control-group">
				<div class="controls">
				  <button id="btnSave" class="btn btn-success">确定添加</button>
				</div>
			  </div>
			</form>
			
        </div>
		
		<footer>
                <hr>
                <p class="pull-right">
                    <a href="javascript:;">
                        <?php echo $appSet[ 'app_name'];?>
                    </a>
                </p>
                <p>&copy;
                    <?php echo $appSet[ 'company_year'];?>
                    <a href="<?php echo $appSet['company_url'];?>" title="<?php echo $appSet['company'];?>" target="_blank">
                        <?php echo $appSet[ 'company'];?>
                    </a>
                </p>
            </footer>
		
    </div>
</div>

<script type="text/javascript">
	$(function(){
		$("#btnSave").click(function(){
			
			var _username = $("#username").val();
			if(_username == ""){
				alert("用户帐号不能为空");
				return;
			}

			var _password = $("#password").val();
			if(_password == ""){
				alert("用户密码不能为空");
				return;
			}
			
			var r_password = $("#r_password").val();
			if(r_password!=_password){
				alert("两次输入密码不一致");
				return;
			}
			
			var _nickname = $("#nickname").val();
			if(_nickname == ""){
				alert("用户昵称不能为空");
				return;
			}

			$.post("manage_edit.php?act=add",{username:_username,password:_password,nickname:_nickname},function(obj){
				if(obj.stat == 0){
					alert('添加成功!');
					window.location.href = 'manage.php';
				}else{
					alert('用户名已存在!');
				}

			},"json");
			
		})
	})
</script>
<script type="text/javascript">
$(function(){
	$("#legal-menu").addClass('in');
})
</script>

<?php 
}	
if($action == 'edit'){
	$id = $_GET['id'];
	$info = $database->get(DB_PREFIX.'manage','*',array('id'=>$id));
	if($info['level'] == 1){
		exit;
	}
?>
<div class="content">
    <div class="header">
        <h1 class="page-title">修改操作员信息</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li><a href="username.php">用户管理</a>  <span class="divider">/</span>
        </li>
        <li class="active">修改信息</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">
		
			<form class="form-horizontal" onsubmit="return false;">
			  <div class="control-group">
				<label class="control-label"  for="username">用户名</label>
				<div class="controls">
				  <input type="text" id="username" readonly value="<?php echo $info['username'];?>">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="password">新密码</label>
				<div class="controls">
				  <input type="password" id="password" value="">(为空则不修改)
				</div>
			  </div>

			  <div class="control-group">
				<label class="control-label"  for="nickname">用户昵称</label>
				<div class="controls">
				  <input type="text" id="nickname" value="<?php echo $info['nickname'];?>">
				</div>
			  </div>			  
			  <div class="control-group">
				<div class="controls">
				  <input type="hidden" id="hid_id" name="hid_id" value="<?php echo $info['id'];?>" />
				  <button id="btnSave" class="btn btn-success">提交修改</button>
				</div>
			  </div>
			</form>
			
        </div>
		
		<footer>
                <hr>
                <p class="pull-right">
                    <a href="javascript:;">
                        <?php echo $appSet[ 'app_name'];?>
                    </a>
                </p>
                <p>&copy;
                    <?php echo $appSet[ 'company_year'];?>
                    <a href="<?php echo $appSet['company_url'];?>" title="<?php echo $appSet['company'];?>" target="_blank">
                        <?php echo $appSet[ 'company'];?>
                    </a>
                </p>
            </footer>
		
    </div>
</div>

<script type="text/javascript">
	$(function(){
		$("#btnSave").click(function(){

			var _password = $("#password").val();

			

			
			var _nickname = $("#nickname").val();
			if(_nickname == ""){
				alert("用户昵称不能为空");
				return;
			}

			$.post("manage_edit.php?act=edit",{id:$("#hid_id").val(),password:_password,nickname:_nickname},function(obj){
				if(obj.stat == 0){
					alert('修改成功!');
					window.location.href = 'manage.php';
				}else{
					alert('修改失败!');
				}

			},"json");
			
		})
	})
</script>

<script type="text/javascript">
$(function(){
	$("#legal-menu").addClass('in');
})
</script>

<?php 
}
?>

<?php include 'foot.php';?>
