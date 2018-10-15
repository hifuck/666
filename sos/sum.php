<?php 
include 'global.php';
?>
<?php include 'base.php';?>
<?php 


if(date('His') < '120000')
{
	$start_time = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : date('Y-m-d',strtotime("-1 day")).' 12:00:00';
	$end_time = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : date('Y-m-d').' 11:59:59';
}
else
{
	$start_time = isset($_REQUEST['start_time']) ? $_REQUEST['start_time'] : date('Y-m-d').' 12:00:00';
	$end_time = isset($_REQUEST['end_time']) ? $_REQUEST['end_time'] : date('Y-m-d',strtotime("+1 day")).' 11:59:59';
}


$sql_where = " ";
if(!empty($start_time)&&!empty($end_time)){
	$sql_where .= " ordertime >= '".strtotime($start_time)."' and ordertime < '".strtotime($end_time)."' ";
}


$sql_size = " SELECT COUNT(*) AS total FROM ".DB_PREFIX."order  where  $sql_where and state=1";
$size = $database->query($sql_size)->fetchAll();
$todayNum = $size[0]["total"];

$sql_size = " SELECT COUNT(*) AS total FROM ".DB_PREFIX."order  where   $sql_where ";
$size = $database->query($sql_size)->fetchAll();
$todayNums = $size[0]["total"];

$sql_size = " SELECT sum(amount) AS total FROM ".DB_PREFIX."order  where $sql_where and state=1";
$size = $database->query($sql_size)->fetchAll();
$todayMoney = $size[0]["total"] > 0 ? $size[0]["total"] : 0;

$sql_size = " SELECT sum(amount) AS total FROM ".DB_PREFIX."order  where $sql_where ";
$size = $database->query($sql_size)->fetchAll();
$todayMoneys = $size[0]["total"] > 0 ? $size[0]["total"] : 0;

?>
<script type="text/javascript" src="lib/datepicker/WdatePicker.js"></script>
<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">平台帐目汇总</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">平台帐目汇总</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">	
					
					<form action="sum.php" method="post" class="form-search pull-left">
					
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
						  <th>汇总</th>
						  <th>有效订单数</th>
						  <th>提交订单数</th>						  
						  <th>订单支付总额</th>	
						  <th>提交订单总额</th>	
						 
						</tr>
					  </thead>
					  <tbody>
							
							<tr>
							  <td style="text-align: center;font-weight: bold;color:red">总计</td>
							  <td style="text-align: center;color:red"><?php echo $todayNum;?></td>
							  <td style="text-align: center;color:red"><?php echo $todayNums;?></td>
							  <td style="text-align: center;color:red"><?php echo $todayMoney;?></td>
							  <td style="text-align: center;color:red"><?php echo $todayMoneys;?></td>	
							</tr>
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
<script type="text/javascript">
$(function(){
	
	<?php 

		if(!empty($start_time)&&!empty($end_time)){
             echo '$("#start_time").val("'.$start_time.'");';
		     echo '$("#end_time").val("'.$end_time.'");';
        }
    ?>
	
	
})


</script>
<!-- page end -->

<script type="text/javascript">
$(function(){
	$("#accounts-menu").addClass('in');
})
</script>

<?php include 'foot.php';?>
