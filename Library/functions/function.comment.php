<?php
/**
 * 添加评论
 * @param array $data
 * @param string $return
 * @return mixed
 */
function comment_add_data($data, $return=FALSE){
	$cid = M('comment')->insert($data, true);
	if ($return){
		return comment_get_data(array('cid'=>$cid));
	}else {
		return $cid;
	}
}

/**
 * 更新评论信息
 * @param mixed $condition
 * @param mixed $data
 */
function comment_update_data($condition, $data){
	return M('comment')->where($condition)->update($data);
}

/**
 * 删除评论
 * @param mixed $condition
 */
function comment_delete_data($condition){
	if (!$condition){
		return false;
	}else {
		return M('comment')->where($condition)->delete();
	}
}

/**
 * 获取单条评论列表
 * @param mixed $condition
 */
function comment_get_data($condition){
	$comment = M('comment')->where($condition)->selectOne();
	if ($comment) {
		$comment['avatar'] = avatar($comment['uid']);
		$comment['ravatar'] = avatar($comment['ruid']);
		return $comment;
	}else {
		return $comment;
	}
}

/**
 * 获取评论数量
 * @param mixed $condition
 */
function comment_get_num($condition){
	return M('comment')->where($condition)->count();
}

/**
 * 获取评论列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 * @param string $dateformat
 * @return unknown[]
 */
function comment_get_list($condition,$num=20, $limit=0, $order='', $dateformat='Y-m-d'){
	$order = $order ? $order : 'cid ASC';
	$commentlist = M('comment')->where($condition)->limit($limit, $num)->order($order)->select();
	if ($commentlist){
		$datalist = array();
		foreach ($commentlist as $list) {
			$list['avatar']  = avatar($list['uid']);
			$list['ravatar'] = avatar($list['ruid']);
			$list['pubtime'] = @date($dateformat, $list['dateline']);
			$datalist[$list['cid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取最新评论列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $dateformat
 */
function comment_get_new($condition, $num=20, $limit=0, $dateformat='Y-m-d'){
	return comment_get_list($condition, $num, $limit, 'cid DESC', $dateformat);
}

/**
 * 获取评论分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 * @param string $dateformat
 */
function comment_get_page($condition,$page=1, $pagesize=20, $order='', $dateformat='Y-m-d'){
	$limit = ($page - 1) * $pagesize;
	return comment_get_list($condition, $pagesize, $limit, $order, $dateformat);
}