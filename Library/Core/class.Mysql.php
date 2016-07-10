<?php
/**
 * ============================================================================
 * Copyright (c) 2015 贵州大师兄信息技术有限公司 All rights reserved.
 * siteַ: http://www.dsxcms.com
 * ============================================================================
 * @author:     David Song<songdewei@163.com>
 * @version:    v1.0.0
 * ---------------------------------------------
 * $Date: 2016-05-13
 * $Id: class.Mysql.php
 */
namespace Core;
class Mysql{
	public $querynum = 0;
	public $tablepre = '';
	public $tablename = '';
	public $linkID = null;
	
	/**
	 * 构造方法
	 * @param array $config
	 */
	function __construct($config = array()){
		$this->config = array(
				'type'  =>C('DB_TYPE'),
				'host'  =>C('DB_HOST'),
				'port'  =>C('DB_PORT'),
				'dbname'=>C('DB_NAME'),
				'user'  =>C('DB_USER'),
				'pwd'   =>C('DB_PWD'),
				'pconnect'=>C('DB_TYPE'),
				'charset' =>C('DB_CHARSET'),
				'tablepre'=>C('DB_PREFIX')
		);
		if (is_array($config)){
			$this->config = array_merge($this->config,$config);
		}
		$this->tablepre = $this->config['tablepre'];
		$this->connect();
	}
	
	/**
	 * 链接Mysql数据库
	 */
	public function connect() {
		if ($this->config['pconnect'] == 0){
			//非持久连接
			$this->linkID = mysql_connect($this->config['host'].':'.$this->config['port'], $this->config['user'], $this->config['pwd']);
		}else {
			//持久连接
			$this->linkID = mysql_pconnect($this->config['host'].':'.$this->config['port'], $this->config['user'], $this->config['pwd']);
		}
		if ($this->errorno() != 0){
			$this->halt("Connect(".$this->config['pconnect'].") to MySQL failed");
		}
		
		@mysql_query("SET character_set_connection=".$this->config['charset'].", character_set_results=".$this->config['charset'].", character_set_client=binary",$this->linkID);
		if($this->version() > '5.0'){
			@mysql_query("SET sql_mode=''",$this->linkID);
		}
		if (!@mysql_select_db($this->config['dbname'],$this->linkID)){
			$this->halt('Cannot use database('.$this->config['dbname'].')');
		}
	}
	
	/**
	 * 关闭数据库
	 */
	public function close() {
		return @mysql_close($this->linkID);
	}
	
	/**
	 * 选择数据库
	 * @param string $dbname
	 */
	public function select_db($dbname){
		if (!@mysql_select_db($dbname)){
			$this->halt('Cannot use database('.$this->config['dbname'].')');
		}
	}
	
	/**
	 * 获取mysql版本
	 */
	public function version(){
		return @mysql_get_server_info($this->linkID);
	}
	
	/**
	 * 获取先前表名称
	 * @param string $tableName
	 */
	public function table($tableName){
		return $this->tablepre.$tableName;
	}
	
	/**
	 * 执行查询操作
	 * @param string $SQL
	 * @param string $method
	 */
	public function query($SQL,$method=''){
		if($method=='U_B' && function_exists('mysql_unbuffered_query')){
			$query = mysql_unbuffered_query($SQL,$this->linkID);
		}else{
			$query = mysql_query($SQL,$this->linkID);
		}
		$this->querynum++;
		if(!$query && DEBUG){
			$this->halt('Query Error: ' . $SQL);
		}
		return $query;
	}
	
	/**
	 * 返回最后插入ID
	 */
	public function insert_id() {
		return mysql_insert_id($this->linkID);
	}
	
	/**
	 * 返回查询结果数组
	 * @param mixed $query
	 */
	public function fetch_array($query) {
		return mysql_fetch_array($query,MYSQL_ASSOC);
	}
	
	/**
	 * 返回行
	 * @param mixed $query
	 */
	public function fetch_row($query){
		return mysql_fetch_row($query);
	}
	
	/**
	 * 返回影响的数据数目
	 */
	public function affected_rows() {
		return mysql_affected_rows($this->linkID);
	}
	
	/**
	 * 返回结果数目
	 * @param mixed $query
	 */
	public function num_rows($query) {
		$rows = mysql_num_rows($query);
		return $rows;
	}
	
	/**
	 * 释放内存
	 * @param mixed $query
	 */
	public function free_result($query) {
		return mysql_free_result($query);
	}
	
	
	public function result($result, $row, $field=null){
		return mysql_result($result, $row, $field);
	}
	
	public function fetch_field($query, $field_offset = null){
		return mysql_fetch_field($query,$field_offset);
	}
	
	/**
	 * 显示数据库中的数据表
	 * @param string $dbname
	 */
	public function show_tables($dbname=''){
		$tables = array();
		$dbname = $dbname ? $dbname : $this->config['dbname'];
		$query = $this->query("SHOW TABLES FROM ".$dbname);
		while ($row = $this->fetch_row($query)){
			$tables[] = $row[0];
		}
		return $tables;
	}
	
	/**
	 * 显示数据库状态
	 * @param string $dbname
	 */
	public function show_table_status($dbname=''){
		$status = array();
		$dbname = $dbname ? $dbname : $this->config['dbname'];
		$query = $this->query("SHOW TABLE STATUS FROM ".$dbname);
		while ($table = $this->fetch_array($query)){
			$status[] = $table;
		}
		return $status;
	}
	
	/**
	 * 显示表DDL
	 * @param string $table
	 */
	public function show_create_table($table){
		$query = $this->query("SHOW CREATE TABLE ".$this->table($table));
		$row = $this->fetch_row($query);
		return $row[1];
	}
	
	/**
	 * 显示表字段
	 * @param string $table
	 */
	public function  show_table_fields($table){
		$fields = array();
		$query = $this->query("SHOW FIELDS FROM ".$this->table($table));
		while ($row = $this->fetch_array($query)){
			$fields[] = $row;
		}
		return $fields;
	}
	
	/**
	 * 返回错误信息
	 */
	public function error(){
		return $this->linkID ? mysql_error($this->linkID) : mysql_error();
	}
	
	/**
	 * 返回错误代码
	 */
	public function errorno(){
		return $this->linkID ? mysql_errno($this->linkID) : mysql_errno();
	}
	
	/**
	 * 显示错误信息
	 * @param string $msg
	 */
	public function halt($msg='') {
		$sqlerror = $this->error();
		$sqlerrno = $this->errorno();
		echo"<html><head><title>SQL Error</title><style type='text/css'>A { TEXT-DECORATION: none;}
				a:hover{ text-decoration: underline;}
				td {COLOR: #000000; font-size:12px;}</style><body>\n\n";
		echo"<table style='TABLE-LAYOUT:fixed;WORD-WRAP: break-word'><tr><td>$msg";
		echo"<br><br><b>The URL Is</b>:<br>http://$_SERVER[HTTP_HOST]";
		echo"<br><br><b>MySQL Server Error</b>:<br>$sqlerror($sqlerrno)";
		echo"</td></tr></table></body></html>";
		exit;
	}
	
	/**
	 * 格式化SQL
	 * @param array $array
	 * @param string $glue
	 */
	public function implode_field_value($array, $glue = ',') {
		if (is_array($array)){
			$sql = $comma = '';
			foreach ($array as $k => $v) {
				$sql .= $comma."`$k`='$v'";
				$comma = $glue;
			}
			return $sql;
		}else {
			return $array;
		}
	}
}