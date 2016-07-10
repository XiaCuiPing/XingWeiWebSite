<?php
namespace Core;
class Model{
	protected $db;
	protected $tablename;
	protected $sql = '';
	protected $data = array();
	protected $config = array();
	protected $option = array(
			'field'=>'*',
			'where'=>'',
			'order'=>'',
			'group'=>'',
			'having'=>'',
			'limit'=>'',
			'page'=>'',
			'join'=>'',
			'union'=>''
	);
	
	function __construct($name=''){
		static $db;
		if (!is_object($db)) $db = new Mysql();
		$this->db = $db;
		$name = $name ? $name : MODEL_NAME;
		$this->t($name);
	}
	
	/**
	 * 连贯操作
	 * @param string $tableName
	 */
	private function t($tableName){
		foreach ($this->option as $key=>$value){
			$this->option[$key] = '';
		}
		foreach ($this->option as $key=>$value){
			$this->option[$key] = '';
		}
		$tbname = '';
		if (is_array($tableName)){
			foreach ($tableName as $k=>$v){
				if (is_numeric($k)){
					$tbname.= $this->db->table($v).',';
				}else {
					$tbname.= $this->db->table($k)." AS ".$v.',';
				}
			}
			$this->tablename = trim($tbname, ',');
		}else {
			$this->tablename = $this->db->table($tableName);
		}
		//return $this;
	}
	
	public function field($args = '*'){
		if (is_array($args)){
			$this->option['field'] = implode($args, ',');
		}else {
			$this->option['field'] = $args;
		}
		!$this->option['field'] && $this->option['feild'] = '*';
		return $this;
	}
	
	public function where($args,$glue = 'AND'){
		$wherestr = '';
		$glue = strtoupper($glue);
		$glue = in_array($glue, array('AND','OR','XOR')) ? ' '.$glue.' ' : ' AND ';
		if (is_string($args)){
			$wherestr = $args;
		}elseif (is_array($args) && !empty($args)){
			foreach ($args as $key=>$value){
				if (is_numeric($key)){
					$wherestr.= $glue.$value;
				}else {
					$key = '`'.$key.'`';
					if (is_array($value)){
						$arr = $value;
						$separate = $value[0];
						if (!in_array($separate, array('=','>','<','>=','<=','<>','LIKE','LEFTLIKE','RIGHTLIKE','IN','NOT IN'))){
							$separate = '=';
						}
						if (strtoupper($separate) == 'LIKE'){
							$wherestr.= $glue.$key." LIKE '%".$value[1]."%'";
						}elseif (strtoupper($separate) == 'LEFTLIKE'){
							$wherestr.= $glue.$key." LIKE '".$value[1]."%'";
						}elseif (strtoupper($separate) == 'RIGHTLIKE'){
							$wherestr.= $glue.$key." LIKE '%".$value[1]."'";
						}elseif (strtoupper($separate) == 'IN' || strtoupper($separate) == 'NOT IN'){
							$wherestr.= $glue.$key.' '.strtoupper($separate)."($value[1])";
						}else {
							$value[1] = "'$value[1]'";
							$wherestr.= $glue.$key.$separate.$value[1];
						}
					}else {
						$wherestr.= $glue.$key."='".$value."'";
					}
				}
			}
			$wherestr = $wherestr? substr($wherestr, strlen($glue)) : '';
		}
		$this->option['where'] = $wherestr ? "WHERE ".$wherestr : "";
		return $this;
	}
	
	public function order($field,$sort = 'ASC'){
		$sql = '';
		if (func_num_args() == 1){
			if (is_string($field)){
				$this->option['order'] = $field;
			}elseif (is_array($field)){
				$order = array();
				foreach ($field as $k=>$v){
					if (is_numeric($k)){
						if (is_string($v)) {
							array_push($order, $v);
						}else {
							$v[1] = strtoupper($v[1]);
							!in_array($v[1], array('ASC','DESC')) && $v[1] = 'ASC';
							array_push($order, "$v[0] $v[1]");
						}
					}else {
						array_push($order, "$k $v");
					}
				}
				$this->option['order'] = implode(',', $order);
			}else {
				$this->option['order'] = '';
			}
				
		}else {
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC','DESC')) ? $sort : 'ASC';
			$this->option['order'] = is_string($field) ? " $field $sort" : '';
		}
		$this->option['order'] = $this->option['order'] ? "ORDER BY ".$this->option['order'] : "";
		return $this;
	}
	
	public function limit($start,$num=0){
		if (func_num_args() == 1){
			if (is_string($start)){
				$this->option['limit'] = $start;
			}elseif (is_array($start)){
				$this->option['limit'] = "$start[0],$start[1]";
			}elseif (is_numeric($start)){
				$this->option['limit'] = "0,$start";
			}else {
				$this->option['limit'] = '';
			}
		}else {
			$num   = abs($num);
			$start = abs($start);
			if ($num > 0) {
				$this->option['limit'] = "$start,$num";
			}else {
				$this->option['limit'] = $start;
			}
		}
	
		$this->option['limit'] = $this->option['limit'] ? "LIMIT ".$this->option['limit'] : '';
		return $this;
	}
	public function page($page,$rows=10){
		$page = intval($page);
		$rows = intval($rows);
		$page = max(array($page,1));
		$rows = abs($rows);
		$start = ($page-1)*$rows;
		$this->limit($start,$rows);
		return $this;
	}
	public function group($field){
		$this->option['group'] = $field ? 'GROUP BY '.$field : '';
		return $this;
	}
	public function having($having){
		$this->option['having'] = $having ? "HAVING ".$having : "";
		return $this;
	}
	public function join($join,$type='LEFT'){
		$joinstr = '';
		if (func_num_args() == 1){
			$jointype = 'LEFT JOIN';
		}else {
			$type = strtoupper($type);
			$type = in_array($type, array('LEFT','RIGHT','INNER')) ? $type :'';
			$jointype = $type ? $type.' JOIN' : 'JOIN';
		}
	
		if (is_array($join)){
			foreach ($join as $key=>$value){
				$joinstr.= ' '.$jointype.' '.$value;
			}
		}else {
			$joinstr.= ' '.$jointype.' '.$join;
		}
		$this->option['join'].= $joinstr;
		return $this;
	}
	public function union($table,$all=FALSE){
		$separate = $all ? 'UNION ALL ' : 'UNION ';
		$this->option['union'].= $separate."SELECT ".$this->option['field']." FROM ".$this->table($table);
		return $this;
	}
	
	public function getSQL(){
		return $this->sql;
	}
	
	private function setSQL($type='select'){
		if (!is_string($type)) {
			$type = 'select';
		}
	
		if ($type == 'select') {
			$this->option['field'] = $this->option['field'] ? $this->option['field'] : '*';
			$SQL = "SELECT ".$this->option['field']." FROM ".$this->tablename;
			$SQL.= $this->option['join']   ? ' '.$this->option['join']   : '';
			$SQL.= $this->option['union']  ? ' '.$this->option['union']  : '';
			$SQL.= $this->option['where']  ? ' '.$this->option['where']  : '';
			$SQL.= $this->option['group']  ? ' '.$this->option['group']  : '';
			$SQL.= $this->option['having'] ? ' '.$this->option['having'] : '';
			$SQL.= $this->option['order']  ? ' '.$this->option['order']  : '';
			$SQL.= $this->option['limit']  ? ' '.$this->option['limit']  : '';
			$this->sql = $SQL;
		}else {
			$this->sql = $type;
		}
	}
	
	public function select() {
		$this->setSQL('select');
		$query = $this->db->query($this->sql);
		while ($data = $this->db->fetch_array($query)){
			$result[] = $data;
		}
		return $result;
	}
	
	public function getOne(){
		!$this->option['limit'] && $this->option['limit'] = " LIMIT 0,1";
		$this->setSQL('select');
		$query  = $this->db->query($this->sql,'U_B');
		$result = $this->db->fetch_array($query, MYSQL_ASSOC);
		return $result;
	}
	
	public function find($limit=0,$num=0){
		$limit = intval($limit);
		$num   = intval($num);
		if (func_num_args() == 1) {
			if ($limit) {
				if(is_numeric($limit)) {
					$limitstr = "0,$limit";
				}else {
					$limitstr = $limit;
				}
			}else {
				$limitstr = '';
			}
		}else {
			$limitstr = "$limit, $num";
		}
		$this->option['limit'] = $limitstr ? 'LIMIT '.$limitstr : '';
		return $this->select();
	}
	
	public function count($field=''){
		!$field && $field = '*';
		$this->option['field'] = "COUNT($field) AS num";
		$row = $this->getOne();
		return $row["num"];
	}
	
	public function data($data = null){
		$this->data = $data;
	}
	
	public function add($data=null,$return_insert_id=false,$replace=false){
		return $this->insert($data, $return_insert_id, $replace);
	}
	
	public function insert($data=null,$return_insert_id=false,$replace=false){
		$this->data = $data ? $data : $this->data;
		if ($this->data) {
			$sql = $this->db->implode_field_value($this->data);
			$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
			$return = $this->db->query("$cmd ".$this->tablename." SET $sql");
			return $return_insert_id ? $this->db->insert_id() : $return;
		}else {
			return false;
		}
	}
	
	public function insertAll($array,$return_insert_id=false,$replace=false){
		if(!empty($array) && is_array($array)){
			$ids = array();
			foreach ($array as $data){
				$ids[] = $this->insert($data,$return_insert_id,$replace);
			}
			return $return_insert_id ? $ids : true;
		}else {
			return false;
		}
	}
	
	public function delete(){
		$res = $this->db->query("DELETE FROM ".$this->tablename." ".$this->option['where']);
		return $res ? $this->db->affected_rows() : false;
	}
	
	public function save($data, $unbuffered = false, $low_priority = false){
		return $this->update($data, $unbuffered, $low_priority);
	}
	
	public function update($data=null, $unbuffered = false, $low_priority = false) {
		$this->data = $data ? $data : $this->data;
		if ($this->data) {
			$sql = $this->db->implode_field_value($this->data);
			$cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
			$res = $this->db->query("$cmd {$this->tablename} SET $sql ".$this->option['where'],$unbuffered ? 'UNBUFFERED' : '');
			return $res ? $this->db->affected_rows() : false;
		}else  {
			return false;
		}
	}
	
	public function updateAll($array, $unbuffered = false, $low_priority = false){
		$affect_rows = 0;
		foreach ($array as $data){
			$affect_rows+= $this->update($data,$unbuffered,$low_priority);
		}
		return $affect_rows;
	}
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
	public function __call($name,$args){
		throw new  \Exception('Class "'.get_class($this).'" does not have a method named "'.$name.'".');
	}
}