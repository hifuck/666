<?php 
include 'global.php';

$action = 'edit';

if(isset($_GET['act'])){
	if($_GET['act'] == 'edit'){
		
		

		
		
		
		
		
		$id = @$_POST['id'];
		
		$info = $database->get(DB_PREFIX.'set','*',array('id'=>$id));
		
		$upArr = array('is_auto'=>@$_POST['is_auto'],'interval'=>@$_POST['interval'],'page_size'=>@$_POST['page_size']);
		
		$database->update(DB_PREFIX.'set',$upArr,array('id'=>$id));
		
		echo json_encode(array('stat'=>0));
		
		return;
	}
}

?>

<?php include 'base.php';?>

<!-- page start -->

<?php 
if($action == 'edit'){

	$info = $database->get(DB_PREFIX.'set','*',array('id'=>1));

?>
<div class="content">
    <div class="header">
        <h1 class="page-title">系统设置</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">系统设置</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">
		
			<form class="form-horizontal" onsubmit="return false;">
			  
			  <div class="control-group">
				<label class="control-label"  for="interval">刷新间隔(秒)</label>
				<div class="controls">
				  <input type="text" id="interval" value="<?php echo $info['interval'];?>">
				</div>
			  </div>
			  <div class="control-group">
				<label class="control-label"  for="page_size">默认分页</label>
				<div class="controls">
				  <input type="text" id="page_size" value="<?php echo $info['page_size'];?>">
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

			var interval = $("#interval").val();
			if(interval == ""){
				alert("刷新间隔不能为空");
				return;
			}
			
			var page_size = $("#page_size").val();
			if(page_size == ""){
				alert("默认分页不能为空");
				return;
			}

			$.post("setting.php?act=edit",{id:$("#hid_id").val(),interval:interval,page_size:page_size},function(obj){
				if(obj.stat == 0){
					alert('修改成功!');
					window.location.href = 'setting.php';
				}else{
					alert('修改失败!');
				}

			},"json");
			
		})
	})
</script>

<?php 
}
?>

<script type="text/javascript">
$(function(){
	$("#legal-menu").addClass('in');
})
</script>


<?php include 'foot.php';?>
