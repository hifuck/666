<?php
header('Content-Type:text/html;Charset=utf-8;'); 
require_once("init.php");

//@$amount=$_REQUEST["amount"];
//@$account=$_REQUEST["account"];
//@$orderno=$_REQUEST["orderno"];
//@$notifyurl=$_REQUEST["notifyurl"];
//@$returnurl=$_REQUEST["returnurl"];
//@$sign=$_REQUEST["sign"];
//@$paytype=$_REQUEST["paytype"];
$str = '{"merId":"d00910160","orderno":"20180920154527274dab","account":"1024","amount":"10.00","realamount":"10.00","sign":"888b2e6d497f7aa978a49dac51f6a3d7"}';
$arr = json_decode($str, true);
$sign = $arr['sign'];
array_pop($arr);
//if(empty($amount) || empty($account) || empty($orderno) || empty($notifyurl) || empty($returnurl) || empty($sign) || empty($paytype))die('参数错误，请检查！');
//$data = array('merId'	=> $merid,	'amount'	=> $amount,	'account'	=> $account,	'orderno'	=> $orderno,	'notifyurl'	=> $notifyurl,	'returnurl'	=> $returnurl,);
$resign = md5(urldecode(http_build_query($arr)).'&key='.$mer_key);
if($resign != $sign)die('签名错误');

switch($paytype)
{
	case 'wxpay':
		$css1 = 'ico-3';
		$L1   = 'images/logo_weixin.png';
		$title = '微信';
		$ptype = 1;
		$types = 'iswx';
		$t = 'weixin://';
		$t1 = 'weixin://scanqrcode';
		
		break;
	case 'alipay':
		$css1 = 'ico-1';
		$L1   = 'images/logo_alipay.png';
		$title = '支付宝';
		$ptype = 2;
		$types = 'isali';
		$t = 'alipay://';
		$t1 = 'alipay://scanqrcode';
		break;
	default;
		die('支付方式错误');
}


$sqls= " select * from ".DB_PREFIX."solidsupp where isopen=1 and $types = 1";
$wes = $database->query($sqls)->fetchAll();
if(empty($wes))die('没有可用通道');
$old = array();
foreach($wes as $item)
{
	$old[] = $item['wechatid'];
}

$gtime = time() - 300;
$amt = $amount / 100;
$realamount = $amt;
$sql = "select wechatid from ".DB_PREFIX."order where state = 0 and amount = '".number_format($amt, 2, ".", "")."' and realamount = '".number_format($amt, 2, ".", "")."' and ordertime > '".$gtime."' and paytype = $ptype ORDER BY id DESC";
$orderwe = $database->query($sql)->fetchAll();
$new = array();
foreach($orderwe as $v)
{
	$new[] = $v['wechatid'];
}

$weArr = array_diff($old, $new);
if(count($weArr) < 1)die('通道被占用，请稍等提交或选择其他金额提交，如：'.($amt-1).'&nbsp;或&nbsp;'.($amt+1));
$we = $weArr[array_rand($weArr,1)];
$sqls= " select urls from ".DB_PREFIX."solidsupp where wechatid = '".$we."' and  isopen=1 and $types = 1";
$urlsArr = $database->query($sqls)->fetchAll();
$urls = $urlsArr[0]['urls'];

$info = $database->get(DB_PREFIX . 'order', '*', array('orderid' => $orderno));
if($info)die('订单号重复，请重新发起支付');

$arrInsert = array('orderid'=>$orderno,'account'=>$account,'amount'=>number_format($amt, 2, ".", ""),'realamount'=>number_format($realamount, 2, ".", ""),'ordertime'=>time(),'systime'=>0,'state'=>0,'notifyurl'=>$notifyurl,'returnurl'=>$returnurl,'notifystatus'=>0,'orderip'=>get_IP(),'wechatid'=>$we,'paytype'=>$ptype);

$database->insert(DB_PREFIX . 'order', $arrInsert);

if(empty($urls))die('系统繁忙，请稍后支付');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="Content-Language" content="zh-cn">
	<meta name="apple-mobile-web-app-capable" content="no"/>
	<meta name="apple-touch-fullscreen" content="yes"/>
	<meta name="format-detection" content="telephone=no,email=no"/>
	<meta name="apple-mobile-web-app-status-bar-style" content="white">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Cache-control" content="no-cache">
	<meta http-equiv="Cache" content="no-cache">
	<meta name="viewport"
		  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?php echo $title;?>支付</title>
	<link href="css/pay1.css" rel="stylesheet" media="screen">
	<script type="text/javascript" src="https://cdn.staticfile.org/jquery/1.11.1/jquery.min.js"></script>
</head>

<body>
<div class="body">
	<h1 class="mod-title">
		<span class="ico_log <?php echo $css1?>"></span>
	</h1>

	<div class="mod-ct">
		<div class="order">
		</div>
		<div class="amount" id="money">￥<?php echo $realamount;?></div>
		<div class ="paybtn" style = "display: none;"><a href="<?php echo $urls;?>" id="alipaybtn" class="btn btn-primary" target="_blank">启动支付宝App支付</a></div>
		<div class="qrcode-img-wrapper" data-role="qrPayImgWrapper">
			<div data-role="qrPayImg" class="qrcode-img-area">
				<div class="ui-loading qrcode-loading" data-role="qrPayImgLoading" style="display: none;"></div>
				<div style="position: relative;display: inline-block;">
					<img  id="show_qrcode" width="300" height="235" style="display: block;">
					<img onclick="$('#use').hide()" id="use" src="<?php echo $L1?>"
						 style="position: absolute;top: 50%;left: 50%;width:32px;height:32px;margin-left: -16px;margin-top: -30px">
					
					<div id="qrcode" style = "display: none;"></div>
					<canvas id="imgCanvas" width="310" height="295" style = "display: none;"></canvas>
				</div>
			</div>


		</div>
		<div class ="payweixinbtn" style = "display: none;"><a href="https://pan.baidu.com/share/qrcode?w=210&h=210&url=<?php echo $urls;?>" download id="downloadbtn" class="btn btn-primary">1.先保存二维码到手机</a></div>
		
		<div class ="payweixinbtn" style = "display: none;padding-top: 10px"><a href="<?php echo $t;?>" class="btn btn-primary">2.打开微信，扫一扫本地图片</a></div>
		
		<div class ="iospayweixinbtn" style = "display: none;">1.长按上面的图片然后"存储图像"</div>
		<div class ="iospayweixinbtn" style = "display: none;padding-top: 10px"><a href="<?php echo $t1;?>" class="btn btn-primary">2.打开<?php echo $title;?>，扫一扫本地图片</a></div>

		
		<div class="time-item" style = "padding-top: 10px">
			<div class="time-item" id="msg"><h1>付款即时到账 未到账可联系我们</h1> </div>
						<div class="time-item"><h1>订单号:<?php echo $orderno;?></h1> </div>
						<strong id="hour_show">0时</strong>
			<strong id="minute_show">0分</strong>
			<strong id="second_show">0秒</strong>
		</div>

		<div class="tip">
			<div class="ico-scan"></div>
			<div class="tip-text">
				<p id="showtext">打开<?php echo $title;?> [扫一扫]</p>
			</div>
		</div>

		

		<div class="tip-text">
		</div>


	</div>
	<div class="foot">
		<div class="inner" style="display:none;">
			<p>手机用户可保存上方二维码到手机中</p>
			<p>在<?php echo $title;?>扫一扫中选择“相册”即可</p>
			<p></p>
		</div>
	</div>
</div>
<script src="/js/jquery.qrcode.min.js"></script>
<script type="text/javascript">    

	var myTimer;
	var strcode = '<?php echo $urls;?>';

	function timer(intDiff) {
		myTimer = window.setInterval(function () {
			var day = 0,
				hour = 0,
				minute = 0,
				second = 0;//时间默认值
			if (intDiff > 0) {
				day = Math.floor(intDiff / (60 * 60 * 24));
				hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
				minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
				second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
			}
			if (minute <= 9) minute = '0' + minute;
			if (second <= 9) second = '0' + second;
			$('#hour_show').html('<s id="h"></s>' + hour + '时');
			$('#minute_show').html('<s></s>' + minute + '分');
			$('#second_show').html('<s></s>' + second + '秒');
			if (hour <= 0 && minute <= 0 && second <= 0) {
				qrcode_timeout();
				clearInterval(myTimer);
			}
			intDiff--;
			
			if (strcode != ""){
				checkdata();
			}
			
		}, 1000);
	}

	function checkdata(){
		$.post(
			"/getresult.php",
			{
				paysapi_id : "<?php echo $orderno;?>",
			},
			function(data){
				if (data.code > 0){
					window.clearInterval(timer);
					$("#show_qrcode").attr("src","images/pay_ok.png");
					$("#use").remove();
					$("#money").text("支付成功");
					$("#msg").html("<h1>即将返回商家页</h1>");
					if (isMobile() == 1){
						$(".paybtn").html('<a href="' + data.url + '" class="btn btn-primary">返回商家页</a>');
						setTimeout(function(){
							// window.location = data.url;
							location.replace(data.url)
						}, 3000);
					}else{
						$("#msg").html("<h1>即将<a href='<?php echo $returnurl;?>'>跳转</a>回商家页</h1>");
						setTimeout(function(){
							// window.location = data.url;
							location.replace(data.url)
						}, 3000);
					}
					
				}
			}
		,'json');
	}

	function qrcode_timeout(){
		$('#show_qrcode').attr("src","images/qrcode_timeout.png");
		$("#use").hide();
		$('#msg').html("<h1>请刷新本页</h1>");
		
	}

	function isWeixin() { 
		var ua = window.navigator.userAgent.toLowerCase(); 
		if (ua.match(/MicroMessenger/i) == 'micromessenger') { 
			return 1;
		} else { 
			return 0;
		} 
	}

	function isMobile() {
		var ua = navigator.userAgent.toLowerCase();
		_long_matches = 'googlebot-mobile|android|avantgo|blackberry|blazer|elaine|hiptop|ip(hone|od)|kindle|midp|mmp|mobile|o2|opera mini|palm( os)?|pda|plucker|pocket|psp|smartphone|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce; (iemobile|ppc)|xiino|maemo|fennec';
		_long_matches = new RegExp(_long_matches);
		_short_matches = '1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-';
		_short_matches = new RegExp(_short_matches);
		if (_long_matches.test(ua)) {
			return 1;
		}
		user_agent = ua.substring(0, 4);
		if (_short_matches.test(user_agent)) {
			return 1;
		}
		return 0;
	}
	//本地生成二维码
	function showCodeImage(){
		var qrcode = $('#qrcode').qrcode({  
			text: '<?php echo $urls;?>',  
			width: 200,  
			height: 200,
		}).hide();  
		//添加文字  
		var outTime = '过期时间：<?php echo date('Y-m-d H:i:s',time()+300 )?>';//过期时间
		var canvas = qrcode.find('canvas').get(0);  
		var oldCtx = canvas.getContext('2d');  
		var imgCanvas = document.getElementById('imgCanvas');  
		var ctx = imgCanvas.getContext('2d');  
		ctx.fillStyle = 'white';  
		ctx.fillRect(0,0,310,295);  
		ctx.putImageData(oldCtx.getImageData(0, 0, 200, 200), 55, 20);  
		//ctx.stroke = 3;  
		ctx.textBaseline = 'middle';  
		ctx.textAlign = 'center';  
		ctx.font ="17px Arial";  
		ctx.fillStyle = 'red';
		ctx.strokeStyle = 'red'
		ctx.fillText(outTime, imgCanvas.width / 2, 260 );  
		ctx.strokeText(outTime, imgCanvas.width / 2, 260);  
		
		var apay = '扫码输入本订单金额：<?php echo $realamount?>元'; 
		ctx.fillText(apay, imgCanvas.width / 2, 235 );  
		ctx.strokeText(apay, imgCanvas.width / 2, 235);  
		
		var about = '金额错误和时间过期勿支付，不自动到账'; 
		ctx.fillText(about, imgCanvas.width / 2, 285 );  
		ctx.strokeText(about, imgCanvas.width / 2, 285);  

		imgCanvas.style.display = 'none';  
		$('#show_qrcode').attr('src', imgCanvas.toDataURL('image/png')).css({  
			width: 310,height:295  
		}); 
	}

	$().ready(function(){
		//如果百度图片加载失败,就在本地生成图片
		// $('#show_qrcode').error(function(){
			// showCodeImage();
		// });
		try{
			var show_expire_time = '1520405856';
			if(show_expire_time!='0'){
				showCodeImage();
			}else{
				$('#show_qrcode').attr('src', "https://pan.baidu.com/share/qrcode?w=210&h=210&url=<?php echo $urls;?>");     
			}
		} catch (e) {
			$('#show_qrcode').attr('src', "https://pan.baidu.com/share/qrcode?w=210&h=210&url=<?php echo $urls;?>"); 
		}
		
		//默认6分钟过期
		
		timer("300");
		var istype = "2";
		var suremoney = "0";
		var uaa = navigator.userAgent;
		var isiOS = !!uaa.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
		if (isMobile() == 1){
			if (isWeixin() == 1 && istype == 2){
				//微信内置浏览器+微信支付
				$("#showtext").text("长按二维码识别");
			} else{
				//其他手机浏览器+支付宝支付
				if (isWeixin() == 0 && istype == 1){
					$(".paybtn").attr('style','');
					var goPay = '<span id="goPay"> <span>';
					//给A标签中的文字添加一个能被jQuery捕获的元素
					$('#alipaybtn').append(goPay);
					//模拟点击A标签中的文字
					$('#goPay').click();

					$('#msg').html("<h1>支付完成后，请返回此页</h1>");
					$(".qrcode-img-wrapper").remove();
					$(".tip").remove();
					$(".foot").remove();                                      

					//$(location).attr('href', 'wxp://f2f1VEit2HhqwugSkOeVT9T9G6WAZXrL51Q6');
				} else {
					if (isWeixin() == 0 && istype == 2){
						//其他手机浏览器+微信支付
						//IOS的排除掉
						if (isiOS){
							// showCodeImage();

							$('.iospayweixinbtn').attr('style','padding-top: 15px;');
						}else{
							$(".payweixinbtn").attr('style','padding-top: 15px;');
						}                    
						$("#showtext").html("请保存二维码到手机<br><?php echo $title;?>扫一扫点右上角-从相册选取");
					}
				}
			}
		}
		
	});
</script>
</body>
</html>