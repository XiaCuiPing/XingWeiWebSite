<?php
/**
 * ============================================================================
 * 版权所有 (C) 2015 贵州大师兄信息技术有限公司 All Rights Reserved.
 * 网站地址: http://www.dsxcms.com
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0
 * ---------------------------------------------
 * $Date: 2015-06-27
 * $ID: class.Template.php
 */
namespace Core;
class Template{
	public static function parse_template($tplfile, $tpldir,$objfile) {
		$file = basename($tplfile, '.htm');
		if(!@$fp = fopen($tplfile, 'r')) {
			exit("Current template file './$tpldir/$file.htm' not found or have no access!");
			exit();
		}
		$template = fread($fp, filesize($tplfile));
		$template = "<?php if (!defined('IN_DSXCMS')) die('Access Denied!');?>".$template;
		fclose($fp);
		$var_regexp = "((\\\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(\[[a-zA-Z0-9_\-\.\"\'\[\]\$\x7f-\xff]+\])*)";
		$const_regexp = "([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)";
	
		//$template = preg_replace("/([\n\r]+)\t+/s", "\\1", $template);
		$template = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $template);
		$template = str_replace("{LF}", "<?=\"\\n\"?>", $template);
		
		$template = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?=\\1?>", $template);
		$template = preg_replace("/$var_regexp/es", "self::addquote('<?=\\1?>')", $template);
		$template = preg_replace("/\<\?\=\<\?\=$var_regexp\?\>\?\>/es", "self::addquote('<?=\\1?>')", $template);
		$template = preg_replace("/[\n\r\t]*\{template\s+([a-z0-9_:]+)\}[\n\r\t]*/ies", "self::stripvtemplate('\\1')", $template);
		$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\s+(.+?)\}[\n\r\t]*/ies", "self::stripvtemplate('\\1','\\2')", $template);
		$template = preg_replace("/[\n\r\t]*\{template\s+(.+?)\}[\n\r\t]*/ies", "self::stripvtemplate('\\1')", $template);
		
		$template = preg_replace("/[\n\r\t]*\{eval\s+(.+?)\}[\n\r\t]*/ies", "self::stripvtags('<? \\1 ?>','')", $template);
		$template = preg_replace("/[\n\r\t]*\{echo\s+(.+?)\}[\n\r\t]*/ies", "self::stripvtags('<? echo \\1; ?>','')", $template);
		$template = preg_replace("/([\n\r\t]*)\{elseif\s+(.+?)\}([\n\r\t]*)/ies", "self::stripvtags('\\1<? } elseif(\\2) { ?>\\3','')", $template);
		$template = preg_replace("/([\n\r\t]*)\{else\}([\n\r\t]*)/is", "\\1<? } else { ?>\\2", $template);
		
		//自定义标签
		$template = preg_replace_callback('/\<php\>(.*?)\<\/php\>/is', function($matches){
			return Template::stripvtags('<? '.$matches[1].' ?>', '');
		}, $template);
		
		//替换头像
		$template = preg_replace_callback('/{avatar\s+(.+?)\s+([big|middle|small]+)\}/is', function($matches){
			return Template::stripvtags('<? echo avatar('.$matches[1].',\''.$matches[2].'\'); ?>','');
		}, $template);
		
		//替换语言
		$template = preg_replace_callback('/{lang\s+(.+?)\}/is', function($matches){
			return Template::stripvtags('<? echo $GLOBALS[lang]['.$matches[1].']; ?>','');
		}, $template);

		for($i = 0; $i < 5; $i++) {
			$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\}[\n\r]*(.+?)[\n\r]*\{\/loop\}[\n\r\t]*/ies", "self::stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2) { ?>','\\3<? } } ?>')", $template);
			$template = preg_replace("/[\n\r\t]*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*(.+?)[\n\r\t]*\{\/loop\}[\n\r\t]*/ies", "self::stripvtags('<? if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>','\\4<? } } ?>')", $template);
			$template = preg_replace("/([\n\r\t]*)\{if\s+(.+?)\}([\n\r]*)(.+?)([\n\r]*)\{\/if\}([\n\r\t]*)/ies", "self::stripvtags('\\1<? if(\\2) { ?>\\3','\\4\\5<? } ?>\\6')", $template);
		}
	
		$template = preg_replace("/\{$const_regexp\}/s", "<?=\\1?>", $template);
		$template = preg_replace("/ \?\>[\n\r]*\<\? /s", " ", $template);
	
		$template = preg_replace("/\"(http)?[\w\.\/:]+\?[^\"]+?&[^\"]+?\"/e", "self::transamp('\\0')", $template);
		$template = preg_replace("/\<script[^\>]*?src=\"(.+?)\".*?\>\s*\<\/script\>/ise", "self::stripscriptamp('\\1')", $template);
		$template = preg_replace("/\<\?(\s{1})/is", "<?php\\1", $template);
		$template = preg_replace("/\<\?\=(.+?)\?\>/is", "<?php echo \\1;?>", $template);
		/* 修正css路径 */
		/*
		$template = preg_replace('/(<link\shref=["|\'])(?:\.\/|\.\.\/)?(css\/)?([a-z0-9A-Z_]+\.css["|\']\srel=["|\']stylesheet["|\']\stype=["|\']text\/css["|\'])/i','\1' . STATICURL . '\2\3', $template);
		$pattern = array(
				'/(href=["|\'])\.\.\/(.*?)(["|\'])/i',  // 替换相对链接
				'/((?:background|src)\s*=\s*["|\'])(?:\.\/|\.\.\/)?(images\/.*?["|\'])/is', // 在images前加上 STATICURL
				'/((?:background|background-image):\s*?url\()(?:\.\/|\.\.\/)?(images\/)/is', // 在images前加上 STATICURL
				'/([\'|"])\.\.\//is', // 以../开头的路径全部修正为空
		);
		$replace = array(
				'\1\2\3',
				'\1' . STATICURL . '\2',
				'\1' . STATICURL . '\2',
				'\1'
		);
		$template = preg_replace($pattern, $replace, $template);
		*/
		if(!@$fp = fopen($objfile, 'w')) {
			exit("Directory './data/templates/' not found or have no access!");
		}
		flock($fp, 2);
		fwrite($fp, $template);
		fclose($fp);
		@chmod($objfile, '0505');
	}
	
	/**
	 * 几个替换，过滤掉一些东西
	 */
	private static function transamp($str) {
		//$str = str_replace('&', '&amp;', $str);
		//$str = str_replace('&amp;amp;', '&amp;', $str);
		$str = str_replace('\"', '"', $str);
		return $str;
	}
	/**
	 * 从正则表达式来看是给ubb代码去掉一个\符号的，应该是为安全性着想的
	 */
	private static function addquote($var) {
		return str_replace("\\\"", "\"", preg_replace("/\[([a-zA-Z0-9_\-\.\x7f-\xff]+)\]/s", "['\\1']", $var));
	}
	/**
	 * 去掉自定义的一些tag
	 */
	public static function stripvtags($expr, $statement) {
		$expr = preg_replace("/\<\?\=(\\\$.+?)\?\>/s", "\\1", $expr);
		$expr = str_replace("\\\"", "\"", $expr);
		$statement = str_replace("\\\"", "\"", $statement);
		return $expr.$statement;
	}
	/**
	 * 作用是把&符号的html编码形式换成&，然后换成javascript引用的形式。
	 */
	private static function stripscriptamp($s) {
		$s = str_replace('&amp;', '&', $s);
		return "<script src=\"$s\" type=\"text/javascript\"></script>";
	}
	private static function stripvtemplate($tpl, $mod='') {
		if ($mod) {
			return self::stripvtags("<? include template('$tpl', '$mod'); ?>", '');
		}else {
			return self::stripvtags("<? include template('$tpl'); ?>", '');
		}
	}
}