<?php
/**
 * 添加文章信息
 * @param array $data
 * @param boolean $return
 */
function post_add_data($data,$return=FALSE){
	$id = M('post_title')->insert($data, true);
	if ($return){
		return post_get_data(array('id'=>$id));
	}else {
		return $id;
	}
}

/**
 * 删除文章信息
 * @param mixed $condition
 * @return boolean
 */
function post_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		$postlist = M('post_title')->where($condition)->select();
		if ($postlist) {
			$deleteids = $comma = '';
			foreach ($postlist as $list){
				$deleteids.= $comma.$list['id'];
				$comma = ',';
			}
			
			if ($deleteids) {
				post_delete_contents(array('aid'=>array('IN', $deleteids)));
			}
		}
		return M('post_title')->where($condition)->delete();
	}
}

/**
 * 更新文章信息
 * @param mixed $condition
 * @param array $data
 */
function post_update_data($condition,$data){
	return M('post_title')->where($condition)->update($data);
}

/**
 * 更新文章点击数
 * @param int $id
 * @param string $reduce
 */
function post_update_viewnum($id, $reduce=FALSE){
	if ($reduce){
		post_update_data(array('id'=>$id), "viewnum=viewnum-1");
	}else {
		post_update_data(array('id'=>$id), "viewnum=viewnum+1");
	}
}

/**
 * 更新文章评论数
 * @param int $id
 * @param string $reduce
 */
function post_update_commentnum($id, $reduce=FALSE){
	if ($reduce){
		post_update_data(array('id'=>$id), "commentnum=commentnum-1");
	}else {
		post_update_data(array('id'=>$id), "commentnum=commentnum+1");
	}
}

/**
 * 获取文章信息
 * @param mixed $condition
 */
function post_get_data($condition){
	$post = M('post_title')->where($condition)->getOne();
	if ($post) {
		$post['picurl'] = image($post['pic']);
		return $post;
	}else {
		return array();
	}
}

/**
 * 获取文章数量
 * @param mixed $condition
 */
function post_get_num($condition){
	return M('post_title')->where($condition)->count();
}

/**
 * 获取文章列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function post_get_list($condition,$num=20,$limit=0,$order=''){
	$limit = $num ? "$limit,$num" : ($limit ? $limit : '');
	!$order && $order = 'id DESC';
	$postlist = M('post_title')->where($condition)->limit($limit)->order($order)->select();
	if ($postlist){
		$datalist = array();
		foreach ($postlist as $list){
			$list['picurl'] = $list['pic'] ? image($list['pic']) : '';
			$list['url'] = getSiteURL().'/?m=post&c=detail&id='.$list['id'];
			array_push($datalist, $list);
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取最新文章列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 */
function post_get_new($condition, $num=20, $limit=0){
	return post_get_list($condition, $num, $limit);
}

/**
 * 获取最热文章列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 */
function post_get_hot($condition, $num=20, $limit=0){
	return post_get_list($condition, $num, $limit, 'viewnum DESC');
}

/**
 * 获取文章分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 */
function post_get_page($condition,$page=1,$pagesize=20,$order=''){
	$limit = ($page - 1)*$pagesize;
	return post_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 添加文章内容
 * @param array $data
 */
function post_add_content($data){
	return M('post_content')->insert($data);
}

/**
 * 添加文章内容
 * @param array $contents
 */
function post_add_contents($contents){
	if (!is_array($contents)){
		return false;
	}else {
		foreach ($contents as $content){
			M('post_content')->insert($content);
		}
		return true;
	}
}

/**
 * 删除文章内容
 * @param mixed $condition
 */
function post_delete_contents($condition){
	if (!$condition){
		return false;
	}else {
		return M('post_content')->where($condition)->delete();
	}
}

/**
 * 获取文章内容列表
 * @param integer $aid
 */
function post_get_contents($aid){
	$contentlist = M('post_content')->where(array('aid'=>$aid))->order('pageorder','ASC')->select();
	if ($contentlist) {
		return $contentlist;
	}else {
		return array();
	}
}

/**
 * 获取文章内容
 * @param integer $aid
 */
function post_get_content($aid){
	$content = '';
	$contentlist = post_get_contents($aid);
	if ($contentlist) {
		foreach ($contentlist as $list){
			$content.= $list['content'];
		}
	}
	return $content;
}