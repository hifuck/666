<?php 
include 'global.php';
if(isset($_GET['action'])){
	if($_GET['action'] == "delete"){
		$id = $_GET['id'];
		if(!empty($id)){
		  $database->delete(DB_PREFIX."noorder",array('id'=>$id));
		 	  
		  header("Location:noorder.php");
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

if(empty($start_time)||empty($end_time)){	
	$start_time = @$_GET["start_time"];
	$end_time = @$_GET["end_time"];
	
	if(!empty($start_time)&&!empty($end_time)){
		$start_time = date('Y-m-d H:i:s',$start_time);
		$end_time = date('Y-m-d H:i:s',$end_time);
	}
}

$sql_where = " where 1 = 1 ";


if(!empty($start_time)&&!empty($end_time)){
	$sql_where .= " and ordertime >= '".strtotime($start_time)."' and ordertime < '".strtotime($end_time)."' ";
}

$sql_limit = " limit ".($pageNumber-1)*$pageSize.",".$pageSize." ";

$sql = " select * from ".DB_PREFIX."noorder $sql_where $sql_order $sql_limit  ";
$sql_size = " select count(*) as total from ".DB_PREFIX."noorder $sql_where "; 
$sql_x = " SELECT sum(amount) AS total FROM ".DB_PREFIX."noorder $sql_where ";

$size = $database->query($sql_size)->fetchAll();
$record = $size[0]["total"];

$x = $database->query($sql_x)->fetchAll();
$xiaofei = ($x[0]["total"]>0)?$x[0]["total"]:0;

$datas = $database->query($sql)->fetchAll();


$tongji = '<li><a href="javascript:;">总金额：'.$xiaofei.'</a></li>';

$extraStr = '';

if(!empty($start_time)&&!empty($end_time)){
	$extraStr .= '&start_time='.$start_time.'&end_time='.$end_time;
}			  



?>
<script type="text/javascript" src="lib/datepicker/WdatePicker.js"></script>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">未匹配金额</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">未匹配金额</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">	
					
					<form action="noorder.php" method="post" class="form-search pull-left">
					
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

						  <th>金额</th>
						  <th>时间</th>
						  <th>接口订单号</th>
						  <th>帐号</th>
						  <th>操作</th>

						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  foreach($datas as $item){			
									  
					  ?>
					  <tr>

						<td style="text-align: center;"><?php echo $item['amount'];?></td>
						  <td style="text-align: center;"><?php echo date('Y-m-d H:i:s',$item['ordertime']);?></td>
						  <td style="text-align: center;"><?php echo $item['wechatid'];?></td>
						  <td style="text-align: center;"><?php echo $item['userid'];?></td>
						  <td style="text-align: center;"><a title='删除' onclick="confirmAction('?action=delete&id=<?php echo $item['id'];?>')" href='javascript:;' >删除</a></td>

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


</script>
<script type="text/javascript">
$(function(){
	$("#client-menu").addClass('in');
})
</script>
<?php include 'foot.php';?>
