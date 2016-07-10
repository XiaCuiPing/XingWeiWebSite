<?php
/**
 * 添加附件信息
 * @param array $data
 * @param string $return
 * @return unknown
 */
function attach_add_data($data, $return=FALSE){
	$attachid = M('attachment')->insert($data, true);
	if ($return) {
		return attach_get_data(array('attachid'=>$attachid));
	}else {
		return $attachid;
	}
}

/**
 * 删除附件信息
 * @param mixed $condition
 */
function attach_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		$attachlist = M('attachment')->where($condition)->select();
		if ($attachlist) {
			foreach ($attachlist as $list){
				@unlink(C('ATTACHDIR').$list['attachment']);
			}
		}
		return M('attachment')->where($condition)->delete();
	}
}

/**
 * 更新附件信息
 * @param mixed $condition
 * @param array $data
 */
function attach_update_data($condition, $data){
	return M('attachment')->where($condition)->update($data);
}

/**
 * 获取附件信息
 * @param mixed $condition
 */
function attach_get_data($condition){
	return M('attachment')->where($condition)->getOne();
}

/**
 * 获取附件数量
 * @param mixed $condition
 */
function attach_get_num($condition){
	return M('attachment')->where($condition)->count();
}

/**
 * 获取附件列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function attach_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : $limit;
	!$order && $order = 'attachid DESC';
	$attachlist = M('attachment')->where($condition)->limit($limit)->order($order)->select();
	if ($attachlist) {
		return $attachlist;
	}else {
		return array();
	}
}

/**
 * 获取附件分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param mixed $order
 */
function attach_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return attach_get_list($condition, $pagesize, $limit, $order);
}

/**
 * ====================================
 * 广告管理
 * ====================================
 */

/**
 * 添加广告
 * @param array $data
 * @param bool $return
 */

function ad_add_data($data, $return=FALSE){
	$id = M('ad')->insert($data, true);
	if ($return) {
		return ad_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除广告
 * @param mixed $condition
 */
function ad_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('ad')->where($condition)->delete();
	}
}

/**
 * 更新广告
 * @param mixed $condition
 * @param array $data
 */
function ad_update_data($condition,$data){
	return M('ad')->where($condition)->update($data);
}

/**
 * 获取广告信息
 * @param mixed $condition
 */
function ad_get_data($condition){
	return M('ad')->where($condition)->getOne();
}

/**
 * 获取广告数量
 * @param mixed $condition
 */
function ad_get_num($condition){
	return M('ad')->where($condition)->count();
}

/**
 * 获取广告列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function ad_get_list($condition,$num=10,$limit=0,$order=''){
	$limit = $num ? "$limit,$num" : '';
	!$order && $order = 'displayorder ASC,id ASC';
	$adlist = M('ad')->where($condition)->limit($limit)->order($order)->select();
	if ($adlist) {
		$datalist = array();
		foreach ($adlist as $list){
			$list['picurl'] = image($list['pic']);
			$datalist[$list['id']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取广告分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function ad_get_page($condition,$page=1,$pagesize=20,$order=''){
	$limit = ($page - 1) * $pagesize;
	return ad_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 获取广告代码
 * @param int $id
 */
function ad_get_html_by_id($id){
	$data = ad_get_data(array('id'=>$id));
	return ad_get_html_by_data($data);
}

/**
 * 获取广告代码
 * @param array $data
 */
function ad_get_html_by_data($data){
	$html = '';
	$addata = unserialize($data['data'][$data['type']]);
	if ($addata) {
		if ($data['type'] == 'text') {
			$html = '<div><a href="'.$addata['link'].'" target="_blank"  onclick="DSXCMS.adClick('.$data['id'].')">'.$addata['text'].'</a></div>';
		}elseif ($data['type'] == 'image') {
			$style = '';
			$addata['width']  = is_numeric($addata['width']) ? $addata['width'].'px' : $addata['width'];
			$addata['height'] = is_numeric($addata['height']) ? $addata['height'].'px' : $addata['height'];
			$style = $addata['width'] ? 'width:'.$data['width'].';' : '';
			$style.= $addata['height'] ? 'height:'.$addata['height'].';' : '';
			$style = $style ? ' style="'.$style.'"' : '';
			$html = '<div><a href="'.$addata['link'].'" onclick="DSXCMS.adClick('.$data['id'].')"><img src="'.$addata['image'].'"'.$style.'></a></div>';
		}else {
			$html = '<div onclick="DSXCMS.adClick('.$data['id'].')">'.$addata['code'].'</div>';
		}
	}
	return $html;
}

/**
 * =================
 * 页面管理
 * =================
 */

/**
 * 添加页面
 * @param array $data
 * @param string $return
 */
function page_add_data($data, $return=false){
	$pageid = M('page')->insert($data, true);
	if ($return) {
		return page_get_data(array('pageid'=>$pageid));
	}else {
		return $pageid;
	}
}

/**
 * 删除页面
 * @param mixed $condition
 */
function page_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('page')->where($condition)->delete();
	}
}

/**
 * 更新页面信息
 * @param mixed $condition
 * @param array $data
 */
function page_update_data($condition, $data){
	return M('page')->where($condition)->update($data);
}

/**
 * 获取页面信息
 * @param mixed $condition
 */
function page_get_data($condition){
	return M('page')->where($condition)->getOne();
}

/**
 * 获取页面数目
 * @param mixed $condition
 */
function page_get_num($condition){
	return M('page')->where($condition)->count();
}

/**
 * 获取页面列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function page_get_list($condition, $num=10, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : ($limit ? "0,$limit" : '');
	!$order && $order = "displayorder ASC,pageid ASC";
	$pagelist = M('page')->where($condition)->limit($limit)->order($order)->select();
	if ($pagelist) {
		$datalist = array();
		foreach ($pagelist as $list){
			$datalist[$list['pageid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取页面分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function page_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return page_get_list($condition, $pagesize, $limit, $order);
}

/**
 * ======================
 * 区域管理
 * ======================
 */

/**
 * 添加区域信息
 * @param array $data
 * @param string $return
 */
function district_add_data($data, $return=FALSE){
	$id = M('district')->insert($data, true);
	if ($return) {
		return district_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除区域信息
 * @param mixed $condition
 */
function district_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('district')->where($condition)->delete();
	}
}

/**
 * 更新区域信息
 * @param mixed $condition
 * @param array $data
 */
function district_update_data($condition, $data){
	return M('district')->where($condition)->update($data);
}

/**
 * 获取区域信息
 * @param mixed $condition
 */
function district_get_data($condition){
	return M('district')->where($condition)->getOne();
}

/**
 * 获取区域数目
 * @param mixed $condition
 */
function district_get_num($condition=''){
	return M('district')->where($condition)->count();
}

/**
 * 获取区域列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function district_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit, $num" : '';
	!$order && $order = 'displayorder ASC,id ASC';
	$districtlist = M('district')->where($condition)->limit($limit)->order($order)->select();
	if ($districtlist) {
		$datalist = array();
		foreach ($districtlist as $list) {
			$datalist[$list['id']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取区域分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function district_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page-1) * $pagesize;
	return district_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 获取区域选项列表
 * @param mixed $condition
 * @param number $selected
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function district_get_options($condition, $selected=0, $num=20, $limit=0, $order=''){
	$options = '';
	$districtlist = district_get_list($condition, $num, $limit, $order);
	foreach ($districtlist as $id=>$data){
		$a = $selected == $id ? ' selected="selected"' : '';
		$options.= '<option value="'.$id.'"'.$a.'>'.$data['name'].'</option>';
	}
	return $options;
}

/**
 * ===================
 * 链接管理
 * ===================
 */

/**
 * 添加链接
 * @param array $data
 * @param string $return
 */
function link_add_data($data, $return=FALSE){
	$linkid = M('link')->insert($data, true);
	if ($return) {
		return link_get_data(array('linkid'=>$linkid));
	}else {
		return $linkid;
	}
}

/**
 * 删除链接
 * @param mixed $condition
 */
function link_delete_data($condition){
	if (!$condition) {
		return $condition;
	}else {
		return M('link')->where($condition)->delete();
	}
}

/**
 * 更新链接
 * @param mixed $condition
 * @param array $data
 */
function link_update_data($condition, $data){
	return M('link')->where($condition)->update($data);
}

/**
 * 获取链接信息
 * @param mixed $condition
 */
function link_get_data($condition){
	return M('link')->where($condition)->getOne();
}

/**
 * 获取链接数目
 * @param mixed $condition
 */
function link_get_num($condition){
	return M('link')->where($condition)->count();
}

/**
 * 获取链接列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @return unknown[]
 */
function link_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : '';
	!$order && $order = 'displayorder ASC';
	$linklist = M('link')->where($condition)->limit($limit, $num)->order($order)->select();
	if ($linklist) {
		$datalist = array();
		foreach ($linklist as $list){
			$list['pic'] = image($list['pic']);
			$datalist[$list['linkid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取链接分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function link_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page - 1) * $pagesize;
	return link_get_list($condition, $pagesize, $limit, $order);
}

/**
 * =============================
 * 添加关注
 * =============================
 */

/**
 * 加关注
 * @param array $data
 * @param string $return
 */
function follow_add_data($data, $return=false){
	$id = M('follow')->insert($data, true);
	if ($return) {
		return follow_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 取消关注
 * @param mixed $condition
 */
function follow_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('follow')->where($condition)->delete();
	}
}

/**
 * 更新关注
 * @param mixed $condition
 * @param array $data
 */
function follow_update_data($condition, $data){
	return M('follow')->where($condition)->update($data);
}

/**
 * 获取关注信息
 * @param mixed $condition
 */
function follow_get_data($condition){
	return M('follow')->where($condition)->getOne();
}

/**
 * 获取关注数
 * @param mixed $condition
 */
function follow_get_num($condition){
	return M('follow')->where($condition)->count();
}

/**
 * 获取关注列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 */
function follow_get_list($condition, $num=20, $limit=0){
	$limit = $num ? "$limit, $num" : ($limit ? $limit : '');
	$followlist = M('follow')->where($condition)->limit($limit)->select();
	if ($followlist) {
		return $followlist;
	}else {
		return array();
	}
}

/**
 * 获取关注分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 */
function follow_get_page($condition, $page=1, $pagesize=20){
	$limit = ($page - 1) * $pagesize;
	return follow_get_list($condition, $pagesize, $limit);
}

/**
 * ===================
 * 收藏管理
 * ===================
 */

/**
 * 添加收藏
 * @param mixed $data
 * @param string $return
 */
function favorite_add_data($data, $return=false){
	$favid = M('favorite')->insert($data, true, true);
	if ($return) {
		return favorite_get_data(array('favid'=>$favid));
	}else {
		return $favid;
	}
}

/**
 * 取消收藏
 * @param mixed $condition
 */
function favorite_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('favorite')->where($condition)->delete();
	}
}

/**
 * 更新收藏
 * @param mixed $condition
 * @param array $data
 */
function favorite_update_data($condition,$data){
	return M('favorite')->where($condition)->update($data);
}

/**
 * 获取收藏信息
 * @param mixed $condition
 */
function favorite_get_data($condition){
	return M('favorite')->where($condition)->getOne();
}

/**
 * 获取收藏数目
 * @param mixed $condition
 */
function favorite_get_num($condition){
	return M('favorite')->where($condition)->count();
}

/**
 * 获取收藏列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function favorite_get_list($condition, $num=20, $limit=0, $order=''){
	$limit = $num ? "$limit,$num" : ($limit ? $limit : '');
	!$order && $order = 'favid DESC';
	$favoritelist = M('favorite')->where($condition)->limit($limit)->order($order)->select();
	if ($favoritelist) {
		$datalist = array();
		foreach ($favoritelist as $list){
			$list['imageurl'] = $list['image'] ? image($list['image']) : '';
			$datalist[$list['favid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取收藏分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function favorite_get_page($condition, $page=1, $pagesize=20, $order=''){
	$limit = ($page-1) * $pagesize;
	return favorite_get_list($condition, $pagesize, $limit, $order);
}