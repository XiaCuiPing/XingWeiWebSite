<?php
/**
 * 上传照片
 * @param string $input
 */
function photo_upload_data($input = 'filedata'){
	$upload = new \Core\UploadImage($input);
	if ($filedata = $upload->saveImage()){
		return  photo_add_data($filedata, true);
	}else {
		return false;
	}
}

/**
 * 添加照片信息
 * @param array $data
 * @param bool $return 是否返回照片数据
 * @return boolean|unknown|unknown
 */
function photo_add_data($data, $return=FALSE){
	if (!$data) return false;
	$photo = array(
			'uid'=>G('uid'),
			'name'=>$data['name'],
			'image'=>$data['image'],
			'thumb'=>$data['thumb'],
			'width'=>$data['width'],
			'height'=>$data['height'],
			'type'=>$data['type'],
			'size'=>$data['size'],
			'dateline'=>time()
	);
	$photoid = M('photo')->insert($photo, true);
	if ($return) {
		return photo_get_data(array('photoid'=>$photoid));
	}else {
		return $photoid;
	}
}

/**
 * 更新照片信息
 * @param mixed $condition
 * @param array $data
 */
function photo_update_data($condition,$data){
	return M('photo')->where($condition)->update($data);
}

/**
 * 删除照片信息
 * @param mixed $condition
 */
function photo_delete_data($condition){
	if (!$condition) return false;
	$photolist = M('photo')->where($condition)->select();
	if ($photolist) {
		foreach ($photolist as $list){
			@unlink(C('ATTACHDIR').$list['image']);
			@unlink(C('ATTACHDIR').$list['thumb']);
		}
		return M('photo')->where($condition)->delete();
	}
}

/**
 * 获取单张照片信息
 * @param mixed $condition
 */
function photo_get_data($condition){
	$photo = M('photo')->where($condition)->getOne();
	if ($photo){
		$photo['imageurl'] = image($photo['image']);
		$photo['thumburl'] = image($photo['thumb']);
		return $photo;
	}else {
		return array();
	}
}

/**
 * 获取照片数量
 * @param mixed $condition
 */
function photo_get_num($condition = ''){
	return M('photo')->where($condition)->count();
}

/**
 * 获取照片列表
 * @param string $condition
 * @param number $num
 * @param number $limit
 */
function photo_get_list($condition='', $num=10, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : '';
	$order = $order ? $order : 'photoid ASC';
	$photolist = M('photo')->where($condition)->order($order)->limit($limit)->select();
	if ($photolist){
		$datalist = array();
		foreach ($photolist as $list){
			$list['imageurl'] = image($list['image']);
			$list['thumburl'] = image($list['thumb']);
			array_push($datalist, $list);
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取照片分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function photo_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return photo_get_list($condition,$pagesize, $limit, $order);
}

/**
 * 创建相册
 * @param array $data
 * @param bool $return
 */
function album_add_data($data, $return=FALSE){
	$albumid = M('photo_album')->insert($data, true);
	if ($return) {
		return album_get_data(array('albumid'=>$albumid));
	}else {
		return $albumid;
	}
}

/**
 * 更新相册信息
 * @param mixed $condition
 * @param array $data
 */
function album_update_data($condition,$data){
	return M('photo_album')->where($condition)->update($data);
}

/**
 * 删除相册
 * @param mixed $condition
 */
function album_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('photo_album')->where($condition)->delete();
	}
}

/**
 * 获取单个相册信息
 * @param mixed $condition
 */
function album_get_data($condition){
	return M('photo_album')->where($condition)->geOne();
}

/**
 * 获取相册数量
 * @param mixed $condition
 */
function album_get_num($condition){
	return M('photo_album')->where($condition)->count();
}

/**
 * 获取相册列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function album_get_list($condition,$num=10,$limit=0,$order=''){
	$limit = $num ? "$limit,$num" : '';
	!$order && $order = 'albumid ASC';
	$albumlist = M('photo_album')->where($condition)->limit($limit)->order($order)->select();
	return $albumlist;
}

/**
 * 获取相册分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function album_get_page($condition,$page=1,$pagesize=10,$order=''){
	$limit = ($page-1) * $pagesize;
	return album_get_list($condition, $pagesize, $limit, $order);
}

/**
 * =====================
 * 信息图片操作
 * =====================
 */

/**
 * 添加图片信息
 * @param array $data
 * @param string $return
 * @param string $replace
 * @return unknown|unknown
 */
function image_add_data($data, $return=FALSE, $replace=false){
	$id = M('image')->insert($data, true, $replace);
	if ($return){
		return image_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 更新图片信息
 * @param mixed $condition
 * @param array $data
 */
function image_update_data($condition,$data){
	return M('image')->where($condition)->update($data);
}

/**
 * 删除图片信息
 * @param mixed $condition
 */
function image_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('image')->where($condition)->delete();
	}
}

/**
 * 获取单张图片信息
 * @param mixed $condition
 */
function image_get_data($condition){
	$image = M('image')->where($condition)->selectOne();
	if ($image) {
		$image['imageurl'] = image($image['image']);
		$image['thumburl'] = image($image['thumb']);
		return $image;
	}else {
		return array();
	}
}

/**
 * 获取图片数量
 * @param mixed $condition
 */
function image_get_num($condition){
	return M('image')->where($condition)->count();
}

/**
 * 获取图片列表
 * @param int $dataid
 * @param string $datatype
 */
function image_get_list($dataid,$datatype){
	$piclist = M('image')->where(array('dataid'=>$dataid,'datatype'=>$datatype))->select();
	if ($piclist) {
		$datalist = array();
		foreach ($piclist as $list){
			$list['imageurl'] = image($list['image']);
			$list['thumburl'] = image($list['thumb']);
			$datalist[$list['id']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}