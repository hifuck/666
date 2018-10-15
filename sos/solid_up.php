<?php 
include 'global.php';
$action = isset($_GET['action']) ? $_GET['action'] : 'add';

if(isset($_GET['act'])){
	if($_GET['act'] == 'add'){
		$wechatid = trim(@$_POST['wechatid']);
		$urls = @$_POST['urls'];
		$desc = @$_POST['desc'];
		$iswx = @$_POST['iswx'];
		$isali = @$_POST['isali'];

		$inArr = array('wechatid'=>$wechatid,'urls'=>$urls,'desc'=>$desc,'isopen'=>0,'iswx'=>$iswx,'isali'=>$isali);
		// print_r($inArr);die;
		$database->insert(DB_PREFIX.'solidsupp',$inArr);
		echo json_encode(array('stat'=>0));
		return;
	}
	if($_GET['act'] == 'edit'){
		$id = @$_POST['id'];
		$desc = @$_POST['desc'];
		$iswx = @$_POST['iswx'];
		$isali = @$_POST['isali'];
		$upArr = array('desc'=>$desc,'iswx'=>$iswx,'isali'=>$isali);		
		$database->update(DB_PREFIX.'solidsupp',$upArr,array('id'=>$id));
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
        <h1 class="page-title">添加固定码</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li><a href="codesupp.php">固定码</a><span class="divider">/</span>
        </li>
        <li class="active">添加固定码</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">
		
			<form class="form-horizontal" onsubmit="return false;">
			  <div class="control-group">
				<label class="control-label"  for="wechatid">帐号</label>
				<div class="controls">
				  <input type="text" id="wechatid" value="">
				</div>
			  </div>
			  
			  <div class="control-group">
				<label class="control-label"  for="urls">链接</label>
				<div class="controls">
				  <input type="text" id="urls" value="">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="desc">备注</label>
				<div class="controls">
				  <input type="text" id="desc" value="">
				</div>
			  </div>
			   <div class="control-group">
				<label class="control-label"  for="iswx">微信</label>
				<div class="controls">
					<input type="radio" id="iswx" name="iswx" checked='checked' value="1">开启
					<input type="radio" id="iswx" name="iswx"  value="0" style="margin-left:15px;">关闭
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="isali">支付宝</label>
				<div class="controls">
					<input type="radio" id="isali" name="isali" checked='checked' value="1">开启
					<input type="radio" id="isali" name="isali"  value="0" style="margin-left:15px;">关闭
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
			
			var _wechatid = $("#wechatid").val();
			if(_wechatid == ""){
				alert("帐号不能为空");
				return;
			}
			var _urls = $("#urls").val();
			if(_urls == ""){
				alert("链接不能为空");
				return;
			}
			var _desc = $("#desc").val();
			if(_desc == ""){
				alert("备注不能为空");
				return;
			}
			var _iswxs = document.getElementsByName("iswx");
			  for(var i=0;i<_iswxs.length;i++){
				 if(_iswxs[i].checked)
					var _iswx = _iswxs[i].value;
			  }
			  var _isalis = document.getElementsByName("isali");
			  for(var i=0;i<_isalis.length;i++){
				 if(_isalis[i].checked)
					var _isali = _isalis[i].value;
			  }
			$.post("solid_up.php?act=add",{wechatid:_wechatid,urls:_urls,desc:_desc,iswx:_iswx,isali:_isali},function(obj){
							if(obj.stat == 0){
								alert('添加成功!');
								window.location.href = 'solidsupp.php';
							}else{
								alert(obj.msg);
							}

						},"json");
				
		})
	})
</script>
<script type="text/javascript">
$(function(){
	$("#accounts-menu").addClass('in');
})
</script>
<?php 
}	
if($action == 'edit'){
	$id = $_GET['id'];
	$info = $database->get(DB_PREFIX.'solidsupp','*',array('id'=>$id));

?>
<div class="content">
    <div class="header">
        <h1 class="page-title">修改固定码</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li><a href="solidsupp.php">固定码</a>  <span class="divider">/</span>
        </li>
        <li class="active">修改信息</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">

		
			<form class="form-horizontal" onsubmit="return false;">
			  <div class="control-group">
				<label class="control-label"  for="wechatid">帐号</label>
				<div class="controls">
				  <input type="text" id="wechatid" readonly value="<?php echo $info['wechatid'];?>">
				</div>
			  </div>
			  
			  <div class="control-group">
				<label class="control-label"  for="urls">链接</label>
				<div class="controls">
				  <input type="text" id="urls" readonly value="<?php echo $info['urls'];?>">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="desc">备注</label>
				<div class="controls">
				  <input type="text" id="desc" value="<?php echo $info['desc'];?>">
				</div>
			  </div>		  
			  <div class="control-group">
				<label class="control-label"  for="iswx">微信</label>
				<div class="controls">
					<input type="radio" id="iswx" name="iswx" <?php if($info['iswx']==1){ echo "checked='checked'"; }?> value="1">开启
					<input type="radio" id="iswx" name="iswx" <?php if($info['iswx']==0){ echo "checked='checked'"; }?>  value="0" style="margin-left:15px;">关闭
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="isali">支付宝</label>
				<div class="controls">
					<input type="radio" id="isali" name="isali" <?php if($info['isali']==1){ echo "checked='checked'"; }?> value="1">开启
					<input type="radio" id="isali" name="isali" <?php if($info['isali']==0){ echo "checked='checked'"; }?> value="0" style="margin-left:15px;">关闭
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
			
			var _wechatid = $("#wechatid").val();
			if(_wechatid == ""){
				alert("帐号不能为空");
				return;
			}
			var _urls = $("#urls").val();
			if(_urls == ""){
				alert("链接不能为空");
				return;
			}
			var _desc = $("#desc").val();
			if(_desc == ""){
				alert("备注不能为空");
				return;
			}
			var _iswxs = document.getElementsByName("iswx");
			  for(var i=0;i<_iswxs.length;i++){
				 if(_iswxs[i].checked)
					var _iswx = _iswxs[i].value;
			  }
			  var _isalis = document.getElementsByName("isali");
			  for(var i=0;i<_isalis.length;i++){
				 if(_isalis[i].checked)
					var _isali = _isalis[i].value;
			  }
			$.post("solid_up.php?act=edit",{id:$("#hid_id").val(),wechatid:_wechatid,urls:_urls,desc:_desc,iswx:_iswx,isali:_isali},function(obj){
							if(obj.stat == 0){
								alert('添加成功!');
								window.location.href = 'solidsupp.php';
							}else{
								alert(obj.msg);
							}

						},"json");
				
		})
	})
</script>

<script type="text/javascript">
$(function(){
	$("#accounts-menu").addClass('in');
})
</script>

<?php 
}
?>




<?php 
include 'foot.php';
?>