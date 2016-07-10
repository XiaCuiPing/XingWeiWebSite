<?php
/**
 * 添加商铺信息
 * @param array $data
 * @param number $return
 */
function shop_add_data($data, $return=0){
	$shopid = M('shop')->insert($data, true);
	if ($return) {
		return shop_get_data(array('shopid'=>$shopid));
	}else {
		return $shopid;
	}
}

/**
 * 删除店铺信息
 * @param mixed $condition
 */
function shop_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('shop')->where($condition)->delete();
	}
}

/**
 * 更新店铺信息
 * @param mixed $condition
 * @param array $data
 */
function shop_update_data($condition,$data){
	return M('shop')->where($condition)->update($data);
}

/**
 * 获取店铺信息
 * @param mixed $condition
 */
function shop_get_data($condition){
	$shop = M('shop')->where($condition)->getOne();
	if ($shop) {
		return $shop;
	}else {
		return array();
	}
}

/**
 * 获取店铺数量
 * @param mixed $condition
 * @param string $field
 */
function shop_get_num($condition, $field='*'){
	return M('shop')->where($condition)->count($field);
}

/**
 * 获取店铺列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function shop_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	!$order && $order = 'shopid DESC';
	$shoplist = M('shop')->where($condition)->limit($limit)->order($order)->select();
	if ($shoplist) {
		$datalist = array();
		foreach ($shoplist as $list) {
			$datalist[$list['shopid']] = $list;
		}
		$piclist = shop_get_image_list(array_keys($datalist));
		foreach ($piclist as $pic){
			$datalist[$pic['dataid']]['image'] = $pic['image'];
			$datalist[$pic['dataid']]['thumb'] = $pic['thumb'];
			$datalist[$pic['dataid']]['imageurl'] = $pic['imageurl'];
			$datalist[$pic['dataid']]['thumburl'] = $pic['thumburl'];
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取店铺分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function shop_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return shop_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 获取附近店铺列表
 * @param mixed $condition
 * @param float $lng
 * @param float $lat
 * @param number $num
 * @param number $limit
 * @param number $sort
 */
function shop_get_local_list($condition,$lng,$lat, $num=20, $limit=0, $sort=0){
	$lng = floatval($lng);
	$lat = floatval($lat);
	$asc = $sort ? 'DESC' : 'ASC';
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	$shoplist = M('shop')->field("*,dsx_distance($lng,$lat,longitude,latitude) AS distance")
	->where($condition)->limit($limit)->order('distance', $asc)->select();
	if ($shoplist) {
		$datalist = array();
		foreach ($shoplist as $list) {
			$datalist[$list['shopid']] = $list;
		}
		$piclist = shop_get_image_list(array_keys($datalist));
		foreach ($piclist as $pic){
			$datalist[$pic['dataid']]['image'] = $pic['image'];
			$datalist[$pic['dataid']]['thumb'] = $pic['thumb'];
			$datalist[$pic['dataid']]['imageurl'] = $pic['imageurl'];
			$datalist[$pic['dataid']]['thumburl'] = $pic['thumburl'];
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取附近店铺分页列表
 * @param mixed $condition
 * @param float $lng
 * @param float $lat
 * @param number $page
 * @param number $pagesize
 * @param number $sort
 */
function shop_get_local_page($condition, $lng, $lat, $page=1, $pagesize=20, $sort=0){
	$limit = ($page - 1) * $pagesize;
	return shop_get_local_list($condition, $lng, $lat, $pagesize, $limit, $sort);
}

/**
 * 获取店铺图片
 * @param int $shopid
 */
function shop_get_image($shopid) {
	return image_get_data(array('datatype'=>'shop', 'dataid'=>$shopid));
}

/**
 * 获取店铺图片列表
 * @param mixed $shopids
 */
function shop_get_image_list($shopids){
	$shopids = is_array($shopids) ? implodeids($shopids) : $shopids;
	$piclist = M('image')->field('*, MIN(id) AS mid')
	->where(array('datatype'=>'shop','dataid'=>array('IN',$shopids)))->group('dataid')->select();
	if ($piclist) {
		$datalist = array();
		foreach ($piclist as $pic){
			$pic['imageurl'] = image($pic['image']);
			$pic['thumburl'] = image($pic['thumb']);
			$datalist[$pic['id']] = $pic;
		}
		return $datalist;
	}else {
		return array();
	}
}