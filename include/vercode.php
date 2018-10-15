<?php
session_start();
//监测验证码
$check_flag = false;
if (!extension_loaded('gd')){
	$check_flag = false;
}else{
	$gd_info=gd_info();
	$gd_info=substr($gd_info['GD Version'], 9, 1);
	if ((int)$gd_info<'2'){
		$check_flag = false;
	}else{
		$check_flag = true;
	}
}
if($check_flag){
$num = 4;$size = 15;$width = 0;$height = 0;
!$width && $width = $num * $size * 4 / 5 + 10;
!$height && $height = $size + 10;
$str = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVW";
$code = '';
for ($i = 0; $i < $num; $i++) {
	$code .= $str[mt_rand(0, strlen($str)-1)];
} 
$im = imagecreatetruecolor($width, $height); 
// 颜色
$back_color = imagecolorallocate($im, 235, 236, 237);
$boer_color = imagecolorallocate($im, 118, 151, 199);
$text_color = imagecolorallocate($im, mt_rand(0, 200), mt_rand(0, 120), mt_rand(0, 120)); 
// 背景
imagefilledrectangle($im, 0, 0, $width, $height, $back_color); 
// 边框
imagerectangle($im, 0, 0, $width-1, $height-1, $boer_color); 
// 干扰线
for($i = 0;$i < 5;$i++) {
	$font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	imagearc($im, mt_rand(- $width, $width), mt_rand(- $height, $height), mt_rand(30, $width * 2), mt_rand(20, $height * 2), mt_rand(0, 360), mt_rand(0, 360), $font_color);
} 
// 干扰点
for($i = 0;$i < 50;$i++) {
	$font_color = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
	imagesetpixel($im, mt_rand(0, $width), mt_rand(0, $height), $font_color);
} 

@imagefttext($im, $size , 0, 5, $size + 3, $text_color, 'vercode.ttf', $code);
$_SESSION["vframe_verify"] = md5(strtolower($code)); 
header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
header("Content-type: image/png;charset=gb2312");
imagepng($im);
imagedestroy($im);

}else{
//生成验证码图片
header("Content-type: image/png");
// 全数字
$str = "1,2,3,4,5,6,7,8,9,a,b,c,d,f,g";      //要显示的字符，可自己进行增删
$list = explode(",", $str);
$cmax = count($list) - 1;
$verifyCode = '';
for ( $i=0; $i < 5; $i++ ){
      $randnum = mt_rand(0, $cmax);
      $verifyCode .= $list[$randnum];           //取出字符，组合成为我们要的验证码字符
}
$_SESSION['vframe_verify'] =  md5(strtolower($verifyCode));        //将字符放入SESSION中
 
$im = imagecreate(58,25);    //生成图片
$black = imagecolorallocate($im, 0,0,0);     //此条及以下三条为设置的颜色
$white = imagecolorallocate($im, 255,255,255);
$gray = imagecolorallocate($im, 200,200,200);
$red = imagecolorallocate($im, 255, 0, 0);
imagefill($im,0,0,$white);     //给图片填充颜色
 
//将验证码绘入图片
imagestring($im, 5, 10, 8, $verifyCode, $black);    //将验证码写入到图片中
 
for($i=0;$i<50;$i++)  //加入干扰象素
{
     imagesetpixel($im, rand() , rand() , $black);    //加入点状干扰素
     imagesetpixel($im, rand() , rand() , $red);
     imagesetpixel($im, rand(), rand(), $gray);
     //imagearc($im, rand()p, rand()p, 20, 20, 75, 170, $black);    //加入弧线状干扰素
     //imageline($im, rand()p, rand()p, rand()p, rand()p, $red);    //加入线条状干扰素
}
imagepng($im);
imagedestroy($im);	
}
?> 