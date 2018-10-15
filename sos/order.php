<?php 
include 'global.php';

if(isset($_GET['action'])){
	if($_GET['action'] == "delete"){
		$id = $_GET['id'];
		if(!empty($id)){
		   //
		  $database->delete(DB_PREFIX."order",array('id'=>$id));
		  header("Location:order.php");
		}
	}
	
	if($_GET['action'] == "del"){
		
		$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
		$state = isset($_GET['state']) ? $_GET['state'] : '';
		$notifystatus = isset($_GET['notifystatus']) ? $_GET['notifystatus'] : '';
		$start_time = @$_GET["start_time"];
		$end_time = @$_GET["end_time"];
		
		$sql_where = " where 1 = 1 ";

		if( ($state != 'all') && ($state != '') ){
			$sql_where .= " and state = '".$state."' ";
		}
		if( ($notifystatus != 'all') && ($notifystatus != '') ){
			$sql_where .= " and notifystatus = '".$notifystatus."' ";
		}
		if(!empty($start_time)&&!empty($end_time)){
			$sql_where .= " and ordertime >= '".$start_time."' and ordertime < '".$end_time."' ";
		}
		if(!empty($keywords)){
			$sql_where .= " and (orderid = '".$keywords."' or account = '".$keywords."'   or tradeno= '".$keywords."' or orderip= '".$keywords."' ) ";
		}
		
		$database->query(" delete from ".DB_PREFIX."order ".$sql_where);		
		message('温馨提示',"清空数据成功",'order.php');
		exit;
	}
}

if(isset($_GET['act'])){

	$act = $_GET['act'];
	if($act == "set1"){	  
	  $interval = isset($_GET['interval'])?$_GET['interval']:10;
	  $_SESSION['interval'] = $interval;
	  
    }
	
	if($act == "set2"){	  
	  $auto_refresh = isset($_GET['auto_refresh'])?$_GET['auto_refresh']:'1';
	  $_SESSION['auto_refresh'] = $auto_refresh;	  
    }

}

?>

<?php include 'base.php';?>

<?php

$sql_order = " ORDER BY id DESC ";
$pageNumber = isset($_GET['pageNo']) ? $_GET['pageNo'] : 1;
$todattime="";
$start_time = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$end_time = isset($_POST['end_time']) ? $_POST['end_time'] : '';

$keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
if(empty($keywords)){
	$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
}

$state = isset($_POST['state']) ? $_POST['state'] : '';
if($state == ''){
	$state = isset($_GET['state']) ? $_GET['state'] : '';
}

$notifystatus = isset($_POST['notifystatus']) ? $_POST['notifystatus'] : '';
if($notifystatus == ''){
	$notifystatus = isset($_GET['notifystatus']) ? $_GET['notifystatus'] : '';
}

if(empty($start_time)||empty($end_time)){	
	$start_time = @$_GET["start_time"];
	$end_time = @$_GET["end_time"];
	
	if(!empty($start_time)&&!empty($end_time)){
		$start_time = date('Y-m-d H:i:s',$start_time);
		$end_time = date('Y-m-d H:i:s',$end_time);
	}
}

$sql_where = " where 1 = 1 ";

if( ($state != 'all') && ($state != '') ){
	$sql_where .= " and state = '".$state."' ";
}
if( ($notifystatus != 'all') && ($notifystatus != '') ){
	$sql_where .= " and notifystatus = '".$notifystatus."' ";
}
if(!empty($start_time)&&!empty($end_time)){
	$sql_where .= " and ordertime >= '".strtotime($start_time)."' and ordertime < '".strtotime($end_time)."' ";
}

if(!empty($keywords)){
	$sql_where .= " and (orderid = '".$keywords."' or account = '".$keywords."'    or orderip= '".$keywords."' ) ";
}

$sql_limit = " limit ".($pageNumber-1)*$pageSize.",".$pageSize." ";

$sql = " select * from ".DB_PREFIX."order $sql_where $sql_order $sql_limit  ";
$sql_size = " select count(*) as total from ".DB_PREFIX."order $sql_where "; 
$sql_x = " SELECT sum(amount) AS total FROM ".DB_PREFIX."order $sql_where ";

$size = $database->query($sql_size)->fetchAll();
$record = $size[0]["total"];

$x = $database->query($sql_x)->fetchAll();
$xiaofei = ($x[0]["total"]>0)?$x[0]["total"]:0;

$datas = $database->query($sql)->fetchAll();

			  








$tongji = '<li><a href="javascript:;">总金额：'.$xiaofei.'</a></li>';

$extraStr = '';
if(!empty($keywords)){
	$extraStr .= '&keywords='.$keywords;
}
if($state != ''){
	$extraStr .= '&state='.$state;
}
if($notifystatus != ''){
	$extraStr .= '&notifystatus='.$notifystatus;
}
if(!empty($start_time)&&!empty($end_time)){
	$extraStr .= '&start_time='.strtotime($start_time).'&end_time='.strtotime($end_time);
}

function payState($flag){
	switch($flag){
		case 0:
			return '处理中';
		case 1:
			return '<font color=red>已完成</font>';
	}
}
function payTpye($flag){
	switch($flag){
		case 1:
			return '微信';
		case 2:
			return '支付宝';
	}
}

function notifystatus($flag){
	switch($flag){
		case 0:
			return '待处理';
		case 1:
			return '<font color=red>已完成</font>';
		case 2:
			return '通知失败';

	}
}

if(isset($_SESSION['interval'])){
	$interval = $_SESSION['interval'];
}else{
	$interval = 10;
}

if(empty($interval)){
	$interval = 10;
}

if(isset($_SESSION['auto_refresh'])){
	$auto_refresh = $_SESSION['auto_refresh'];
}else{
	$auto_refresh = '0';
}








?>
<script type="text/javascript" src="lib/datepicker/WdatePicker.js"></script>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">订单管理</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">订单管理</li><li class="pull-right" style=" "><label style="height:30px;line-height:25px;display: inline-block;vertical-align: middle;font-size:12px;" >自动刷新：</label>
					
						<select ID="auto_refresh" name="auto_refresh" style="width:120px;margin-right:10px;font-size:12px;height: 25px;margin-bottom: 15px;">
                        <option Value="2" >关闭</option>
						<option Value="1" selected>开启</option>
						</select>
						
						<label style="height:30px;line-height:25px;display: inline-block;vertical-align: middle;font-size:12px;" >刷新间隔：</label>
					
						<select ID="interval" name="interval" style="width:120px;margin-right:10px;font-size:12px;height: 25px;margin-bottom: 15px;">
						<option Value="5" >5秒</option>
						<option Value="10" selected="selected">10秒</option>
                        <option Value="15" >15秒</option>
						<option Value="20" >20秒</option>
						<option Value="30" >30秒</option>
						<option Value="60" >60秒</option>
						</select>&nbsp;&nbsp;</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">	
					
					<form action="order.php" method="post" class="form-search pull-right">
					
					  <label style="height:30px;line-height:30px;display: inline-block;vertical-align: middle;">开始：</label>
					  <input id="start_time" name="start_time" type="text" class='Wdate' style='width:180px;margin-right:10px;' value="" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'end_time\')}'})"></input>
					  <label style="height:30px;line-height:30px;display: inline-block;vertical-align: middle;">截止：</label>
					  <input id="end_time" name="end_time" type="text" class='Wdate' style='width:180px;margin-right:10px;' value="" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'start_time\')}'})"></input>		
					
					  <select name="state" id="state" class="search-query" style="margin-right:10px;">
						<option value='all' >--订单状态--</option>
						<option value='0'>处理中</option>
						<option value='1' >已成功</option>
						<option value='2'>交易失败</option>
					  </select>

					  
					  <select name="notifystatus" id="notifystatus" class="search-query" style="margin-right:10px;">
						<option value='all' selected>--通知状态--</option>
						<option value='0'>处理中</option>
						<option value='1'>已完成</option>
						<option value='2'>失败</option>
					  </select>
					  
					  <input type="text"  name="keywords" id="keywords"  placeholder="用户名、IP、订单号" class="search-query">
	
					  <button type="submit" class="btn">搜索</button>
					  
					  <!--<a href="export.php?<?php echo $extraStr;?>" target='_blank' class="btn btn-primary">导出</a>-->
					  
					  <a href="javascript:;" onclick="delForm('order.php?action=del&<?php echo $extraStr;?>')" class="btn btn-danger">清空</a>
					  
					  
					</form>
					
				</div>				
				
					
				
				
					<table class="table table-hover table-bordered">
					  <thead>
						<tr>

						  <th>订单号</th>
						   <th>接口号</th>
						  <th>用户名</th>
						  <th>金额</th>
						  <th>真实金额</th>
						  <th>支付方式</th>
						  <th>订单时间</th>
						  <th>到帐时间</th>
						  <th>订单状态</th>
						  <th>下发状态</th>
						  <th>IP</th>
						  <th>微信号</th>
						  <th>操作</th>
						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  foreach($datas as $item){			
								  
					  ?>
					  <tr>

						  <td><?php echo $item['orderid'];?></td>
						  <td><?php echo $item['tradeno'];?></td>
						  <td><?php echo $item['account'];?></td>
						  <td><?php echo $item['amount'];?></td>
						  <td><?php echo $item['realamount'];?></td>
						  <td><?php echo payTpye($item['paytype']);?></td>
						  <td><?php echo date('Y-m-d H:i:s',$item['ordertime']);?></td>
						  <td><?php if($item['systime'] == '' || $item['systime'] == '0'){echo '';}else{ echo date('Y-m-d H:i:s',$item['systime']);};?></td>						  
						  <td><?php echo payState($item['state']);?></td>
						  <td><?php echo notifystatus($item['notifystatus']);?></td>
						   <td><?php echo $item['orderip'];?></td>
						   <td><?php echo $item['wechatid'];?></td>
						  <td>

						  <a title='删除' onclick="confirmAction('?action=delete&id=<?php echo $item['id'];?>')" href='javascript:;'>删除</a></td>
					  </tr>
					  <?php
					  }
					  ?>
						
						
					  </tbody>
					</table>
				
				<?php 
				echo bootpage($record,$pageSize,$pageNumber,"",$extraStr,$tongji);
				?>
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
</div>

<!-- 弹出model -->
<div id="myModal" class="modal hide fade in" style="display: none; ">
<div class="modal-header">
<a class="close" data-dismiss="modal">×</a>
<h3>处理订单</h3>
</div>
<div class="modal-body"></div>
</div>

<!-- page end -->

<script type="text/javascript">
$(function(){
	
	<?php 
		echo '$("#interval").val("'.$interval.'");';
		
		echo '$("#auto_refresh").val("'.$auto_refresh.'");';
	
       
		if($state!=""){
		     echo '$("#state option[value=\''.$state.'\']").attr("selected", "selected");';  
	    }
		if($notifystatus!=""){
		     echo '$("#notifystatus option[value=\''.$notifystatus.'\']").attr("selected", "selected");';  
	    }
        if(!empty($keywords)){
             echo '$("#keywords").val("'.$keywords.'");';
        }
		if(!empty($start_time)&&!empty($end_time)){
             echo '$("#start_time").val("'.$start_time.'");';
		     echo '$("#end_time").val("'.$end_time.'");';
        }
    ?>
	
	<?php 
	if($auto_refresh == '1'){
	?>
		setTimeout(refresh,<?php echo $interval*1000;?>);
		
	<?php
	}
	?>
	
	$('#interval').change(function(){
		var interval = $('#interval').val();
		window.location.href = 'order.php?act=set1&interval='+interval;
	})
		
	$('#auto_refresh').change(function(){
		var auto_refresh = $('#auto_refresh').val();
		window.location.href = 'order.php?act=set2&auto_refresh='+auto_refresh;
	})
	
	
	$("#client-menu").addClass('in');
	
	$('#myModal').on('hide.bs.modal', function () {
		//关闭模态框
		location.reload();
	})
	
})

	function refresh(){
		var auto_refresh = $('#auto_refresh').val();
		if(auto_refresh == "1"){
			window.location.href = 'order.php';
		}
	}

	function delForm(url){
		if(confirm('确定要清空搜索数据吗?')){
			var result = prompt("请输入操作密码：8888");
			if(result != '8888'){
				alert('密码错误!');
				return;
			}
			window.location.href = url;
		}		
	}

</script>

<script type="text/javascript">
$(function(){
	$("#client-menu").addClass('in');
})
</script>
<?php include 'foot.php';?>
