<?php 
include 'global.php';

?>

<?php include 'base.php';?>

<?php

@$orderid = $_REQUEST['orderid'];

if($orderid )
{

	$sql = " SELECT * FROM ".DB_PREFIX."order where orderid = '$orderid' and state = 1 ";
	$data = $database->query($sql)->fetchAll();
	
			$orderno = $orderid;	//订单号
			
			$notifyurl = $data[0]['notifyurl'];
						
			$redata = array(
				'merId'	=> $merid,
				'orderno' => $orderno,
				'account' => $data[0]['account'],
				'amount' => $data[0]['amount'],
				'realamount' => $data[0]['realamount'],
				
			);
			$redata['sign'] = md5(urldecode(http_build_query($redata)).'&key='.$mer_key);
			
						
			
			$post_string = http_build_query($redata);
			
			$url=$notifyurl.'?'.$post_string;
	
	
	
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
        <h1 class="page-title">订单详情</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">订单详情</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid" style="padding-top:20px;">
		<?php
			if(@$data){
		?>
            <form class="form-horizontal" onsubmit="return false;">  			
				<div class="control-group">
					<label class="control-label"  for="interval">订单号：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo $data[0]['orderid'];?>
					</div>
				 </div>	
				 
				 <div class="control-group">
					<label class="control-label"  for="interval">用户名：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo $data[0]['account'];?>
					</div>
				 </div>	
				
				 <div class="control-group">
					<label class="control-label"  for="interval">金额：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo $data[0]['amount'];?>
					</div>
				 </div>	
				  <div class="control-group">
					<label class="control-label"  for="interval">真实金额</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo $data[0]['realamount'];?>
					</div>
				 </div>	
				 <div class="control-group">
					<label class="control-label"  for="interval">订单时间：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo date('Y-m-d H:i:s',$data[0]['ordertime']);;?>
					</div>
				 </div>	
				 <div class="control-group">
					<label class="control-label"  for="interval">订单状态：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo payState($data[0]['state']);?>
					</div>
				 </div>	
				 <div class="control-group">
					<label class="control-label"  for="interval">下发状态：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo notifystatus($data[0]['notifystatus']);?>
					</div>
				 </div>	
				  
				 <div class="control-group">
					<label class="control-label"  for="interval">回调地址：</label>
					<div class="controls" style="line-height:30px;">
					  <?php echo $data[0]['notifyurl'];?>
					</div>
				 </div>	
				 <div class="control-group">
					<label class="control-label"  for="interval">回调链接：</label>
					<div class="controls" style="line-height:30px;">
					  <a href='<?php echo $url;?>' target='_blank' >打开</a>
					</div>
				 </div>	
			</form>
		<?php
			}else{
		?>	
			 <form class="form-horizontal" onsubmit="return false;">  			
				<div class="control-group">
					<label class="control-label"  for="interval">订单查询错误</label>
					
				 </div>	
			</form>
		<?php
			}
		?>
				
						
						
					  </tbody>
					</table>
				
				
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
	
	
	
	$("#client-menu").addClass('in');
	
	$('#myModal').on('hide.bs.modal', function () {
		//关闭模态框
	})
	
})


</script>
<script type="text/javascript">
$(function(){
	$("#client-menu").addClass('in');
})
</script>
<?php include 'foot.php';?>
