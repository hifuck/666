<?php
header('Content-Type:text/html;Charset=utf-8;'); 
require_once("init.php");
error_reporting(E_ALL); 
ini_set('display_errors', '1'); 
#{"orderid":"80092756177","amount":"150","sign":"4c59c238877f86d5abaa66686b72e7ee"}

// $_REQUEST = json_decode('{"userid":"13067473645","orderid":"80091516251","amount":"100","sign":"ba45b8279a3ee0b4239baa7a6d86774b"}',true);

file_put_contents('sysadslog/'.date('Ymd').'.txt',date('Y-m-d H:i:s').':'.json_encode($_REQUEST)."\r\n",FILE_APPEND);
$mKey = 'UlPm9yYho12J65k';
@$userid = $_REQUEST['userid'];
@$amount = $_REQUEST['amount'];
@$outTradeNo = $_REQUEST['orderid'];
@$paytype	= $_REQUEST['paytype'];
@$sign = $_REQUEST['sign'];

$resign = md5($userid.$outTradeNo.$amount.$paytype.$mKey);
if($sign != $resign)
{
	file_put_contents('sysadslog/'.date('Ymd').'.txt','sign error'."\r\n",FILE_APPEND);
	die();
}

switch($paytype)
{
	case 'wxpay':
		$ptype = 1;
		break;
	case 'alipay':
		$ptype = 2;
		break;	
	default;
		file_put_contents('sysadslog/'.date('Ymd').'.txt','paytype error'."\r\n",FILE_APPEND);
		die();
}

if($amount)
{
	$sql = "select COUNT(*) AS traderow from ".DB_PREFIX."order where  tradeno = '".$outTradeNo."' and paytype = $ptype ORDER BY id DESC";
	
	$trow = $database->query($sql)->fetchAll();
	$tre = $trow[0]['traderow'];
	if($tre > 0)
	{
		file_put_contents('sysadslog/'.date('Ymd').'.txt','tradeno repeat'."\r\n",FILE_APPEND);
		die();
	}
	$gtime = time() - 300;
	$sql = "select COUNT(*) AS total from ".DB_PREFIX."order where state = 0  and wechatid = '".$userid."' and  realamount = '".number_format($amount, 2, ".", "")."' and ordertime > '".$gtime."' and paytype = $ptype ORDER BY id DESC";
	$row = $database->query($sql)->fetchAll();
	$re = $row[0]['total'];
	if($re == 1)
	{
		$sql = "select * from ".DB_PREFIX."order where state = 0 and wechatid = '".$userid."' and  realamount = '".number_format($amount, 2, ".", "")."' and ordertime > '".$gtime."' and paytype = $ptype ORDER BY id DESC";	
		$data = $database->query($sql)->fetchAll();
		
		$orderno	=	$data[0]['orderid'];
		
		$upArr = array('state' => '1','tradeno' => $outTradeNo,'systime'=>time());

		$database->update(DB_PREFIX . 'order', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '0', 'realamount' => number_format($amount, 2, ".", ""))));


		///支付成功后修改pay_info表里的付款状态
        $updateState = updateState($database,$amount,$userid);
        /////////////=================/////
        //大发平台接口
        if($updateState==1){
            $json_url       = 'https://pay.dafa-api.com/Pay/Index';
            $MerchantId     = '20181011180656738D1D0655F3BB94D9';
            $MemberId       = $userid;
            $Amount         = $amount;
            $ClientIP       = '43.229.153.150';
            $SourceName     = 'PC';
            $secretKey      = 'ba9640e5595d429d849d95636f4433d1';
            $Sign           = md5('{MerchantId='.$MerchantId.'&MemberId='.$MemberId.'&Amount='.$Amount.'&OrderId='.$outTradeNo.'&ClientIP='.$ClientIP.'&SourceName='.$SourceName.'}'.$secretKey);
            $param = array(
                'MerchantId'    => $MerchantId,
                'MemberId'      => $MemberId,
                'Amount'        => $Amount,
                'OrderId'       => $outTradeNo,
                'ClientIP'      => $ClientIP,
                'SourceName'    => $SourceName,
                'sign'          => $Sign
            );
            $res =  curlRequest($json_url,$param,true);
            $arr = json_decode($res,true);
            $code = $arr['RespCode'];
            if($code=='SUCCESS'){
                $upArr1 = array('InState' => 1);
                $database->update('pay_info', $upArr1, array('AND' => array('Amount' => $amount, 'UserId' => $userid,'Instate' => '0', 'PayState' => '1')));
            }
        }
        /////////////=================////
		$notifyurl	=	$data[0]['notifyurl'];
		$redata = array(
			'merId'	=> $merid,
			'orderno' => $orderno,
			'account' => $data[0]['account'],
			'amount' => $data[0]['amount'],
			'realamount' => $data[0]['realamount'],
			
		);
		$redata['sign'] = md5(urldecode(http_build_query($redata)).'&key='.$mer_key);
		$res = '';
		$x = 1;
		do{
			if($x == 2)sleep(5);
			if($x == 3)sleep(15);
			if($x == 4)sleep(30);
			if($x == 5)sleep(60);
			$res=request_curl($notifyurl, http_build_query($redata));
			file_put_contents('sysadslog/no.txt',date('Y-m-d H:i:s').'发送数据:'.http_build_query($redata)."\r\n",FILE_APPEND);
			file_put_contents('sysadslog/no.txt',date('Y-m-d H:i:s').'回调地址:'.$notifyurl."\r\n",FILE_APPEND);
			file_put_contents('sysadslog/no.txt',date('Y-m-d H:i:s').'响应数据:'.$res."\r\n",FILE_APPEND);
			$x++;
			if($res=='success'){
				$upArr = array('notifystatus' => '1');
				$database->update(DB_PREFIX . 'order', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '1', 'realamount' => number_format($amount, 2, ".", ""))));
                //支付成功后修改pay_info表里的付款状态
                $upArr1 = array('InState' => '1','InId' => 1);
                $database->update('pay_info', $upArr1, array('AND' => array('Amount' => $amount, 'UserId' => $userid,'Instate' => '0', 'InId' => '0')));
				break;
			}	
			
		}while($x<=5);
				
	}
	else
	{		
		$arrInsert = array('amount'=>number_format($amount, 2, ".", ""),'ordertime'=>time(),'wechatid' => $outTradeNo,'userid' => $userid);
		$database->insert(DB_PREFIX . 'noorder', $arrInsert);
	}
}
die('success');


?>