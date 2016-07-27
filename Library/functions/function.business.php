<?php
/**
 * 添加联盟商家
 * @param array $data
 * @param number $return
 * @return unknown
 */
function business_add_data($data, $return=0){
	$id = M('business')->insert($data, true);
	if ($return){
		return business_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除联盟商家
 * @param mixed $condition
 * @return boolean
 */
function business_delete_data($condition){
	if ($condition) {
		return M('business')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新联盟商家
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function business_update_data($condition, $data){
	return M('business')->where($condition)->update($data);
}

/**
 * 获取联盟商家
 * @param mixed $condition
 * @return array
 */
function business_get_data($condition){
	$data = M('business')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取联盟商家数目
 * @param mixed $condition
 * @return int
 */
function business_get_num($condition){
	return M('business')->where($condition)->count();
}

/**
 * 获取联盟商家列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @return array
 */
function business_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	!$order && $order = 'id ASC';
	$businesslist = M('business')->where($condition)->limit($limit)->order($order)->select();
	if ($businesslist) {
		return $businesslist;
	}else {
		return array();
	}
}

/**
 * 获取联盟商家分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function business_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return business_get_list($condition, $pagesize, $limit , $order);
}

/**
 * 添加联盟商家详细介绍
 * @param array $data
 * @return boolean|unknown
 */
function business_add_desc($data){
	return M('business_desc')->insert($data, false, true);
}

/**
 * 删除联盟商家详细信息
 * @param mixed $condition
 * @return boolean
 */
function business_delete_desc($condition){
	if ($condition) {
		return M('business_desc')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新联盟商家详细介绍
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function business_update_desc($condition, $data){
	return M('business_desc')->where($condition)->update($data);
}

/**
 * 获取联盟商家详细介绍
 * @param int $businessid
 */
function business_get_desc($businessid){
	return M('business_desc')->where(array('businessid'=>$businessid))->getOne();
}

/**
 * 获取联盟商家详细列表
 * @param mixed $condition
 * @return array
 */
function business_get_desc_list($condition){
	$desclist = M('business_desc')->where($condition)->select();
	if ($desclist){
		return $desclist;
	}else {
		return array();
	}
}

/**
 * 添加联盟企业
 * @param array $data
 * @param number $return
 * @return unknown|unknown
 */
function company_add_data($data,$return=0){
	$id = M('company')->insert($data, true);
	if ($return) {
		return company_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除联盟企业
 * @param mixed $condition
 * @return boolean
 */
function company_delete_data($condition){
	if ($condition) {
		return M('company')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新联盟企业信息
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function company_update_data($condition,$data){
	return M('company')->where($condition)->update($data);
}

/**
 * 获取联盟企业信息
 * @param mixed $condition
 * @return array
 */
function company_get_data($condition){
	$data = M('company')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取联盟企业数目
 * @param mixed $condition
 */
function company_get_num($condition){
	return M('company')->where($condition)->count();
}

/**
 * 获取联盟企业列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @return array
 */
function company_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : ($limit ? $limit :'');
	!$order && $order = 'id ASC';
	$companylist = M('company')->where($condition)->limit($limit)->order($order)->select();
	if ($companylist){
		return $companylist;
	}else {
		return array();
	}
}

/**
 * 获取联盟企业列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function company_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page -1) * $pagesize;
	return company_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 添加企业描述
 * @param array $data
 * @return boolean|unknown
 */
function company_add_desc($data){
	return M('company_desc')->insert($data, false, true);
}

/**
 * 删除企业描述
 * @param mixed $condition
 * @return boolean
 */
function company_delete_desc($condition){
	if ($condition) {
		return M('company_desc')->where($condition)->delete();
	}else {
		return false;
	}
}

/**
 * 更新企业描述
 * @param mixed $condition
 * @param array $data
 * @return boolean
 */
function company_update_desc($condition, $data){
	return M('company_desc')->where($condition)->update($data);
}

/**
 * 获取企业描述
 * @param int $companyid
 * @return array
 */
function company_get_desc($companyid){
	$data = M('company_desc')->where(array('companyid'=>$companyid))->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}