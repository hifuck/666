<?php
//error_reporting(0);
require_once('360_safe3.php');
define('ROOT_PATH', dirname(preg_replace('@\\(.*\\(.*$@', '', __FILE__)) . DIRECTORY_SEPARATOR);	//当前目录
define('INC_PATH', ROOT_PATH."include". DIRECTORY_SEPARATOR);			
define('ExcelLib_PATH', INC_PATH. DIRECTORY_SEPARATOR);
require INC_PATH . 'function_core.php';
require INC_PATH . 'medoo.min.php';
require INC_PATH . 'config.php';
define('MAGIC_QUOTES_GPC', PHP_VERSION < '5.3.0' && function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
if (!MAGIC_QUOTES_GPC) {
    $_GET = $_GET ? daddslashes($_GET) : array();
    $_POST = $_POST ? daddslashes($_POST) : array();
    $_COOKIE = $_COOKIE ? daddslashes($_COOKIE) : array();
    $_FILES = $_FILES ? daddslashes($_FILES) : array();
}
date_default_timezone_set('PRC');
$database = new medoo(array('database_type' => DB_TYPE, 'database_name' => DB_NAME, 'server' => DB_HOST, 'port' => DB_PORT, 'username' => DB_USER, 'password' => DB_PASSWD));
ob_start();
session_start();

if(isset($_SESSION['expiretime'])) {
    if($_SESSION['expiretime'] < time()) {
        unset($_SESSION['expiretime']);
        header('Location: logout.php?TIMEOUT'); // 登出
        exit(0);
    } else {
       echo $_SESSION['expiretime'] = time() + 1; // 刷新时间戳
    }
}

//伪造POST请求
function curlRequest($url, $postData=array(), $isPost=false){
    if (empty($url)) {
        return false;
    }
    $postData = http_build_query($postData);
    if(!$isPost){
        $url = $url.'?'.$postData;
    }
    // 初始化一个 cURL 对象
    $curl = curl_init();
    // 设置你需要抓取的URL
    curl_setopt($curl, CURLOPT_URL, $url);
    // 设置header
    curl_setopt($curl, CURLOPT_HEADER, 0);
    // 设置cURL 参数，要求结果(1保存到字符串中)还是(0输出到屏幕上)。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //https
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if($isPost){
        //post提交方式
        curl_setopt($curl, CURLOPT_POST, 1);
        //post提交的数据
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    }
    // 运行cURL，请求网页
    $html = curl_exec($curl);

    // 关闭URL请求
    curl_close($curl);
    return $html;
}

function updateState($database,$amount,$userid){
    $upArr1 = array('PayState' => '1');
    $res = $database->update('pay_info', $upArr1, array('AND' => array('Amount' => $amount, 'UserId' => $userid,'PayState' => '0')));
    return $res;
}

?>