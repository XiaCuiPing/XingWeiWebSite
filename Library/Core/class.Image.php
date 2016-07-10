<?php
/**
 * ============================================================================
 * Copyright (c) 2015 贵州大师兄信息技术有限公司 All rights reserved.
 * siteַ: http://www.dsxcms.com
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0
 * ---------------------------------------------
 * $Date: 2015-06-27
 * $ID: class.Image.php
 */
namespace Core;
class Image{
    /* 缩略图相关常量定义 */
    const IMAGE_THUMB_SCALE     =   1 ; //常量，标识缩略图等比例缩放类型
    const IMAGE_THUMB_FILLED    =   2 ; //常量，标识缩略图缩放后填充类型
    const IMAGE_THUMB_CENTER    =   3 ; //常量，标识缩略图居中裁剪类型
    const IMAGE_THUMB_NORTHWEST =   4 ; //常量，标识缩略图左上角裁剪类型
    const IMAGE_THUMB_SOUTHEAST =   5 ; //常量，标识缩略图右下角裁剪类型
    const IMAGE_THUMB_FIXED     =   6 ; //常量，标识缩略图固定尺寸缩放类型
    
    /* 水印相关常量定义 */
    const IMAGE_WATER_NORTHWEST =   1 ; //常量，标识左上角水印
    const IMAGE_WATER_NORTH     =   2 ; //常量，标识上居中水印
    const IMAGE_WATER_NORTHEAST =   3 ; //常量，标识右上角水印
    const IMAGE_WATER_WEST      =   4 ; //常量，标识左居中水印
    const IMAGE_WATER_CENTER    =   5 ; //常量，标识居中水印
    const IMAGE_WATER_EAST      =   6 ; //常量，标识右居中水印
    const IMAGE_WATER_SOUTHWEST =   7 ; //常量，标识左下角水印
    const IMAGE_WATER_SOUTH     =   8 ; //常量，标识下居中水印
    const IMAGE_WATER_SOUTHEAST =   9 ; //常量，标识右下角水印
    /**
     * 图像资源对象
     * @var resource
     */
    private $img;
    
    /**
     * 图像信息，包括width,height,type,mime,size
     * @var array
     */
    private $info;
    public $errno = 0;
    
    public static function getInstance($imgname = null){
    	static $instance;
    	if(!is_object($instance)) $instance = new \Core\Image($imgname);
    	return $instance;
    }
    
    /**
     * 构造方法，可用于打开一张图像
     * @param string $imgname 图像路径
     */
    public function __construct($imgname = null) {
        $imgname && $this->open($imgname);
    }
    
    /**
     * 打开一张图像
     * @param  string $imgname 图像路径
     */
    public function open($imgname){
        //检测图像文件
        if(!is_file($imgname)) {
        	$this->$errno = 1;
        	//die('不存在的图像文件');
        }
    
        //获取图像信息
        $info = getimagesize($imgname);
    
        //检测图像合法性
        if(false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))){
            //die('非法图像文件');
            $this->$errno = 2;
        }
    
        //设置图像信息
        $this->info = array(
            'width'  => $info[0],
            'height' => $info[1],
            'type'   => image_type_to_extension($info[2], false),
            'mime'   => $info['mime']
        );
        //销毁已存在的图像
        empty($this->img) || imagedestroy($this->img);
    
        //打开图像
        $fun = "imagecreatefrom{$this->info['type']}";
        $this->img = $fun($imgname);
    }
    
    /**
     * 保存图像
     * @param  string  $imgname   图像保存名称
     * @param  string  $type      图像类型
     * @param  integer $quality   图像质量
     * @param  boolean $interlace 是否对JPEG类型图像设置隔行扫描
     */
    public function save($imgname, $type = null, $quality=100,$interlace = true){
        if(empty($this->img)) {
        	//die('没有可以被保存的图像资源');
        	$this->$errno = 3;
        }
        
        @mkdir(dirname($imgname), 0777 ,true);
        //自动获取图像类型
        if(is_null($type)){
            $type = $this->info['type'];
        } else {
            $type = strtolower($type);
        }
        //保存图像
        if('jpeg' == $type || 'jpg' == $type){
            //JPEG图像设置隔行扫描
            imageinterlace($this->img, $interlace);
            imagejpeg($this->img, $imgname,$quality);
        }elseif('gif' == $type){
            imagegif($this->img,$imgname);
        }else{
            imagepng($this->img, $imgname);
        }
    }
    
    /**
     * 返回图像宽度
     * @return integer 图像宽度
     */
    public function width(){
        if(empty($this->img)) {
        	//die('没有指定图像资源');
        	$this->$errno = 4;
        }
        return $this->info['width'];
    }
    
    /**
     * 返回图像高度
     * @return integer 图像高度
     */
    public function height(){
        if(empty($this->img)){
        	//die('没有指定图像资源');
        	$this->$errno = 4;
        }
        return $this->info['height'];
    }
    
    /**
     * 返回图像类型
     * @return string 图像类型
     */
    public function type(){
        if(empty($this->img)){
        	die('没有指定图像资源');
        	$this->$errno = 4;
        }
        return $this->info['type'];
    }
    
    /**
     * 返回图像MIME类型
     * @return string 图像MIME类型
     */
    public function mime(){
        if(empty($this->img)) {
        	//die('没有指定图像资源');
        	$this->$errno = 4;
        }
        return $this->info['mime'];
    }
    
    /**
     * 返回图像尺寸数组 0 - 图像宽度，1 - 图像高度
     * @return array 图像尺寸
     */
    public function size(){
        if(empty($this->img)) {
        	//die('没有指定图像资源');
        	$this->$errno = 4;
        }
        return array($this->info['width'], $this->info['height']);
    }
    
    /**
     * 裁剪图像
     * @param  integer $w      裁剪区域宽度
     * @param  integer $h      裁剪区域高度
     * @param  integer $x      裁剪区域x坐标
     * @param  integer $y      裁剪区域y坐标
     * @param  integer $width  图像保存宽度
     * @param  integer $height 图像保存高度
     */
    public function crop($w, $h, $x = 0, $y = 0, $width = null, $height = null){
        if(empty($this->img)) {
        	//die('没有可以被裁剪的图像资源');
        	$this->$errno = 5;
        }
    
        //设置保存尺寸
        empty($width)  && $width  = $w;
        empty($height) && $height = $h;
    
        //创建新图像
        $img = imagecreatetruecolor($width, $height);
        // 调整默认颜色
        $color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $color);
        
        //裁剪
        imagecopyresampled($img, $this->img, 0, 0, $x, $y, $width, $height, $w, $h);
        imagedestroy($this->img); //销毁原图
        
        //设置新图像
        $this->img = $img;
        $this->info['width']  = $width;
        $this->info['height'] = $height;
    }
    
    /**
     * 生成缩略图
     * @param  integer $width  缩略图最大宽度
     * @param  integer $height 缩略图最大高度
     * @param  integer $type   缩略图裁剪类型
     */
    public function thumb($width, $height, $type = Image::IMAGE_THUMB_SCALE){
        if(empty($this->img)) die('没有可以被缩略的图像资源');
    
        //原图宽度和高度
        $w = $this->info['width'];
        $h = $this->info['height'];
    
        /* 计算缩略图生成的必要参数 */
        switch ($type) {
            /* 等比例缩放 */
            case Image::IMAGE_THUMB_SCALE:
                //原图尺寸小于缩略图尺寸则不进行缩略
                if($w < $width && $h < $height) return;
    
                //计算缩放比例
                $scale = min($width/$w, $height/$h);
    
                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $width  = $w * $scale;
                $height = $h * $scale;
                break;
    
                /* 居中裁剪 */
            case Image::IMAGE_THUMB_CENTER:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);
    
                //设置缩略图的坐标及宽度和高度
                $w = $width/$scale;
                $h = $height/$scale;
                $x = ($this->info['width'] - $w)/2;
                $y = ($this->info['height'] - $h)/2;
                break;
    
                /* 左上角裁剪 */
            case Image::IMAGE_THUMB_NORTHWEST:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);
    
                //设置缩略图的坐标及宽度和高度
                $x = $y = 0;
                $w = $width/$scale;
                $h = $height/$scale;
                break;
    
                /* 右下角裁剪 */
            case Image::IMAGE_THUMB_SOUTHEAST:
                //计算缩放比例
                $scale = max($width/$w, $height/$h);
    
                //设置缩略图的坐标及宽度和高度
                $w = $width/$scale;
                $h = $height/$scale;
                $x = $this->info['width'] - $w;
                $y = $this->info['height'] - $h;
                break;
    
                /* 填充 */
            case Image::IMAGE_THUMB_FILLED:
                //计算缩放比例
                if($w < $width && $h < $height){
                    $scale = 1;
                } else {
                    $scale = min($width/$w, $height/$h);
                }
    
                //设置缩略图的坐标及宽度和高度
                $neww = $w * $scale;
                $newh = $h * $scale;
                $posx = ($width  - $w * $scale)/2;
                $posy = ($height - $h * $scale)/2;
    
                do{
                    //创建新图像
                    $img = imagecreatetruecolor($width, $height);
                    // 调整默认颜色
                    $color = imagecolorallocate($img, 255, 255, 255);
                    imagefill($img, 0, 0, $color);
    
                    //裁剪
                    imagecopyresampled($img, $this->img, $posx, $posy, $x, $y, $neww, $newh, $w, $h);
                    imagedestroy($this->img); //销毁原图
                    $this->img = $img;
                } while(!empty($this->gif) && $this->gifNext());
    
                $this->info['width']  = $width;
                $this->info['height'] = $height;
                return;
    
                /* 固定 */
            case Image::IMAGE_THUMB_FIXED:
                $x = $y = 0;
                break;
    
            default:
                //die('不支持的缩略图裁剪类型');
                $this->errno = 6;
        }
    
        /* 裁剪图像 */
        $this->crop($w, $h, $x, $y, $width, $height);
    }
    
    /**
     * 添加水印
     * @param  string  $source 水印图片路径
     * @param  integer $locate 水印位置
     * @param  integer $alpha  水印透明度
     */
    public function water($source, $locate = Image::IMAGE_WATER_SOUTHEAST,$alpha=80){
        //资源检测
        if(empty($this->img)) {
        	die('没有可以被添加水印的图像资源');
        }
        if(!is_file($source)) {
        	die('水印图像不存在');
        }
    
        //获取水印图像信息
        $info = getimagesize($source);
        if(false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))){
            die('非法水印文件');
        }
    
        //创建水印图像资源
        $fun   = 'imagecreatefrom' . image_type_to_extension($info[2], false);
        $water = $fun($source);
    
        //设定水印图像的混色模式
        imagealphablending($water, true);
    
        /* 设定水印位置 */
        switch ($locate) {
            /* 右下角水印 */
            case Image::IMAGE_WATER_SOUTHEAST:
                $x = $this->info['width'] - $info[0];
                $y = $this->info['height'] - $info[1];
                break;
    
                /* 左下角水印 */
            case Image::IMAGE_WATER_SOUTHWEST:
                $x = 0;
                $y = $this->info['height'] - $info[1];
                break;
    
                /* 左上角水印 */
            case Image::IMAGE_WATER_NORTHWEST:
                $x = $y = 0;
                break;
    
                /* 右上角水印 */
            case Image::IMAGE_WATER_NORTHEAST:
                $x = $this->info['width'] - $info[0];
                $y = 0;
                break;
    
                /* 居中水印 */
            case Image::IMAGE_WATER_CENTER:
                $x = ($this->info['width'] - $info[0])/2;
                $y = ($this->info['height'] - $info[1])/2;
                break;
    
                /* 下居中水印 */
            case Image::IMAGE_WATER_SOUTH:
                $x = ($this->info['width'] - $info[0])/2;
                $y = $this->info['height'] - $info[1];
                break;
    
                /* 右居中水印 */
            case Image::IMAGE_WATER_EAST:
                $x = $this->info['width'] - $info[0];
                $y = ($this->info['height'] - $info[1])/2;
                break;
    
                /* 上居中水印 */
            case Image::IMAGE_WATER_NORTH:
                $x = ($this->info['width'] - $info[0])/2;
                $y = 0;
                break;
    
                /* 左居中水印 */
            case Image::IMAGE_WATER_WEST:
                $x = 0;
                $y = ($this->info['height'] - $info[1])/2;
                break;
    
            default:
                /* 自定义水印坐标 */
                if(is_array($locate)){
                    list($x, $y) = $locate;
                } else {
                    die('不支持的水印位置类型');
                }
        }
    
        //添加水印
        $src = imagecreatetruecolor($info[0], $info[1]);
        // 调整默认颜色
        $color = imagecolorallocate($src, 255, 255, 255);
        imagefill($src, 0, 0, $color);
        
        imagecopy($src, $this->img, 0, 0, $x, $y, $info[0], $info[1]);
        imagecopy($src, $water, 0, 0, 0, 0, $info[0], $info[1]);
        imagecopymerge($this->img, $src, $x, $y, 0, 0, $info[0], $info[1], $alpha);
        
        //销毁零时图片资源
        imagedestroy($src);
    
        //销毁水印资源
        imagedestroy($water);
    }
    
    /**
     * 图像添加文字
     * @param  string  $text   添加的文字
     * @param  string  $font   字体路径
     * @param  integer $size   字号
     * @param  string  $color  文字颜色
     * @param  integer $locate 文字写入位置
     * @param  integer $offset 文字相对当前位置的偏移量
     * @param  integer $angle  文字倾斜角度
     */
    public function text($text, $font, $size, $color = '#00000000',
        $locate = Image::IMAGE_WATER_SOUTHEAST, $offset = 0, $angle = 0){
        //资源检测
        if(empty($this->img)) die('没有可以被写入文字的图像资源');
        if(!is_file($font)) die("不存在的字体文件：{$font}");
    
        //获取文字信息
        $info = imagettfbbox($size, $angle, $font, $text);
        $minx = min($info[0], $info[2], $info[4], $info[6]);
        $maxx = max($info[0], $info[2], $info[4], $info[6]);
        $miny = min($info[1], $info[3], $info[5], $info[7]);
        $maxy = max($info[1], $info[3], $info[5], $info[7]);
    
        /* 计算文字初始坐标和尺寸 */
        $x = $minx;
        $y = abs($miny);
        $w = $maxx - $minx;
        $h = $maxy - $miny;
    
        /* 设定文字位置 */
        switch ($locate) {
            /* 右下角文字 */
            case Image::IMAGE_WATER_SOUTHEAST:
                $x += $this->info['width']  - $w;
                $y += $this->info['height'] - $h;
                break;
    
                /* 左下角文字 */
            case Image::IMAGE_WATER_SOUTHWEST:
                $y += $this->info['height'] - $h;
                break;
    
                /* 左上角文字 */
            case Image::IMAGE_WATER_NORTHWEST:
                // 起始坐标即为左上角坐标，无需调整
                break;
    
                /* 右上角文字 */
            case Image::IMAGE_WATER_NORTHEAST:
                $x += $this->info['width'] - $w;
                break;
    
                /* 居中文字 */
            case Image::IMAGE_WATER_CENTER:
                $x += ($this->info['width']  - $w)/2;
                $y += ($this->info['height'] - $h)/2;
                break;
    
                /* 下居中文字 */
            case Image::IMAGE_WATER_SOUTH:
                $x += ($this->info['width'] - $w)/2;
                $y += $this->info['height'] - $h;
                break;
    
                /* 右居中文字 */
            case Image::IMAGE_WATER_EAST:
                $x += $this->info['width'] - $w;
                $y += ($this->info['height'] - $h)/2;
                break;
    
                /* 上居中文字 */
            case Image::IMAGE_WATER_NORTH:
                $x += ($this->info['width'] - $w)/2;
                break;
    
                /* 左居中文字 */
            case Image::IMAGE_WATER_WEST:
                $y += ($this->info['height'] - $h)/2;
                break;
    
            default:
                /* 自定义文字坐标 */
                if(is_array($locate)){
                    list($posx, $posy) = $locate;
                    $x += $posx;
                    $y += $posy;
                } else {
                    die('不支持的文字位置类型');
                }
        }
    
        /* 设置偏移量 */
        if(is_array($offset)){
            $offset = array_map('intval', $offset);
            list($ox, $oy) = $offset;
        } else{
            $offset = intval($offset);
            $ox = $oy = $offset;
        }
    
        /* 设置颜色 */
        if(is_string($color) && 0 === strpos($color, '#')){
            $color = str_split(substr($color, 1), 2);
            $color = array_map('hexdec', $color);
            if(empty($color[3]) || $color[3] > 127){
                $color[3] = 0;
            }
        } elseif (!is_array($color)) {
            die('错误的颜色值');
        }
        
        /* 写入文字 */
        $col = imagecolorallocatealpha($this->img, $color[0], $color[1], $color[2], $color[3]);
        imagettftext($this->img, $size, $angle, $x + $ox, $y + $oy, $col, $font, $text);
    }
    
    /**
     * 添加图片滤镜效果
     * @param number $filtertype
     * @param int $arg1
     * @param int $arg2
     * @param int $arg3
     * @param int $arg4
     */
    public function filter($filtertype=0, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null){
    	/**
	    *IMG_FILTER_NEGATE：将图像中所有颜色反转。
	    *IMG_FILTER_GRAYSCALE：将图像转换为灰度的。
	    *IMG_FILTER_BRIGHTNESS：改变图像的亮度。用 arg1 设定亮度级别。
	    *IMG_FILTER_CONTRAST：改变图像的对比度。用 arg1 设定对比度级别。
	    *IMG_FILTER_COLORIZE：与 IMG_FILTER_GRAYSCALE 类似，不过可以指定颜色。用 arg1，arg2 和 arg3 分别指定 red，blue 和 green。每种颜色范围是 0 到 255。
	    *IMG_FILTER_EDGEDETECT：用边缘检测来突出图像的边缘。
	    *IMG_FILTER_EMBOSS：使图像浮雕化。
	    *IMG_FILTER_GAUSSIAN_BLUR：用高斯算法模糊图像。
	    *IMG_FILTER_SELECTIVE_BLUR：模糊图像。
	    *IMG_FILTER_MEAN_REMOVAL：用平均移除法来达到轮廓效果。
	    *IMG_FILTER_SMOOTH：使图像更柔滑。用 arg1 设定柔滑级别。
    	 */
    	switch ($filtertype){
    		case IMG_FILTER_BRIGHTNESS :
    			imagefilter($this->img, $filtertype, $arg1);
    			break;
    		case IMG_FILTER_CONTRAST:
    			imagefilter($this->img, $filtertype, $arg1);
    			break;
    		case IMG_FILTER_COLORIZE:
    			imagefilter($this->img, $arg1, $arg2, $arg3, $arg4);
    			break;
    		case IMG_FILTER_SMOOTH:
    			imagefilter($this->img, $filtertype, $arg1);
    			break;
    		default:imagefilter($this->img, $filtertype);
    	}
    	return $this->img;
    }
    
    /**
     * 析构方法，用于销毁图像资源
     */
    public function __destruct() {
        empty($this->img) || imagedestroy($this->img);
    }
}