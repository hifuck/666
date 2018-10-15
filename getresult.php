<?php
header('Content-Type:text/html;Charset=utf-8;'); 
require_once("init.php");

@$BillNO=$_REQUEST['paysapi_id'];


$sql= " SELECT * FROM ".DB_PREFIX."order  WHERE `orderid`='$BillNO' ";
$querys = $database->query($sql)->fetchAll();
@$order_state = $querys[0]["state"];			//商户通道简码
@$return = $querys[0]["returnurl"];	
if($order_state=="1"){
	$data = array('msg'=>1,'code'=>1,'url'=>$return,'data'=>'success');
}
else
{
	$data = array('msg'=>-1,'code'=>-1,'url'=>'','data'=>'');
}
echo json_encode($data);
return;

?>