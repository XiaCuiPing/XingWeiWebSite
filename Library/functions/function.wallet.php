<?php
/**
 * 添加钱包
 * @param array $data
 * @return boolean|unknown
 */
function wallet_add_data($data){
	return M('wallet')->insert($data, false, true);
}

/**
 * 删除钱包
 * @param mixed $condition
 * @return boolean
 */
function wallet_delete_data($condition){
	if ($condition) {
		return M('wallet')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新钱包
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function wallet_update_data($condition, $data){
	return M('wallet')->where($condition)->update($data);
}

/**
 * 获取钱包数据
 * @param int $uid
 * @return array
 */
function wallet_get_data($uid){
	$data = M('wallet')->where(array('uid'=>$uid))->getOne();
	if (!$data) {
		wallet_add_data(array(
				'uid'=>$uid,
				'balance'=>0,
				'total_expend'=>0,
				'total_income'=>0
		));
		return wallet_get_data($uid);
	}else {
		return $data;
	}
}

/**
 * 添加交易信息
 * @param array $data
 * @param number $return
 * @return void|boolean|unknown
 */
function trade_add_data($data, $return=0){
	$trade_id = M('trade')->insert($data, true);
	if ($return) {
		return ;
	}else {
		return $trade_id;
	}
}

/**
 * 删除交易信息
 * @param mixed $condition
 * @return boolean
 */
function trade_delete_data($condition){
	if ($condition) {
		return M('trade')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新交易信息
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function trade_update_data($condition, $data){
	return M('trade')->where($condition)->update($data);
}

/**
 * 获取交易信息
 * @param mixed $condition
 * @return array
 */
function trade_get_data($condition){
	$data = M('trade')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取交易记录数目
 * @param mixed $condition
 * @param string $field
 */
function trade_get_num($condition, $field='*'){
	return M('trade')->where($condition)->count($field);
}

function trade_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	!$order && $order = 'trade_id DESC';
	$tradelist = M('trade')->where($condition)->limit($limit)->order($order)->select();
	if ($tradelist) {
		return $tradelist;
	}else {
		return array();
	}
}

/**
 * 获取交易记录分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 * @return array
 */
function trade_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return trade_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 添加用户积分记录
 * @param array $data
 */
function score_add_data($data){
	return M('score')->insert($data, false, true);
}

/**
 * 删除用户积分记录
 * @param mixed $condition
 */
function score_delete_data($condition){
	if ($condition) {
		return M('score')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新积分记录
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function score_update_data($condition, $data){
	return M('score')->where($condition)->update($data);
}

/**
 * 获取用户积分信息
 * @param int $uid
 * @return array
 */
function score_get_data($uid){
	$data = M('score')->where(array('uid'=>$uid))->getOne();
	if ($data) {
		return $data;
	}else {
		score_add_data(array(
				'uid'=>$uid,
				'score'=>0
		));
		return score_get_data($uid);
	}
}