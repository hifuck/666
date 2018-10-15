<?php 
include 'global.php';

if(isset($_GET['action'])){
	if($_GET['action'] == "delete"){
		$id = $_GET['id'];
		if(!empty($id)){
		  $info = $database->get(DB_PREFIX.'sys','*',array('id'=>$id));
		  if($info['sys_level'] == 1){
			  message('温馨提示',"无法删除管理员帐号",'sys_user.php');
			  return;
		  }
		  $database->delete(DB_PREFIX."sys",array('id'=>$id));
		  header("Location:sys_user.php");
		}
	}
}

?>
<?php include 'base.php';?>
<?php 
$sql_where = " WHERE level = 2 ";
$sql_order = " ORDER BY id DESC ";
$pageNumber = isset($_GET['pageNo']) ? $_GET['pageNo'] : 1;
$keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';
if(!empty($keywords)){
	$sql_where .= " and username like '%".$keywords."%'  ";
}
$sql_limit = " limit ".($pageNumber-1)*$pageSize.",".$pageSize." ";

$sql = "SELECT * FROM ".DB_PREFIX."manage $sql_where $sql_order  $sql_limit ";
$sql_size = " SELECT COUNT(*) AS total FROM ".DB_PREFIX."manage a $sql_where ";
$size = $database->query($sql_size)->fetchAll();
$record = $size[0]["total"];
$datas = $database->query($sql)->fetchAll();

function getUserType($type){
	if($type == '1'){
		return '系统管理员';
	}
	if($type == '2'){
		return '操作员';
	}
}

?>

<!-- page start -->
<div class="content">
    <div class="header">        
        <h1 class="page-title">用户管理</h1>
    </div>
    <ul class="breadcrumb">
        <li><a href="index.php">主页</a>  <span class="divider">/</span>
        </li>
        <li class="active">用户管理</li>
    </ul>
    <div class="container-fluid">
        <div class="row-fluid">
              <div class="btn-toolbar" style="height:30px;">
					<button onclick="window.location.href='manage_edit.php'" class="btn btn-primary"><i class="icon-plus"></i> 新增操作员</button>
					<form action="sys_user_list.php" method="post" class="form-search pull-right">
					  <input type="text" name="keywords"  placeholder="输入用户名" class="input-xlarge search-query">
					  <button type="submit" class="btn">搜索</button>
					</form>
				</div>				
				
					<table class="table table-hover table-bordered">
					  <thead>
						<tr>
						  <th>#编号</th>
						  <th>用户名</th>
						  <th>用户类型</th>
						  <th>用户昵称</th>	
						  <th>上次登录</th>	
						  <th>登录IP</th>						  
						  <th>操作</th>
						</tr>
					  </thead>
					  <tbody>
					  <?php 
					  foreach($datas as $item){						  
					  ?>
					  <tr>
						  <td	style="text-align: center;"><?php echo $item['id'];?></td>
						  <td	style="text-align: center;"><?php echo $item['username'];?></td>
						  <td	style="text-align: center;"><?php echo getUserType($item['level']);?></td>
						  <td	style="text-align: center;"><?php echo $item['nickname'];?></td>
						  <td	style="text-align: center;"><?php echo $item['lastlogin'];?></td>
						  <td	style="text-align: center;"><?php echo $item['lastip'];?></td>						  
						  <td	style="text-align: center;"><a title='编辑' href="manage_edit.php?action=edit&id=<?php echo $item['id'];?>">编辑</a>&nbsp;|&nbsp;<a title='删除' onclick="confirmAction('?action=delete&id=<?php echo $item['id'];?>')" href='javascript:;' >删除</a></td>
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
	$("#legal-menu").addClass('in');
})
</script>

<?php include 'foot.php';?>
