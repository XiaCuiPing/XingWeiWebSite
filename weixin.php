<?php
error_reporting(E_ALL && E_NOTICE);
$appid = 'wx681e02d69eae358d';
$appsecret = '41177be4a598c1d2bc0dc569b421b339';
$siteid = isset($_GET['siteid']) ? intval($_GET['siteid']) : 0;
if ($siteid){
	$json = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$_GET['code'].'&grant_type=authorization_code ');
	$data = json_decode($json, true);
	$openid = $data['openid'];
	
	$json = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$appsecret);
	$data = json_decode($json, true);
	$access_token = $data['access_token'];
	
	$json = file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
	$data = json_decode($json, true);
}

$data = array('a'=>1,'b'=>2,'c'=>'http://songdewei.com/?a=1&b=2');
$str = serialize($data);
$str2 = base64_encode($str);
echo $str.'<br>'.$str2.'<br>'.base64_decode($str2);