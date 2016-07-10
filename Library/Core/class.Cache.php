<?php
/**
 * ============================================================================
 * 版权所有 (C) 2015 贵州大师兄信息技术有限公司 All Rights Reserved.
 * 网站地址: http://www.dsxcms.com
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0
 * ---------------------------------------------
 * $Date: 2015-12-11
 * $ID: class.Cache.php
 */
namespace Core;
class Cache{
	public static function getInstance(){
		static $instance;
		if(!is_object($instance)) $instance = new Cache();
		return $instance;
	}
	
	/**
	 * 写入缓存
	 * @param string $name
	 * @param string $value
	 */
    public function set($name, $value='') {
        if(!is_dir(CACHE_PATH)) {
            @mkdir(CACHE_PATH,0777,true);
        }
        
        if(is_array($value)){
            $cachedata = 'return '.$this->arrayeval($value).';';
        }else {
            $cachedata = $value;
        }
        
        if($fp = @fopen(CACHE_PATH.$name.'.php', 'wb')) {
            fwrite($fp, "<?php\n//Cache file, DO NOT modify me!".
                "\n//Created: ".date("M j, Y, G:i").
                "\n//Identify: ".md5($name.'.php'.$cachedata)."\n$cachedata");
            fclose($fp);
        } else {
            die('Can not write to cache files, please check directory ./cache/ .');
        }
    }
    
    /**
     * 获取缓存
     * @param string $name
     */
    public function get($name){
        $cachefile = CACHE_PATH.$name.'.php';
        if (is_file($cachefile)){
        	return include($cachefile);
        }else {
        	return false;
        }
    }
    
    /**
     * 删除缓存
     * @param string $name
     */
    public function rm($name){
        return @unlink(CACHE_PATH.$name.'.php');
    }
    
    /**
     * 序列号数组
     * @param array $array
     * @param number $level
     */
    public function arrayeval($array, $level = 0) {
        if(!is_array($array)) {
            return "'".$array."'";
        }
        if(is_array($array) && function_exists('var_export')) {
            return var_export($array, true);
        }

        $space = '';
        for($i = 0; $i <= $level; $i++) {
            $space .= "\t";
        }
        $evaluate = "Array\n$space(\n";
        $comma = $space;
        if(is_array($array)) {
            foreach($array as $key => $val) {
                $key = is_string($key) ? '\''.addcslashes($key, '\'\\').'\'' : $key;
                $val = !is_array($val) && (!preg_match("/^\-?[1-9]\d*$/", $val) || strlen($val) > 12) ? '\''.addcslashes($val, '\'\\').'\'' : $val;
                if(is_array($val)) {
                    $evaluate .= "$comma$key => ".self::arrayeval($val, $level + 1);
                } else {
                    $evaluate .= "$comma$key => $val";
                }
                $comma = ",\n$space";
            }
        }
        $evaluate .= "\n$space)";
        return $evaluate;
    }
}