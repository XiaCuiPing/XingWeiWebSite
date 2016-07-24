<?php
/**
 * 生成返券序列号
 * @return string
 */
function fan_ticket_create_sn(){
	$sn = date('YmdHis', time()).rand(1000,9999).rand(1000,9999);
	return strtoupper(md5($sn));
}

/**
 * 添加返券
 * @param array $data
 * @param number $return
 * @return mixed
 */
function fan_ticket_add_data($data, $return=0){
	$id = M('fan_ticket')->insert($data, true);
	if ($return) {
		return fan_ticket_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除返券
 * @param mixed $condition
 * @return boolean
 */
function fan_ticket_delete_data($condition){
	if ($condition) {
		return M('fan_ticket')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 获取返券
 * @param mixed $condition
 * @return array
 */
function fan_ticket_get_data($condition){
	$data = M('fan_ticket')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取返券数目
 * @param mixed $condition
 * @param string $field
 */
function fan_ticket_get_num($condition, $field='*'){
	return M('fan_ticket')->where($condition)->count($field);
}

/**
 * 获取返券列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @return array
 */
function fan_ticket_get_list($condition, $num=20, $limit=0){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	$ticketlist = M('fan_ticket')->where($condition)->limit($limit)->order('id ASC')->select();
	if ($ticketlist) {
		return $ticketlist;
	}else {
		return array();
	}
}

/**
 * 获取返券分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @return array
 */
function fan_ticket_get_page($condition, $page=1, $pagesize=20){
	$limit = ($page - 1) * $pagesize;
	return fan_ticket_get_list($condition, $pagesize, $limit);
}


/**
 * 添加返券发放记录
 * @param array $data
 * @param number $return
 * @return mixed
 */
function fan_record_add_data($data, $return=0){
	$id = M('fan_record')->insert($data, true);
	if ($return) {
		return fan_record_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除返券记录
 * @param mixed $condition
 */
function fan_record_delete_data($condition){
	if ($condition) {
		return M('fan_record')->where($condition)->delete();
	}else {
		return 0;
	}
}

/**
 * 获取返券记录
 * @param mixed $condition
 */
function fan_record_get_data($condition){
	$data = M('fan_record')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取返券记录数
 * @param mixed $condition
 */
function fan_record_get_num($condition){
	return M('fan_record')->where($condition)->count();
}

/**
 * 获取返券记录列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @return array
 */
function fan_record_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : ($limit ? $limit : '');
	!$order && $order = 'id DESC';
	$recordlist = M('fan_record')->where($condition)->limit($limit)->order($order)->select();
	if ($recordlist) {
		return $recordlist;
	}else {
		return array();
	}
}

/**
 * 获取返券记录分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function fan_record_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return fan_record_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 添加返现排队
 * @param array $data
 * @param number $return
 * @return boolean|unknown
 */
function fan_queue_add_data($data, $return=0){
	$id = M('fan_queue')->add($data, true);
	if ($return) {
		
	}else {
		return $id;
	}
}

/**
 * 删除返现排队
 * @param mixed $condition
 * @return boolean
 */
function fan_queue_delete_data($condition){
	if ($condition) {
		return M('fan_queue')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新返现排队
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function fan_queue_update_data($condition, $data){
	return M('fan_queue')->where($condition)->update($data);
}

/**
 * 获取返现排队信息
 * @param mixed $condition
 * @return array
 */
function fan_queue_get_data($condition){
	$data = M('fan_queue')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取返现排队人数
 * @param mixed $condition
 * @param string $field
 */
function fan_queue_get_num($condition, $field='*'){
	return M('fan_queue')->where($condition)->count($field);
}

/**
 * 获取返现排队列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @return array
 */
function fan_queue_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	!$order && $order = 'id DESC';
	$queuelist = M('fan_queue')->where($condition)->limit($limit)->order($order)->select();
	if ($queuelist) {
		return $queuelist;
	}else {
		return array();
	}
}

/**
 * 获取返现排队分页
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function fan_queue_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page-1) * $pagesize;
	return fan_queue_get_list($condition, $pagesize, $limit, $order);
}
