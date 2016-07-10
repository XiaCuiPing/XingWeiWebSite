<?php
/**
 * 生成商品编号
 */
function goods_create_no(){
	return date('YmdHis', time()).rand(1000,9999).rand(1000,9999);
}

/**
 * 添加商品
 * @param array $data
 * @param number $return
 */
function goods_add_data($data, $return=0){
	$id = M('goods')->insert($data, true);
	if ($return) {
		return goods_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除商品
 * @param mixed $condition
 * @return boolean
 */
function goods_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('goods')->where($condition)->delete();
	}
}

/**
 * 更新商品信息
 * @param mixed $condition
 * @param array $data
 */
function goods_update_data($condition, $data){
	return M('goods')->where($condition)->update($data);
}

/**
 * 获取单品信息
 * @param mixed $condition
 */
function goods_get_data($condition) {
	return M('goods')->where($condition)->getOne();
}

/**
 * 获取商品数目
 * @param mixed $condition
 * @param string $field
 */
function goods_get_num($condition, $field='*') {
	return M('goods')->where($condition)->count($field);
}

/**
 * 获取商品列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function goods_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	$goodslist = M('goods')->where($condition)->limit($limit)->order($order)->select();
	if ($goodslist) {
		$datalist = $goodsids = array();
		foreach ($goodslist as $list){
			$datalist[$list['id']] = $list;
		}
		$piclist = goods_get_image_list(array_keys($datalist));
		foreach ($piclist as $pic){
			$datalist[$pic['dataid']]['image'] = $pic['image'];
			$datalist[$pic['dataid']]['thumb'] = $pic['thumb'];
			$datalist[$pic['dataid']]['imageurl'] = image($pic['image']);
			$datalist[$pic['dataid']]['thumburl'] = image($pic['thumb']);
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取商品分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function goods_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return goods_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 获取附近商品列表
 * @param mixed $condition
 * @param number $lng
 * @param number $lat
 * @param number $num
 * @param number $limit
 * @param number $sort
 */
function goods_get_local_list($condition, $lng=0, $lat=0, $num=20, $limit=0, $sort=0){
	if (!$lng || $lat) {
		$location = getLocationByIP();
		$lng = $location['longitude'];
		$lat = $location['latitude'];
	}
	$lng = floatval($lng);
	$lat = floatval($lat);
	
	$limit = $num ? "$limit,$num" : ($limit ? $limit : '');
	$asc = $sort ? 'DESC' : 'ASC';
	$goodslist = M('goods')->field("*,dsx_distance($lng,$lat,longitude,latitude) AS distance")
	->where($condition)->limit($limit)->order('distance', $asc)->select();
	if ($goodslist) {
		$datalist = $goodsids = array();
		foreach ($goodslist as $list){
			$datalist[$list['id']] = $list;
		}
		$piclist = goods_get_image_list(array_keys($datalist));
		foreach ($piclist as $pic){
			$datalist[$pic['dataid']]['image'] = $pic['image'];
			$datalist[$pic['dataid']]['thumb'] = $pic['thumb'];
			$datalist[$pic['dataid']]['imageurl'] = image($pic['image']);
			$datalist[$pic['dataid']]['thumburl'] = image($pic['thumb']);
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取附近商品分页列表
 * @param mixed $condition
 * @param number $lng
 * @param number $lat
 * @param number $page
 * @param number $pagesize
 * @param number $sort
 */
function goods_get_local_page($condition, $lng=0, $lat=0, $page=1, $pagesize=20, $sort=0){
	$limit = ($page - 1) * $pagesize;
	return goods_get_local_list($condition, $lng, $lat, $pagesize, $limit, $sort);
}

/**
 * 获取商品图片列表
 * @param mixed $gids
 */
function goods_get_image_list($gids){
	$gids = is_array($gids) ? implodeids($gids) : $gids;
	$imagelist = M('image')->field('*,MIN(id) AS mid')
	->where(array('datatype'=>'goods', 'dataid'=>array('IN', $gids)))->group('dataid')->select();
	if ($imagelist) {
		$datalist = array();
		foreach ($imagelist as $list) {
			$list['thumburl'] = image($list['thumb']);
			$list['imageurl'] = image($list['image']);
			array_push($datalist, $list);
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 添加商品描述
 * @param mixed $data
 */
function goods_add_desc($data) {
	return M('goods_desc')->insert($data, false, true);
}

/**
 * 删除商品描述
 * @param mixed $condition
 */
function goods_delete_desc($condition) {
	if (!$condition) {
		return false;
	}else {
		return M('goods_desc')->where($condition)->delete();
	}
}

/**
 * 获取单品描述
 * @param int $gid
 */
function goods_get_desc($gid) {
	return M('goods_desc')->where(array('gid'=>$gid))->getOne();
}

/**
 * 获取商品描述列表
 * @param mixed $gids
 */
function goods_get_desc_list($gids){
	$gids = is_array($gids) ? implodeids($gids) : $gids;
	$desclist = M('goods_desc')->where(array('gid'=>array('IN', $gids)))->select();
	if ($desclist) {
		return $desclist;
	}else {
		return array();
	}
}

/**
 * =====================
 * 购物车操作
 * =====================
 */
	
/**
 * 添加购物车
 * @param mixed $data
 * @param number $return
 */
function cart_add_data($data, $return=0){
	$cartid = M('cart')->insert($data, true, true);
	if ($return) {
		return cart_get_data(array('cartid'=>$cartid));
	}else {
		return $cartid;
	}
}

/**
 * 删除购物车
 * @param mixed $condition
 */
function cart_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('cart')->where($condition)->delete();
	}
}

/**
 * 更新购物车
 * @param mixed $condition
 * @param array $data
 */
function cart_update_data($condition, $data){
	return M('cart')->where($condition)->update($data);
}

/**
 * 获取购物车信息
 * @param mixed $condition
 */
function cart_get_data($condition){
	$data = M('cart')->where($condition)->getOne();
	if ($data) {
		return $data;
	}else {
		return array();
	}
}

/**
 * 获取购物车商品数量
 * @param mixed $condition
 * @param string $field
 */
function cart_get_num($condition, $field='*'){
	return M('cart')->where($condition)->count($field);
}

/**
 * 获取购物车列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function cart_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : ($limit ? $limit : '');
	!$order && $order = 'cartid DESC';
	$cartlist = M('cart')->where($condition)->limit($limit)->order($order)->select();
	if ($cartlist) {
		$datalist = array();
		foreach ($cartlist as $list){
			$datalist[$list['cartid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取购物车分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function cart_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page -1) * $pagesize;
	return cart_get_list($condition, $pagesize, $limit, $order);
}
