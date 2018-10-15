<?php 
include 'global.php';

if(isset($_GET['act'])){
	
	if($_GET['act'] == 'callback'){
		$orderid = @$_POST['orderid'];
		if($orderid)
		{
			$sql = " SELECT * FROM ".DB_PREFIX."order where orderid = '$orderid' and state = 1 ";
			$data = $database->query($sql)->fetchAll();			
			$orderno = $orderid;	//订单号
			
			$notifyurl	=	$data[0]['notifyurl'];
			$redata = array(
				'merId'	=> $merid,
				'orderno' => $orderno,
				'account' => $data[0]['account'],
				'amount' => $data[0]['amount'],
				'realamount' => $data[0]['realamount'],
				
			);
			$redata['sign'] = md5(urldecode(http_build_query($redata)).'&key='.$mer_key);
			
			$res=request_curl($notifyurl, http_build_query($redata));
			if($res=='success'){
				$upArr = array('notifystatus' => '1');
				$database->update(DB_PREFIX . 'order', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '1', 'realamount' => number_format($data[0]['realamount'], 2, ".", ""))));
				echo json_encode(array('stat'=>0));
				return;
			}			
			else
			{
				echo json_encode(array('msg'=>$res));
				return;
			}
		}
	}
	

	
	
}

?>

<?php include 'base.php';?>

<?php

$sql_order = " ORDER BY id DESC ";
$pageNumber = isset($_GET['pageNo']) ? $_GET['pageNo'] : 1;
$todattime="";
$start_time = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : date('Y-m-d').' 00:00:00';
$end_time = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : date('Y-m-d').' 23:59:59';

$keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
if(empty($keywords)){
	$keywords = isset($_GET['keywords']) ? $_GET['keywords'] : '';
}

$state = isset($_POST['state']) ? $_POST['state'] : '1';
if($state == ''){
	$state = isset($_GET['state']) ? $_GET['state'] : '';
}

$notifystatus = isset($_POST['notifystatus']) ? $_POST['notifystatus'] : '0';
if($notifystatus == ''){
	$notifystatus = isset($_GET['notifystatus']) ? $_GET['notifystatus'] : '0';
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
	$sql_where .= " and (orderid = '".$keywords."' or account = '".$keywords."'   or tradeno= '".$keywords."' ) ";
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






$extraStr = '';

if(!empty($start_time)&&!empty($end_time)){
	$extraStr .= '&start_time='.$start_time.'&end_time='.$end_time;
}			  










function payState($flag){
	switch($flag){
		case 0:
			return '处理中';
		case 1:
			return '<font color=red>已完成</font>';
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











?>
<script type="text/javascript" src="lib/datepicker/WdatePicker.js"></script>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">通知状态</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">通知状态</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">	
					
					<form action="notify.php" method="post" class="form-search pull-left">
					
					  <label style="height:30px;line-height:30px;display: inline-block;vertical-align: middle;">开始：</label>
					  <input id="start_time" name="start_time" type="text" class='Wdate' style='width:180px;margin-right:10px;' value="" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',maxDate:'#F{$dp.$D(\'end_time\')}'})"></input>
					  <label style="height:30px;line-height:30px;display: inline-block;vertical-align: middle;">截止：</label>
					  <input id="end_time" name="end_time" type="text" class='Wdate' style='width:180px;margin-right:10px;' value="" onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',minDate:'#F{$dp.$D(\'start_time\')}'})"></input>					  
	
					  <button type="submit" class="btn">搜索</button>
					  

					 
					</form>
				</div>				
				
				
					<table class="table table-hover table-bordered">
					  <thead>
						<tr>

						  <th>订单号</th>
						  <th>用户名</th>
						  <th>金额</th>
						  <th>真实金额</th>
						
						  <th>订单时间</th>
						  <th>到帐时间</th>
						  <th>订单状态</th>
						  <th>下发状态</th>
						  <th>明细</th>
						  <th>操作</th>

						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  foreach($datas as $item){			
									  
					  ?>
					  <tr>

						<td><?php echo $item['orderid'];?></td>
						  <td><?php echo $item['account'];?></td>
						  <td><?php echo $item['amount'];?></td>
						  <td><?php echo $item['realamount'];?></td>
						  <td><?php echo date('Y-m-d H:i:s',$item['ordertime']);?></td>
						  <td><?php if($item['systime'] == '' || $item['systime'] == '0'){echo '';}else{ echo date('Y-m-d H:i:s',$item['systime']);};?></td>						  
						  <td><?php echo payState($item['state']);?></td>
						  <td><?php echo notifystatus($item['notifystatus']);?></td>
						  <td><a href="ordermore.php?orderid=<?php echo $item['orderid'];?>"  >更多</a></td>
						  <td width="135"><a href="javascript:void(0)" id="<?php echo $item['orderid'];?>" onclick="recallback('<?php echo $item['orderid'];?>')">补发</a></td>

					  </tr>
					  <?php
					  }
					  ?>
						
						
					  </tbody>
					</table>
				
				<?php 
				echo bootpage($record,$pageSize,$pageNumber,"",$extraStr);
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

		if(!empty($start_time)&&!empty($end_time)){
             echo '$("#start_time").val("'.$start_time.'");';
		     echo '$("#end_time").val("'.$end_time.'");';
        }
    ?>
	
	$("#client-menu").addClass('in');
	
	$('#myModal').on('hide.bs.modal', function () {
		//关闭模态框
	})
	
})

	function recallback(orderid){
		$('#'+orderid).text('补发中...');
		$.post("notify.php?act=callback",{orderid:orderid},function(obj){
				if(obj.stat == 0){
					alert('补发成功!');
					$('#'+orderid).text('补发成功');
					window.location.href = 'notify.php?pageNo=1<?php echo $extraStr;?>';
				}else{
					alert('补发失败!失败原因：'+obj.msg);
					$('#'+orderid).text('补发失败');
				}

			},"json");
	}

</script>
<script type="text/javascript">
$(function(){
	$("#client-menu").addClass('in');
})
</script>
<?php include 'foot.php';?>
