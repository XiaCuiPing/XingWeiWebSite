<?php
/**
 * 获取分类列表
 * @param array $data
 * @param string $return
 */
function category_add_data($data, $return=FALSE){
	$catid = M('category')->insert($data, true);
	if ($return) {
		return category_get_data(array('catid'=>$catid));
	}else {
		return $catid;
	}
}

/**
 * 删除分类信息
 * @param mixed $condition
 * @return boolean
 */
function category_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('category')->where($condition)->delete();
	}
}

/**
 * 更新分类信息
 * @param mixed $condition
 * @param array $data
 */
function category_update_data($condition, $data){
	return M('category')->where($condition)->update($data);
}

/**
 * 获取分类信息
 * @param mixed $condition
 */
function category_get_data($condition){
	return M('category')->where($condition)->getOne();
}

/**
 * 更新分类缓存
 * @param string $type
 */
function category_update_cache($type='article'){
	$categorylist = category_get_all($type, false, false);
	cache('category_'.$type, $categorylist);
}

/**
 * 获取分类列表,无格式化
 * @param string $type
 * @param bool $fromcache
 * @param bool $all
 */
function category_get_all($type='article', $fromcache=true, $all=false){
	if ($fromcache) {
		$categorylist = cache('category_'.$type);
	}else {
		$condition = array('type'=>$type);
		if (!$all) $condition['available'] = 1;
		$categorylist = M('category')->where($condition)->order('displayorder ASC,catid ASC')->select();
	}
	if ($categorylist && is_array($categorylist)){
		return $categorylist;
	}else {
		return array();
	}
}

/**
 * 获取分类列表，已格式化
 * @param string $type
 * @param bool $fromcache
 * @param bool $all
 */
function category_get_tree($type='article', $fromcache=true, $all=false){
	$categorylist = category_get_all($type, $fromcache, $all);
	if ($categorylist) {
		$datalist = array();
		foreach ($categorylist as $list){
			$datalist[$list['fid']][$list['catid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取分类列表，已格式化
 * @param string $type
 * @param bool $fromcache
 * @param bool $all
 */
function category_get_list($type='article', $fromcache=true, $all=false){
	$categorylist = category_get_all($type, $fromcache, $all);
	if ($categorylist) {
		$datalist = array();
		foreach ($categorylist as $list){
			$datalist[$list['fid']][] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取子分类列表
 * @param number $fid
 * @param string $type
 */
function category_get_childs($fid=0, $type='article'){
	$childs = array();
	$categorylist = category_get_all($type);
	if ($categorylist) {
		foreach ($categorylist as $list){
			if ($list['fid'] == $fid){
				array_push($childs, $list);
			}
		}
	}
	return $childs;
}

/**
 * 获取分类选项列表
 * @param number $fid
 * @param number $selected
 * @param bool   $showenable
 * @param string $type
 */
function category_get_options($fid=0, $selected=0, $showenable=0, $type='article'){
	static $separater;
	$optionlist = '';
	$separater2 = $separater;
	$categorylist = category_get_all($type);
	if ($categorylist && is_array($categorylist)) {
		foreach ($categorylist as $list){
			if ($list['fid'] == $fid){
				$s = $list['catid'] == $selected ? ' selected="selected"' : '';
				$a = $showenable ? ($list['enable'] ? '' : ' disabled') : '';
				$optionlist.= '<option value="'.$list['catid'].'"'.$s.$a.'>'.$separater.$list['cname'].'</option>';
				$separater.= '|--';
				//$optionlist.= category_get_options($list['catid'], $selected, $showenable, $type);
				$separater = $separater2;
			}
		}
	}
	return $optionlist;
}

function category_set_options(&$optionlist, $fid=0, $selected=0, $showenable=0, $type='article'){
	static $separater;
	$optionlist = '';
	$separater2 = $separater;
	$categorylist = category_get_all($type);
	if ($categorylist && is_array($categorylist)) {
		foreach ($categorylist as $list){
			if ($list['fid'] == $fid){
				$s = $list['catid'] == $selected ? ' selected="selected"' : '';
				$a = $showenable ? ($list['enable'] ? '' : ' disabled') : '';
				$optionlist.= '<option value="'.$list['catid'].'"'.$s.$a.'>'.$separater.$list['cname'].'</option>';
				$separater.= '|--';
				//$optionlist.= category_get_options($list['catid'], $selected, $showenable, $type);
				$separater = $separater2;
				//category_set_options($optionlist, $list['catid'], $selected, $showenable, $type);
			}
		}
	}
	//return $optionlist;
}

function category_get_paths($type='article',$catid=0){
	
}