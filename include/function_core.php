<?php
function notifynotice($account,$amount,$orderno,$tradeno,$md5key){
	$tradestatus="success";
	$actionType="outreachpay";
	$src="account=$account&amount=$amount&orderno=$orderno&tradeno=$tradeno&tradestatus=$tradestatus&$md5key";
	$srcs=urlencode($src);
	$m = new Xcrypt($keys, 'cbc',$keys); 
	$b=$m->encrypt($srcs,'hex');
	$c=$m->decrypt($b,'hex');
	$sign=md5(strtoupper($b));
	$post_string = "tradestatus=".$tradestatus."&account=$account&orderno=".$orderno."&amount=".$amount."&tradeno=".$tradeno."&actionType=".$actionType."&sign=".$sign;
	$data=request_curl($notifyurl, $post_string);
	return $data; 
}

function get_IP() {  
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  
			//如果变量是非空或非零的值，则 empty()返回 false。  
				$ip = explode(',',$_SERVER['HTTP_CLIENT_IP']);  
			}  
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
				$ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);  
			}  
			elseif (!empty($_SERVER['REMOTE_ADDR'])) {  
				$ip = explode(',',$_SERVER['REMOTE_ADDR']);  
			}  
			else {  
				$ip[0] = 'none';  
			}  
			return $ip[0];  
		}

function getAmt($amt,$re)
{
	$re = $re + 1;
	$m = $re / 2;
	$n = $re % 2;
	$b = (int)$m / 100;
	if($n == '0')
	{
		$amount = $amt - $b;
		
	}else
	{
		$amount = $amt + $b;
	}
	return $amount;
}
function getReal($amt,$gtime,$database)
{
	for($n=0;$n<9;$n++)
	{
		$realamount = getAmt($amt,$n);
		$sql = "select COUNT(*) AS total from ".DB_PREFIX."order where state = 0 and amount = '".number_format($amt, 2, ".", "")."' and realamount = '".number_format($realamount, 2, ".", "")."' and ordertime > '".$gtime."' ORDER BY id DESC";
		$row = $database->query($sql)->fetchAll();
		$re = $row[0]['total'];
		if($re == 0)
		{
			return $realamount;
		}
	}
	return $realamount = '';
}	


function no($orderno,$amount,$tradeno,$md5key,$keys,$database)
{
	$md5key = '87654328';
	
	$info = $database->get(DB_PREFIX . 'logorder', '*', array('orderid' => $orderno));
	
	if($info['ordertype'] == 'wj')
	{
		wjno($orderno,$amount,$tradeno,$md5key,$keys,$database);
	}
	elseif($info['ordertype'] == 'pt')
	{
		ptno($orderno,$amount,$tradeno,$md5key,$keys,$database);
	}
	
	
}

function wjno($orderno,$amount,$tradeno,$md5key,$keys,$database)
{
		$info = $database->get(DB_PREFIX . 'wjorder', '*', array('AND' => array('orderid' => $orderno, 'amount' => number_format($amount, 2, ".", ""))));
		$notifyurl=$info['notifyurl'];
		$account=$info['account'];
		if ($info) {
			$upArr = array('state' => '1','tradeno'=>$tradeno);
			$database->update(DB_PREFIX . 'wjorder', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '0', 'amount' => number_format($amount, 2, ".", ""))));
				
			$upnotify = array('tradeno' => $tradeno,'notifycount'=>1);
			$database->update(DB_PREFIX . 'wjordernotify', $upnotify, array('AND' => array('orderid' => $orderno)));
		}
		$tradestatus="success";
		$actionType="outreachpay";
		$src="account=$account&amount=$amount&orderno=$orderno&tradeno=$tradeno&tradestatus=$tradestatus&$md5key";
		$srcs=urlencode($src);
		$m = new Xcrypt($keys, 'cbc',$keys); 
		$b=$m->encrypt($srcs,'hex');
		$c=$m->decrypt($b,'hex');
		$sign=md5(strtoupper($b));
		$post_string = "tradestatus=".$tradestatus."&account=$account&orderno=".$orderno."&amount=".$amount."&tradeno=".$tradeno."&actionType=".$actionType."&sign=".$sign;
		$data=request_curl($notifyurl, $post_string);		
		if($data=="success"){
			$upArr = array('notifystatus' => '1');
			$database->update(DB_PREFIX . 'wjorder', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '1', 'amount' => number_format($amount, 2, ".", ""))));

			$upnotify = array('status' => 1,'notifycount'=>1,'context'=>$data);
			$database->update(DB_PREFIX . 'wjordernotify', $upnotify, array('AND' => array('orderid' => $orderno)));
		}
	
}

function ptno($orderno,$amount,$tradeno,$md5key,$keys,$database)
{
		$info = $database->get(DB_PREFIX . 'order', '*', array('AND' => array('orderid' => $orderno, 'amount' => number_format($amount, 2, ".", ""))));
		$notifyurl=$info['notifyurl'];
		$account=$info['account'];
		if ($info) {
			$upArr = array('state' => '1','tradeno'=>$tradeno);
			$database->update(DB_PREFIX . 'order', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '0', 'amount' => number_format($amount, 2, ".", ""))));
				
			$upnotify = array('tradeno' => $tradeno,'notifycount'=>1);
			$database->update(DB_PREFIX . 'ordernotify', $upnotify, array('AND' => array('orderid' => $orderno)));
		}
		$tradestatus="success";
		$actionType="outreachpay";
		
		$src="account=$account&amount=$amount&orderno=$orderno&tradeno=$tradeno&tradestatus=$tradestatus&$md5key";
		$srcs=urlencode($src);	
		$m = new Xcrypt($keys, 'cbc',$keys); 
		$b=$m->encrypt($srcs,'hex');
		$c=$m->decrypt($b,'hex');
		$sign=md5(strtoupper($b));
		$post_string = "tradestatus=".$tradestatus."&account=$account&orderno=".$orderno."&amount=".$amount."&tradeno=".$tradeno."&actionType=".$actionType."&sign=".$sign;
		
		$data=request_curl($notifyurl, $post_string);		
		if($data=="success"){
			$upArr = array('notifystatus' => '1');
			$database->update(DB_PREFIX . 'order', $upArr, array('AND' => array('orderid' => $orderno, 'state' => '1', 'amount' => number_format($amount, 2, ".", ""))));

			$upnotify = array('status' => 1,'notifycount'=>1,'context'=>$data);
			$database->update(DB_PREFIX . 'ordernotify', $upnotify, array('AND' => array('orderid' => $orderno)));
		}
	
}





function  log_result($file,$word){
	    $fp = fopen($file,"a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"执行日期：".date("Y-m-d h:i:s")."\n".$word."\n\n");
	    flock($fp, LOCK_UN);
	    fclose($fp);
}

function request_curl($notifyurl, $post_string){  
  $ch = curl_init();  
  curl_setopt($ch, CURLOPT_URL, $notifyurl);  
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
  curl_setopt($ch, CURLOPT_USERAGENT, "NewpaySoft beta");  
  $data = curl_exec($ch);  
  curl_close($ch);  
  return $data;  
}

//addslashes扩展函数, 可以过滤数组
function daddslashes($string, $force = 1) {
	if(is_array($string)) {
		$keys = array_keys($string);
		foreach($keys as $key) {
			$val = $string[$key];
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val, $force);
		}
	} else {
		$string = addslashes(trim($string));
	}
	return $string;
}

//stripslashes扩展; 支持数组  删除由 addslashes() 函数添加的反斜杠。
function dstripslashes($string) {
	if(empty($string)) return $string;
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}

	return $string;
}

//htmlspecialchars扩展函数, 安全增强, 可以过滤数组
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), trim($string));
		if(strpos($string, '&amp;#') !== false) {
			$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
		}
	}
	return $string;
}

//提取文件后缀名
function fileext($filename) {
	return addslashes(trim(substr(strrchr($filename, '.'), 1, 10)));
}

//implode扩展; 把数组转成 字符串数组
function dimplode($array) {
	if(!empty($array)) {
		return "'".implode("','", is_array($array) ? $array : array($array))."'";
	} else {
		return 0;
	}
}

/*
 Utf-8、gb2312都支持的汉字截取函数
 cut_str(字符串, 截取长度, 开始长度, 编码);
 编码默认为 utf-8
 开始长度默认为 0
*/
function cutStr($string, $sublen, $start = 0, $code = 'UTF-8'){
    if($code == 'UTF-8'){
        $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
        preg_match_all($pa, $string, $t_string);

        if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen))."...";
        return join('', array_slice($t_string[0], $start, $sublen));
    }else{
        $start = $start*2;
        $sublen = $sublen*2;
        $strlen = strlen($string);
        $tmpstr = '';

        for($i=0; $i<$strlen; $i++){
            if($i>=$start && $i<($start+$sublen)){
                if(ord(substr($string, $i, 1))>129){
                    $tmpstr.= substr($string, $i, 2);
                }else{
                    $tmpstr.= substr($string, $i, 1);
                }
            }
            if(ord(substr($string, $i, 1))>129) $i++;
        }
        if(strlen($tmpstr)<$strlen ) $tmpstr.= "...";
        return $tmpstr;
    }
}


//跳转地址
function goToUrl($url = '', $extra_type = 0) {
	if($url) {
		if($extra_type == '301') {
			header("HTTP/1.1 301 Moved Permanently");
			header("location: ". str_replace('&amp;', '&', $url));
		} else {
			echo '<script type="text/javascript" reload="1">if(top.location != this.location){top.location.replace(\''. str_replace("'", "\'", $url). '\');}window.location.href=\''. str_replace("'", "\'", $url). '\';</script>';
		}
	}
	exit();
}

//获取当前页面的url
function curPageURL() {
	$pageURL = 'http';
	//if (!empty($_SERVER['HTTPS'])) {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

//页面提示跳转
function message($msgTitle,$message,$jumpUrl){
	$str = '<!DOCTYPE HTML>';
	$str .= '<html>';
	$str .= '<head>';
	$str .= '<meta charset="utf-8">';
	$str .= '<title>页面提示</title>';
	$str .= '<style type="text/css">';
	$str .= '*{margin:0; padding:0}a{color:#369; text-decoration:none;}a:hover{text-decoration:underline}body{height:100%; font:12px/18px Tahoma, Arial,  sans-serif; color:#424242; background:#fff}.message{width:450px; height:120px; margin:16% auto; border:1px solid #99b1c4; background:#ecf7fb}.message h3{height:28px; line-height:28px; background:#2c91c6; text-align:center; color:#fff; font-size:14px}.msg_txt{padding:10px; margin-top:8px}.msg_txt h4{line-height:26px; font-size:14px}.msg_txt h4.red{color:#f30}.msg_txt p{line-height:22px}';
	$str .= '</style>';
	$str .= '</head>';
	$str .= '<body>';
	$str .= '<div class="message">';
	$str .= '<h3>'.$msgTitle.'</h3>';
	$str .= '<div class="msg_txt">';
	$str .= '<h4 class="red">'.$message.'</h4>';
	$str .= '<p>页面将在 <span style="color:blue;font-weight:bold">3</span> 秒后自动关闭!</p>';
	$str .= "<script>setTimeout('location.replace(\'".$jumpUrl."\')',3000)</script>";
	$str .= '</div>';
	$str .= '</div>';
	$str .= '</body>';
	$str .= '</html>';
	echo $str;
}

//页面提示
function message_nojump($msgTitle,$message){
	$str = '<!DOCTYPE HTML>';
	$str .= '<html>';
	$str .= '<head>';
	$str .= '<meta charset="utf-8">';
	$str .= '<title>页面提示</title>';
	$str .= '<style type="text/css">';
	$str .= '*{margin:0; padding:0}a{color:#369; text-decoration:none;}a:hover{text-decoration:underline}body{height:100%; font:12px/18px Tahoma, Arial,  sans-serif; color:#424242; background:#fff}.message{width:450px; height:120px; margin:16% auto; border:1px solid #99b1c4; background:#ecf7fb}.message h3{height:28px; line-height:28px; background:#2c91c6; text-align:center; color:#fff; font-size:14px}.msg_txt{padding:10px; margin-top:8px}.msg_txt h4{line-height:26px; font-size:14px}.msg_txt h4.red{color:#f30}.msg_txt p{line-height:22px}';
	$str .= '</style>';
	$str .= '</head>';
	$str .= '<body>';
	$str .= '<div class="message">';
	$str .= '<h3>'.$msgTitle.'</h3>';
	$str .= '<div class="msg_txt">';
	$str .= '<h4 class="red">'.$message.'</h4>';
	$str .= '</div>';
	$str .= '</div>';
	$str .= '</body>';
	$str .= '</html>';
	echo $str;
}



//翻页方法
function page($num, $perpage, $curpage, $mpurl, $rewrite = 0, $maxpages = 0, $page = 10) {
	$a_name = ''; $url_format = 'pageNo=';
	if(strpos($mpurl, '#') !== FALSE) {
		$a_strs = explode('#', $mpurl);
		$mpurl  = $a_strs[0];
		$a_name = '#'. $a_strs[1];
	}

	$lang['prev']  = "上一页";
	$lang['next']  = "下一页";
	$lang['first'] = "首页";
	$lang['end']   = "尾页";

	$multipage = '';

	if(!$rewrite) {
		$mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';
	} else {
		$url_format = '/';
	}

	$realpages = 1;
	$page -= strlen($curpage) - 1;

	$page <= 0 && $page = 1;

	if($num > $perpage) {

		$offset = floor($page * 0.5);

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to   = $pages;
		} else {
			$from = $curpage - $offset;
			$to   = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to   = $pages;
			}
		}
		
		$multipage = ($curpage - $offset > 1 && $pages > $page ? "<a href=\"{$mpurl}{$url_format}1{$a_name}\" class=\"first\">{$lang['first']}</a>" : '').
		($curpage > 1 ? '<a href="'.$mpurl.$url_format.($curpage - 1).$a_name.'" class="prev">'.$lang['prev'].'</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? '<strong>'.$i.'</strong>' :
			'<a href="'.$mpurl.$url_format.$i.($i == $pages ? '#' : $a_name).'">'.$i.'</a>';
		}
		$multipage .= ($curpage < $pages ? '<a href="'.$mpurl.$url_format.($curpage + 1).$a_name.'" class="next">'.$lang['next'].'</a>' : ''). 
					  ($to < $pages ? '<a href="'.$mpurl.$url_format.$pages.$a_name.'" class="last">'.$lang['end'].'</a>' : '');
		$multipage = $multipage ? '<div class="pages" style="text-align:center;" >'.$multipage.'</div>' : '';
	}
	
	$maxpage = $realpages;

	return $multipage;
}

//附带查询的翻页
function bootpage($num, $perpage, $curpage, $mpurl,$a_name, $tongji = '', $rewrite = 0, $maxpages = 0, $page = 10) {
    $url_format = 'pageNo=';
	

	$lang['prev']  = "上一页";
	$lang['next']  = "下一页";
	$lang['first'] = "首页";
	$lang['end']   = "尾页";

	$multipage = '';

	if(!$rewrite) {
		$mpurl .= strpos($mpurl, '?') !== FALSE ? '&amp;' : '?';
	} else {
		$url_format = '/';
	}

	$realpages = 1;
	$page -= strlen($curpage) - 1;

	$page <= 0 && $page = 1;

	if($num > $perpage) {

		$offset = floor($page * 0.5);

		$realpages = @ceil($num / $perpage);
		$pages = $maxpages && $maxpages < $realpages ? $maxpages : $realpages;

		if($page > $pages) {
			$from = 1;
			$to   = $pages;
		} else {
			$from = $curpage - $offset;
			$to   = $from + $page - 1;
			if($from < 1) {
				$to = $curpage + 1 - $from;
				$from = 1;
				if($to - $from < $page) {
					$to = $page;
				}
			} elseif($to > $pages) {
				$from = $pages - $page + 1;
				$to   = $pages;
			}
		}
		
		$multipage = ($curpage - $offset > 1 && $pages > $page ? "<li><a href=\"{$mpurl}{$url_format}1{$a_name}\" class=\"first\">{$lang['first']}</a></li>" : '').
		($curpage > 1 ? '<li><a href="'.$mpurl.$url_format.($curpage - 1).$a_name.'" class="prev">'.$lang['prev'].'</a>' : '');
		for($i = $from; $i <= $to; $i++) {
			$multipage .= $i == $curpage ? ' <li class="active"><a href="javascript:;">'.$i.'</a></li>' :
			'<li><a href="'.$mpurl.$url_format.$i. $a_name.'">'.$i.'</a></li>';
		}
		$multipage .= ($curpage < $pages ? '<li><a href="'.$mpurl.$url_format.($curpage + 1).$a_name.'" class="next">'.$lang['next'].'</a></li>' : ''). 
					  ($to < $pages ? '<li><a href="'.$mpurl.$url_format.$pages.$a_name.'" class="last">'.$lang['end'].'</a></li>' : '');
		
	}
	
	$multipage = '<div class="pagination" ><ul>'.$multipage.'<li><a href="javascript:;">总记录：'.$num.'</a></li>'.$tongji.'</ul></div>';
	
	$maxpage = $realpages;

	return $multipage;
}

	//把字节(根据1024进一规则)自动转换为(GB、MB、KB、Bytes)
	function sizecount($size) {
		if($size >= 1073741824) {
			$size = round($size / 1073741824 * 100) / 100 . ' GB';
		} elseif($size >= 1048576) {
			$size = round($size / 1048576 * 100) / 100 . ' MB';
		} elseif($size >= 1024) {
			$size = round($size / 1024 * 100) / 100 . ' KB';
		} else {
			$size = $size . ' Bytes';
		}

		return $size;
	}
	/**
	* 设置时区
	* @param $timeoffset - 时区数值
	* @return 无
	*/
	function timezone_set($timeoffset = 0) {
		if(function_exists('date_default_timezone_set')) {
			date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
		}
	}
	
	// 获取客户端IP
	function get_client_ip() {
		$ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			foreach ($matches[0] AS $xip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
					$ip = $xip;
					break;
				}
			}
		}

		return $ip;
	}
	
	//读取URL内容
	function get_url_contents($url){
		$result = "";
		if(function_exists('file_get_contents')) {
			$result = file_get_contents($url);
		}else{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $url);
			$result =  curl_exec($ch);
			curl_close($ch);			
		}
		return $result;
	}
	
	//检查文件或者目录是否可写
	function file_info($file){
		if (DIRECTORY_SEPARATOR == '/' and @ini_get("safe_mode") == FALSE){
			return is_writable($file);
		}
		if (is_dir($file)){
			$file = rtrim($file, '/').'/is_writable.html';
			if (($fp = @fopen($file,'w+')) === FALSE){
				return FALSE;
			}
			fclose($fp);
			@chmod($file,0755);
			@unlink($file);
			return TRUE;
		}else if ( ! is_file($file) or ($fp = @fopen($file, 'r+')) === FALSE){
			return FALSE;
		}
		fclose($fp);
		return TRUE;
	}
	
	//php利用新浪ip查询接口获取ip地理位置
	function getiploc_sina($queryip){      
		   $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryip;      
		   $ch = curl_init($url);       
		   curl_setopt($ch,CURLOPT_ENCODING ,'utf8');       
		   curl_setopt($ch, CURLOPT_TIMEOUT , 5);     
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回    
		   $location = curl_exec($ch);      
		   return $location;  
	}
	
	
	
	/** 
	 * 获取访客信息的类：语言、浏览器、操作系统、ip、地理位置、isp。 
	 * 使用： 
	 *   $obj = new guest_info; 
	 *   $obj->getlang();     //获取访客语言：简体中文、繁體中文、english。 
	 *   $obj->getbrowser();  //获取访客浏览器：msie、firefox、chrome、safari、opera、other。 
	 *   $obj->getos();       //获取访客操作系统：windows、mac、linux、unix、bsd、other。 
	 *   $obj->getip();       //获取访客ip地址。 
	 *   $obj->getiploc_sina();      //获取访客地理位置，使用 新浪接口。 
	 */  
	class guest_info{  
		function getlang() {  
			$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 4);  
			//使用substr()截取字符串，从 0 位开始，截取4个字符  
			if (preg_match('/zh-c/i',$lang)) {  
			//preg_match()正则表达式匹配函数  
				$lang = '简体中文';  
			}
			elseif (preg_match('/zh/i',$lang)) {  
				$lang = '繁體中文';  
			}  
			else {  
				$lang = 'english';  
			}  
			return $lang;  
		}  
		function getbrowser() {  
			$browser = $_SERVER['HTTP_USER_AGENT'];  
			if (preg_match('/msie/i',$browser)) {  
				$browser = 'msie';  
			}  
			elseif (preg_match('/firefox/i',$browser)) {  
				$browser = 'firefox';  
			}  
			elseif (preg_match('/chrome/i',$browser)) {  
				$browser = 'chrome';  
			}  
			elseif (preg_match('/safari/i',$browser)) {  
				$browser = 'safari';  
			}  
			elseif (preg_match('/opera/i',$browser)) {  
				$browser = 'opera';  
			}  
			else {  
				$browser = 'other';  
			}  
			return $browser;  
		}  
		function getos() {  
			$os = $_SERVER['HTTP_USER_AGENT'];  
			if (preg_match('/win/i',$os)) {  
				$os = 'windows';  
			}  
			elseif (preg_match('/mac/i',$os)) {  
				$os = 'mac';  
			}  
			elseif (preg_match('/linux/i',$os)) {  
				$os = 'linux';  
			}  
			elseif (preg_match('/unix/i',$os)) {  
				$os = 'unix';  
			}  
			elseif (preg_match('/bsd/i',$os)) {  
				$os = 'bsd';  
			}  
			else {  
				$os = 'other';  
			}  
			return $os;  
		}  
		function getip() {  
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {  
			//如果变量是非空或非零的值，则 empty()返回 false。  
				$ip = explode(',',$_SERVER['HTTP_CLIENT_IP']);  
			}  
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
				$ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);  
			}  
			elseif (!empty($_SERVER['REMOTE_ADDR'])) {  
				$ip = explode(',',$_SERVER['REMOTE_ADDR']);  
			}  
			else {  
				$ip[0] = 'none';  
			}  
			return $ip[0];  
		}
		
	}
	
?>
