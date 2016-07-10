<?php
/**
 * 生成图形验证码
 * @author David Song
 *
 */
namespace Core;
class Captcha{
	function __construct(){
		$this->createCode();
	}
	
	function createCode($len=4){
		$width  = 100;
		$height = 50;
		$size   = 22;//字体大小
		$font   = ROOT_PATH.'/static/font/arial.ttf'; //字体	
		$img   = imagecreatetruecolor($width,$height); //创建画布
		$bgimg = imagecreatefromjpeg(ROOT_PATH.'/static/background/'.rand(1, 5).'.jpg'); //生成背景图片
		$bg_x  = rand(0,100); //随机招贴画布起始X轴坐标
		$bg_y  = rand(0,50); //随机招贴画布起始Y轴坐标
		imagecopy($img,$bgimg,0,0,$bg_x,$bg_y,$bg_x+$width,$bg_y+$height); //把背景图片$bging粘贴的画布上
		
		
		$str = $this->creaStr($len); //字符串
		for($i=0,$j=5;$i<4;$i++){
			$array = array(-1,1);
			$p = array_rand($array);
			$an = $array[$p]*mt_rand(1,10); //扭曲角度
			imagettftext($img, $size, $an, $j+5,34,imagecolorallocate($img,rand(0,100),rand(0,100),rand(0,100)), $font, $str[$i]);//生成验证字符窜
			$j+=20;
		}
		cookie('captchacode', strtolower($str));
		header('Content-type:image/png');
		imagepng($img);
		imagedestroy($img);
	}
	
	//生成随机字符串
	private function creaStr($length){
	    $characters = '23456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ';
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, strlen($characters) - 1)];
	    }
	    return $randomString;
	}
}