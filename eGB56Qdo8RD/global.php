<?php
include '../init.php';
 
$appSet['company']  = '';
$appSet['company_year'] = '2017';
$appSet['company_url'] = 'http://www.dd.com/';
$appSet['app_name'] = '订单管理系统';

$sysSet = $database->get(DB_PREFIX.'set','*',array('id'=>1));




$pageSize = ($sysSet['page_size'] > 0) ? $sysSet['page_size'] : 10;


@$username=$_SESSION['card_admin']['username'];
@$password=$_SESSION['card_admin']['password'];
//var_dump($_SESSION);
$r = $database->get(DB_PREFIX."manage","*",array('AND'=>array('username'=>$username,'password'=>$password)));
if(empty($r)){
	unset($_SESSION['card_admin']);
	echo '<script>window.location.href="login.php";</script>';
	return;	
}
if(isset($_GET['go'])){
	if($_GET['go']=='logout'){
		$_SESSION['card_admin'] = null;
		unset($_SESSION['card_admin']);
		echo '<script>window.location.href="login.php";</script>';
		return;
	}
}
?>