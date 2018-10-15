<?php 
include 'global.php';
if(isset($_GET['action'])){
	if($_GET['action'] == "delete"){
		$id = $_GET['id'];
		if(!empty($id)){
		  $database->delete(DB_PREFIX."solidsupp",array('id'=>$id));
		  header("Location:solidsupp.php");
		}
	}
	if($_GET['action'] == "up"){
		$id = $_GET['id'];
		$state = $_GET['state'];
		if(!empty($id)){
			$upArr = array('isopen'=>$state);
			$database->update(DB_PREFIX.'solidsupp',$upArr,array('id'=>$id));	    	  
			header("Location:solidsupp.php");
		}
	}
}
?>
<?php include 'base.php';?>
<?php 
$sql_order = " ORDER BY id DESC ";
$pageNumber = isset($_GET['pageNo']) ? $_GET['pageNo'] : 1;
$sql_limit = " limit ".($pageNumber-1)*$pageSize.",".$pageSize." ";
$sql = "SELECT * FROM ".DB_PREFIX."solidsupp $sql_order  $sql_limit ";
$sql_size = " SELECT COUNT(*) AS total FROM ".DB_PREFIX."solidsupp a ";
$size = $database->query($sql_size)->fetchAll();
$record = $size[0]["total"];
$datas = $database->query($sql)->fetchAll();
?>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">固定码</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">固定码</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">
					<button onclick="window.location.href='solid_up.php'" class="btn btn-primary"><i class="icon-plus"></i> 添加固定码</button>

				</div>				
				
					<table class="table table-hover table-bordered">
					  <thead>
						<tr>
						  <th>备注</th>
						  <th>帐号</th>
						   <th>今日总额</th>
						  <th>累计总额</th>
						  <th>链接</th>
						  <th>支付类型</th>
						  <th>状态</th>
						  <th>操作</th>	
						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  foreach($datas as $item){	
							$sql_size = " SELECT sum(amount) AS total FROM ".DB_PREFIX."order  where datediff(FROM_UNIXTIME(ordertime,'%Y-%m-%d'),now()) = 0 and state=1 and wechatid = '".$item['wechatid']."'";
							$size = $database->query($sql_size)->fetchAll();
							$todayMoney = $size[0]["total"] > 0 ? $size[0]["total"] : 0;
							
							$sql_size = " SELECT sum(amount) AS total FROM ".DB_PREFIX."order where state=1 and wechatid = '".$item['wechatid']."'";
							$size = $database->query($sql_size)->fetchAll();
							$totalMoney = $size[0]["total"] >0 ? $size[0]["total"] :  0;
					  ?>
					  <tr>	
						   <td style="text-align: center;"><?php echo $item['desc'];?></td>
						  <td style="text-align: center;"><?php echo $item['wechatid'];?></td>
						   <td	style="text-align: center;"><?php echo $todayMoney;?></td>
						  <td	style="text-align: center;"><?php echo $totalMoney;?></td>
						  <td style="text-align: center;"><?php echo $item['urls'];?></td>
						  <td><?php  
						   if($item['iswx']==1) echo " 微信 ";
						   if($item['isali']==1) echo " 支付宝 ";
						  ?></td>	
						  <td style="text-align: center;"><?php if($item['isopen']==1) echo "<span style='color:red'>开启</span>";else echo "关闭";?></td>						 	  
						  <td style="text-align: center;"><a href="solidsupp.php?action=up&id=<?php echo $item['id'];?>&state=<?php if($item['isopen']==1) echo "0";else echo "1";?>"><?php if($item['isopen']==1) echo "关闭";else echo "开启";?></a>&nbsp;|&nbsp;<a title='编辑' href="solid_up.php?action=edit&id=<?php echo $item['id'];?>" >编辑</a>&nbsp;|&nbsp;<a title='删除' onclick="confirmAction('?action=delete&id=<?php echo $item['id'];?>')" href='javascript:;' >删除</a></td>
					  </tr>
					  <?php
					  }
					  ?>
						
						
					  </tbody>
					</table>

				<?php 
				echo bootpage($record,$pageSize,$pageNumber,"","");
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

<!-- page end -->

<script type="text/javascript">
$(function(){
	$("#accounts-menu").addClass('in');
})
</script>

<?php include 'foot.php';?>
