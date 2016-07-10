<?php
/**
 * ============================================================================
 * Copyright (c) 2015 贵州大师兄信息技术有限公司 All rights reserved.
 * siteַ: http://www.dsxcms.com
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0.0
 * ---------------------------------------------
 * $Date: 2015-12-11
 * $Id: function.common.php
 */

/**
 * 配置全局变量
 * @param string $name
 * @param string $value
 */
function G($name='',$value=null){
	global $G;
	if (empty($name)){
		return $G;
	}else {
		if (is_null($value)){
			return isset($G[$name]) ? $G[$name] : '';
		}else {
			$G[$name] = $value;
			return $G;
		}
	}
}

/**
 * 语言设置
 * @param string $langname
 */
function L($name, $value=null){
	global $lang;
	if (empty($name)){
		return $lang;
	}else {
		if (is_null($value)){
			return isset($lang[$name]) ? $lang[$name] : '';
		}else {
			$lang[$name] = $value;
			return $lang;
		}
	}
}

/**
 * 增加配置
 * @param string $name
 * @param string $value
 */
function C($name,$value=null){
	global $config;
	if (empty($name)) {
		return $config;
	}
	if (is_null($value)){
		return isset($config[$name]) ? $config[$name] : '';
	}else {
		$config[$name] = $value;
		return $config;
	}
}

/**
 * 初始化模型
 * @param string $name
 */
function M($name){
	if (is_array($name)){
		$model = new \Core\Model($name);
		return $model;
	}else {
		$modelclass = ucfirst($name).'Model';
		if (is_file(APP_PATH.$name.'/class.'.$modelclass.'.php')){
			$class = $name.'\\'.$modelclass;
		}else {
			if (is_file(APP_PATH.$name.'/Model/class.'.$modelclass.'.php')){
				$class = $name.'\\Model\\'.$modelclass;
			}else {
				$class = 'Core\\Model';
			}
		}
		$model = new $class($name);
		return $model;
	}
}

/**
 * 系统设置操作函数
 * @param string $name
 * @param string $value
 */
function setting($name=NULL, $value=NULL){
	$setting = $GLOBALS['G']['setting'];
	if (is_null($name)) {
		return $setting;
	}elseif (is_null($value)) {
		return isset($setting[$name]) ? $setting[$name] : null;
	}else {
		$setting[$name] = $value;
		return $setting;
	}
}

/**
 * 缓存操作
 * @param string $name
 * @param string $value
 */
function cache($name, $value=''){
	$cache = Core\Cache::getInstance();
	if ($value === ''){
		return $cache->get($name);
	}elseif (is_null($value)){
		return $cache->rm($name);
	}else {
		return $cache->set($name,$value);
	}
}

function cookie($name='',$value='',$expire=0){
	// 默认设置
	$config = array(
			'prefix'    =>  C('COOKIE_PREFIX'), // cookie 名称前缀
			'expire'    =>  C('COOKIE_EXPIRE'), // cookie 保存时间
			'path'      =>  C('COOKIE_PATH'), // cookie 保存路径
			'domain'    =>  C('COOKIE_DOMAIN'), // cookie 有效域名
			'secure'    =>  C('COOKIE_SECURE'), //  cookie 启用安全传输
			'httponly'  =>  C('COOKIE_HTTPONLY'), // httponly设置
	);
	$config['expire'] = $expire ? intval($expire) : $config['expire'];
	// 清除指定前缀的所有cookie
	if (is_null($name)) {
		if (empty($_COOKIE))
			return null;
			// 要删除的cookie前缀，不指定则删除config设置的指定前缀
			$prefix = empty($value) ? $config['prefix'] : $value;
			if (!empty($prefix)) {// 如果前缀为空字符串将不作处理直接返回
				foreach ($_COOKIE as $key => $val) {
					if (0 === stripos($key, $prefix)) {
						setcookie($key, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
						unset($_COOKIE[$key]);
					}
				}
			}
			return null;
	}elseif('' === $name){
		// 获取全部的cookie
		return $_COOKIE;
	}
	
	$name = $config['prefix'] . str_replace('.', '_', $name);
	if ('' === $value) {
		return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
	} else {
		if (is_null($value)) {
			setcookie($name, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
			unset($_COOKIE[$name]); // 删除指定cookie
		} else {
			// 设置cookie
			if(is_array($value)){
				$value  = serialize($value);
			}
			$expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
			setcookie($name, $value, $expire, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
			$_COOKIE[$name] = $value;
		}
	}
	return null;
}

/**
 * 获取用户头像
 * @param number $uid
 * @param string $size
 * @param number $img
 */
function avatar($uid=0,$size='big',$img=0){
	if (!$uid) return C('STATICURL').'images/avatar_default.png';
	$size = in_array($size, array('small','middle')) ? $size : 'big';
	$imgurl = getSiteURL().'/?m=member&c=avatar&uid='.$uid.'&size='.$size;
	if ($img) {
		return '<img src="'.$imgurl.'" border="0"/>';
	}else {
		return $imgurl;
	}
}

/**
 * 图片地址解析
 * @param string $path
 * @param number $html
 */
function image($path,$html=0){
	if (preg_match("/([http|https|ftp]\:\/\/)(.*?)/is", $path)){
		$url = $path;
	}else {
		if (is_file(C('ATTACHDIR').$path)){
			$url = C('ATTACHURL').$path;
		}else {
			$url = C('STATICURL').'images/nopic.jpg';
		}
	}
	if ($html){
		return '<img src="'.$url.'" />';
	}else {
		return $url;
	}
}

/**
 * 格式化距离
 * @param string $distance
 */
function distance($distance){
	if (!$distance) return '';
	if ($distance < 1000){
		return $distance.'m';
	}else {
		return number_format($distance/1000,2).'km';
	}
}

/**
 * 从远程服务器下载数据
 * @param unknown $url
 * @param number $ssl
 * @param number $timeout
 * @return mixed
 */
function httpGet($url, $ssl=0, $timeout=500){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl);
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
}

/**
 * POST提交数据到远程服务器
 * @param string $url
 * @param string $data
 * @param number $ssl
 * @param number $timeout
 * @return mixed
 */
function httpPost($url, $data='', $ssl=0, $timeout=500){
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $ssl);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $ssl);
	$res = curl_exec($curl);
	curl_close($curl);
	return $res;
}

/**
 * 获取位置及纬度
 * @param string $city
 * @param string $address
 */
function getLocation($city='',$address=''){
	$location = array();
	$url = "http://restapi.amap.com/v3/geocode/geo?key=389880a06e3f893ea46036f030c94700&s=rsv3&city=".$city."&address=".$address;
	$json  = httpGet($url);
	$data  = json_decode($json,true);
	$local = $data['geocodes'][0]['location'];
	$arr = explode(',', $local);
	$location['longitude'] = $arr[0];
	$location['latitude']  = $arr[1];
	return $location;
}

/**
 * 根据当前IP获取位置信息
 */
function getLocationByIP(){
	$ip = getIp();
	$location = cookie('location_ip');
	$location = unserialize($location);
	if ($location && $location['ip'] == $ip){
		return $location;
	}else {
		$url = 'http://api.map.baidu.com/location/ip?ak=SWEeKmLWiGERXvCtxoOMdW56&ip='.$ip.'&coor=bd09ll';
		$json = httpGet($url);
		$array = json_decode($json,true);
		$location = array(
				'ip'=>$ip,
				'longitude'=>$array['content']['point']['x'],
				'latitude'=>$array['content']['point']['y'],
				'address'=>$array['content']['address'],
				'data'=>$array['content']['address_detail']
		);
		cookie('location_ip', serialize($location));
		return $location;
	}
}

/**
 * 根据坐标获取位置信息
 * @param float $lng
 * @param float $lat
 */
function getLocationByPoint($lng,$lat){
	$lng = floatval($lng);
	$lat = floatval($lat);
	$point = $lat.','.$lng;
	$url = 'http://api.map.baidu.com/cloudrgc/v1?ak=5EFnFovwRTE1uffvxOn8Y2S2&geotable_id=7906881&location='.$point;
	$json  = httpGet($url);
	$array = json_decode($json,true);
	$location = array(
			'longitude'=>$lng,
			'latitude'=>$lat,
			'address'=>$array['formatted_address'],
			'data'=>$array['address_component'],
			'description'=>$array['recommended_location_description']
	);
	return $location;
}

/**
 *  @desc 计算两点之间的距离
 *  @param float $lat 纬度值
 *  @param float $lng 经度值
 *  @param float $lat2 纬度值
 *  @param float $lng2 经度值
 */
function getDistance($lng1,$lat1,$lng2,$lat2){
	$earthRadius = 6377830;
	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;

	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;
	
	$calcLongitude = $lng2 - $lng1;
	$calcLatitude  = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	$calculatedDistance = $earthRadius * $stepTwo;
	return round($calculatedDistance);
}

/**
 * discuz 加减密方法
 * @param string $string
 * @param number $decode
 * @param string $key
 * @param number $expiry
 * @return string
 */
function authcode($string, $decode = 0, $key = '', $expiry = 0) {
    $ckey_length = 4;
    $key  = md5($key ? $key : C('AUTHKEY'));
    $keya = md5(substr($key, 0, 16));
    $keyb = md5(substr($key, 16, 16));
    $keyc = $ckey_length ? ($decode ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);

    $string = $decode ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);

    $result = '';
    $box = range(0, 255);

    $rndkey = array();
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }

    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }

    if($decode) {
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}


/**
 * 产生一个HASH字符串
 * @return string
 */
function formhash() {
    return md5(substr(time(), 0, -4).C('AUTHKEY'));
}

function daddslashes($string, $force = 0) {
    !defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
    if(!MAGIC_QUOTES_GPC || $force) {
        if(is_array($string)) {
            foreach($string as $key => $val) {
                $string[$key] = daddslashes($val, $force);
            }
        } else {
            $string = addslashes($string);
        }
    }
    return $string;
}

/**
 * 按长度截取字符串
 * @param string $string
 * @param string $length
 * @param string $dot
 * @return string
 */
function cutstr($string, $length, $dot ='...', $charset='utf8') {
    if(strlen($string) <= $length) {
        return $string;
    }
    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
    $strcut = '';
    if(strtolower($charset) == 'utf8') {
        $n = $tn = $noc = 0;
        while($n < strlen($string)) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t < 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $length) {
                break;
            }
        }
        if($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    } else {
        for($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
        }
    }
    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);
    return $strcut.$dot;
}

/**
 * 去除一些特殊字符
 * @param string $string
 * @return mixed
 */
function dhtmlspecialchars($string) {
    if(is_array($string)) {
        foreach($string as $key => $val) {
            $string[$key] = dhtmlspecialchars($val);
        }
    } else {
        $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1',
        //$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
        str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }
    return $string;
}

/**
 * 生成一个随机字符串
 * @param number $length
 * @param number $numeric
 * @return string
 */
function random($length, $numeric = 0) {
    PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
    $seed = base_convert(md5(print_r($_SERVER, 1).microtime()), 16, $numeric ? 10 : 35);
    $seed = $numeric ? (str_replace('0', '', $seed).'012340567890') : ($seed.'zZ'.strtoupper($seed));
    $hash = '';
    $max = strlen($seed) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $seed[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 获取站点URL
 * @return string
 */
function getSiteURL(){
    if ($_SERVER['SERVER_PORT'] == 443) {
        return 'https://'.$_SERVER['HTTP_HOST'];
    }else{
        return 'http://'.$_SERVER['HTTP_HOST'];
    }
}

/**
 * 去除HTML代码和空格
 * @param string $str
 * @return mixed
 */
function stripHtml($str){
    $str = strip_tags($str);
    $str = str_replace('&amp;', '&', $str);
    $str = str_replace(array('&ldquo;','&rdquo;'),array('“','”'),$str);
    $str = preg_replace('/\s|\n\r|　/', '', $str);
    return $str;
}

/**
 * 打印数组
 * @param array $array
 */
function print_array($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

/**
 * 正则验证邮箱地址
 * @param string $email
 * @return boolean
 */
function isemail($email) {
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * 正则验证手机号码
 * @param string $mobile
 * @return number
 */
function ismobile($mobile){
    return preg_match('/^1[3|4|5|6|7|8][0-9]\d{4,8}$/', $mobile);
}

/**
 * 获取模板文件
 * @param string $file
 * @param string $tpldir 目录
 * @param string $theme 主题
 * @return string
 */
function template($file, $tpldir = '', $theme='') {
    global $G;
    $tpldir = $tpldir ? $tpldir : $G['m'];
    !$tpldir && $tpldir = 'common';
    if (defined('IN_ADMIN')) $theme = 'default';
    if (!$theme) {
    	$theme = defined('THEME') ? THEME : 'default';
    }
    
    $tplfile = TPL_PATH.$theme.'/'.$tpldir.'/'.$file.'.htm';
    if (!is_file($tplfile)){
    	$tpldir2 = $tpldir;
    	$tpldir  = 'common';
    	$tplfile = TPL_PATH.$theme.'/common/'.$file.'.htm';
    	if (!is_file($tplfile)){
    		$tpldir  = $tpldir2;
    		$theme = 'default';
    		$tplfile = TPL_PATH.'/default/'.$tpldir.'/'.$file.'.htm';
    		if (!is_file($tplfile)){
    			$tpldir  = 'common';
    			$tplfile = TPL_PATH.'default/common/'.$file.'.htm';
    		}
    	}
    }
    
    $objfile = DATA_PATH.'template/'.$theme.'/'.$tpldir.'/'.$file.'.tpl.php';
    if (!is_file($objfile) || filemtime($tplfile)>filemtime($objfile)){
    	@mkdir(dirname($objfile),0777,true);
        \Core\Template::parse_template($tplfile,$tpldir,$objfile);
    }
    return $objfile;
}

/**
 * 格式化ID
 * @param string $array
 * @return string
 */
function implodeids($array) {
    if(!empty($array)) {
        return "'".implode("','", is_array($array) ? $array : array($array))."'";
    } else {
        return '';
    }
}

/**
 * 格式时间
 * @param string $time
 * @param string $format
 * @return boolean
 */
function formatTime($time,$format=''){
    if(!$time) return false;
    !$format && $format = $GLOBALS['G']['setting']['dateformat'];
    !$format && $format = 'Y-m-d';
    return @date($format,$time);
}

/**
 * 格式化文件尺寸
 * @param number $size
 */
function formatsize($size){
    $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
    if ($size == 0) {
        return('n/a');
    } else {
        return (round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
    }
}

/**
 * 替换字符串
 * @param string $string
 * @param string $replacer
 */
function stringParser($string,$replacer){
    $result = str_replace(array_keys($replacer), array_values($replacer),$string);
    return $result;
}

/**
 * 获取当前页面地址
 * @return string
 */
function curPageURL() {
    $pageURL = 'http';
    if (!empty($_SERVER['HTTPS'])) {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

/**
 * 获取用户真实IP
 * @return Ambigous <string, unknown>
 */
function getIp() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                    $ip = $_SERVER['REMOTE_ADDR'];
                else
                    $ip = "unknown";
                return ($ip);
}

/**
 * SQL反注入
 * @param unknown $sql
 */
function injCheck($sql) {
    $check = preg_match('/select|insert|update|delete|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile/', $sql);
    if ($check) {
        return false;
    } else {
        return $sql;
    }
}

/**
 * 判断是否从移动客户端访问
 */
function mobilecheck()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
    {
        return true;
    }
    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA']))
    {
        // 找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
    }
    // 脑残法，判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT']))
    {
        $clientkeywords = array ('nokia',
            'sony',
            'ericsson',
            'mot',
            'samsung',
            'htc',
            'sgh',
            'lg',
            'sharp',
            'sie-',
            'philips',
            'panasonic',
            'alcatel',
            'lenovo',
            'iphone',
            'ipod',
            'blackberry',
            'meizu',
            'android',
            'netfront',
            'symbian',
            'ucweb',
            'windowsce',
            'palm',
            'operamini',
            'operamobi',
            'openwave',
            'nexusone',
            'cldc',
            'midp',
            'wap',
            'mobile'
        );
        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
        {
            return true;
        }
    }
    // 协议法，因为有可能不准确，放到最后判断
    if (isset ($_SERVER['HTTP_ACCEPT']))
    {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
        {
            return true;
        }
    }
    return false;
}

/**
 * 清除文档格式
 */
function cleanUpStyle($str){
	$str = preg_replace('/\s*mso-[^:]+:[^;"]+;?/i', "", $str);
	$str = preg_replace('/\s*margin(.*?)pt\s*;/i', "", $str);
	$str = preg_replace('/\s*margin(.*?)cm\s*;/i', "", $str);
	$str = preg_replace('/\s*text-indent:(.*?)\s*;/i', "", $str);
	$str = preg_replace('/\s*line-height:(.*?)\s*;/i', "", $str);
	$str = preg_replace('/\s*page-break-before: [^\s;]+;?"/i', "", $str);
	$str = preg_replace('/\s*font-variant: [^\s;]+;?"/i', "", $str);
	$str = preg_replace('/\s*tab-stops:[^;"]*;?/i', "", $str);
	$str = preg_replace('/\s*tab-stops:[^"]*/i', "", $str);
	$str = preg_replace('/\s*face="[^"]*"/i', "", $str);
	$str = preg_replace('/\s*face=[^ >]*/i', "", $str);
	$str = preg_replace('/\s*font:(.*?);/i', "", $str);
	$str = preg_replace('/\s*font-size:(.*?);/i', "", $str);
	$str = preg_replace('/\s*font-weight:(.*?);/i', "", $str);
	$str = preg_replace('/\s*font-family:[^;"]*;?/i', "", $str);
	$str = preg_replace('/<span style="Times New Roman&quot;">\s\n<\/span>/i', "", $str);
	return $str;
}