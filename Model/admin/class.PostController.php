<?php
namespace Admin;
class PostController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	/**
	 * 文章列表
	 */
	public function showlist(){
		if ($this->checkFormSubmit()){
			$articleids = $_GET['id'];
			if (is_array($articleids) && !empty($articleids)) {
				$articleids = $articleids ? implode(',', $articleids) : 0;
				if($_GET['option'] == 'delete'){
					post_delete_data(array('id'=>array('IN', $articleids)));
					$this->showSuccess('delete_succeed');
				}
				
				if ($_GET['option'] == 'audit') {
					post_update_data(array('id'=>array('IN', $articleids)), array('status'=>0));
					$this->showSuccess('update_succeed');
				}
				
				if ($_GET['option'] == 'unaudit'){
					post_update_data(array('id'=>array('IN', $articleids)), array('status'=>-1));
					$this->showSuccess('update_succeed');
				}
			}else{
				$this->showError('no_select');
			}
		}else {
			global $G,$lang;
			$pagesize  = 30;
			$condition = array();
			$catid     = intval($_GET['catid']);
			$status    = intval($_GET['status']);
			$keyword   = trim($_GET['keyword']);
			if ($catid) $condition['catid'] = array('IN',$catid);
			if ($status) $condition['status'] = $status;
			if ($keyword) $condition['title'] = array('LIKE',$keyword);
			$totalnum  = post_get_num($condition);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$postlist = post_get_page($condition, min(array($G['page'], $pagecount)), $pagesize, 'id DESC');
			$pages = $this->showPages($G['page'], $pagecount, $totalnum,"catid=$catid&status=$status&keyword=$keyword",1);
			$categoryoptions = category_get_options(0, $catid, 0, 'article');
			$categorylist = category_get_all('article');
			if ($categorylist) {
				$datalist = array();
				foreach ($categorylist as $list){
					$datalist[$list['catid']] = $list;
				}
				$categorylist = $datalist;
				unset($datalist,$list);
			}
			include template('post_list');
		}
	}
	
	/**
	 * 发布文章
	 */
	public function publish(){
		if ($this->checkFormSubmit()){
			$this->save();
		}else {
			global $G,$lang,$config;
			$catid = isset($_GET['catid']) ? intval($_GET['catid']) : 0;
			$categoryoptions = category_get_options(0, $catid, 1, 'article');
			$type = in_array($_GET['type'], array('image','video')) ? $_GET['type'] : 'article';
			$article['picurl']  = image(0);
			$article['from']    = $G['setting']['sitename'];
			$article['fromurl'] = $G['setting']['siteurl'];
			$article['author']  = $this->username;
			include template('post_form');
		}
	}
	
	/**
	 * 保存文章
	 */
	private function save(){
		global $G;
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			header('Allow: POST');
			header('HTTP/1.1 405 Method Not Allowed');
			header('Content-Type: text/plain');
			exit;
		}
		
		$newpost = $_GET['newpost'];
		$content = $_GET['content'];
		if (is_array ( $newpost )) {
			$newpost['uid'] = $this->uid;
			$newpost['username'] = $this->username;
			$newpost['pubtime']  = TIMESTAMP;
 			$newpost['modified'] = TIMESTAMP;
			$newpost['author']   = $newpost['author'] ? $newpost['author'] : $this->username;
			$newpost['from']     = isset($newpost['from']) ? $newpost['from'] : $G['setting']['sitename'];
			$newpost['fromurl']  = isset($newpost['fromurl']) ? $newpost['fromurl'] : $G['setting']['siteurl'];
			$newpost['tags'] = $newpost['tags'] ? $newpost['tags'] : '';
			$newpost['allowcomment'] = intval($newpost['allowcomment']);
			if (!$newpost['summary']) {
				$newpost['summary'] = cutstr(stripHtml($content), 300);
			}
			$newpost['summary'] = str_replace('&amp;', '&', $newpost['summary']);
			$newpost['summary'] = str_replace('&nbsp;', '', $newpost['summary']);
			$newpost['summary'] = str_replace('　', '', $newpost['summary']);
			$newpost['summary'] = preg_replace('/\s/', '', $newpost['summary']);
			
			$id = post_add_data($newpost);
			if ($newpost['type'] == 'article') {
				if ($content) {
					$contentlist = preg_split('/<hr class=\"ke-pagebreak\" style=\"page-break-after: always;\">/', $content);
					foreach ($contentlist as $key=>$value){
						$data = array(
								'aid'=>$id,
								'content'=>$content,
								'pageorder'=>$key+1,
								'dateline'=>time()
						);
						post_add_content($data);
					}
				}
			}
			
			if ($newpost['type'] == 'image'){
				$piclist = $_GET['piclist'];
				if ($piclist) {
					foreach ($piclist as $key=>$pic){
						if (is_numeric($key) && $key>0){
							$pic['id'] = $key;
						}
						image_add_data($pic, 0, 1);
					}
				}
			}
			
			if ($newpost['type'] == 'video'){
				$videourl = trim ( $_GET['videourl'] );
				if ($videourl) {
					$videodata = \Core\ParseVideoUrl::ParseUrl ($videourl);
					post_delete_contents(array('aid'=>$id));
					post_add_content(array(
							'aid'=>$id,
							'content'=>serialize($videodata),
							'pageorder'=>1,
							'dateline'=>time()
					));
					if (!$newpost['pic']) {
						post_update_data(array('id'=>$id), array('pic'=>$videodata['img']));
					}
				}
			}
			
			$links = array (
					array (
							'text' => 'continue_publish',
							'url' => '/?m=admin&c=post&a=publish&type='.$newpost['type'].'&catid='.$newpost['catid']
					),
					array (
							'text'=>'view',
							'url'=>'/?m=post&c=detail&id='.$id,
							'target'=>'_blank'
					),
					array(
							'text'=>'go_home',
							'url'=>'/?m=admin'
					)
			);
			$this->showSuccess('publish_succeed','', $links,'',true);
		} else {
			$this->showError('undefined_error');
		}
	}
	
	/**
	 * 编辑文章
	 */
	public function edit(){
		if ($this->checkFormSubmit()){
			$this->update();
		}else {
			global $G,$lang;
			$id = intval($_GET['id']);
			$article = post_get_data(array('id'=>$id));
			if (in_array($article['type'], array('image','video'))){
				$type = $article['type'];
			}else {
				$type = 'article';
			}
			$categoryoptions = category_get_options(0, $article['catid'], 1, 'article');
			
			if ($article['type'] == 'article'){
				$content = post_get_content($id);
			}
			
			if ($article['type'] == 'image'){
				$piclist = image_get_list($id, 'article');
			}
			
			if ($article['type'] == 'video'){
				$videodata = post_get_content($id);
				$videodata = unserialize($videodata);
			}
			include template('post_form');
		}
	}
	
	/**
	 * 更新文章
	 */
	public function update(){
		global $G;
		if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
			header('Allow: POST');
			header('HTTP/1.1 405 Method Not Allowed');
			header('Content-Type: text/plain');
			exit;
		}

		$id = intval($_GET['id']);
		$newpost = $_GET['newpost'];
		$content = $_GET['content'];
		if (is_array ( $newpost )) {
			$newpost['modified'] = TIMESTAMP;
			$newpost['author']   = $newpost['author'] ? $newpost['author'] : $this->username;
			$newpost['from']     = isset($newpost['from']) ? $newpost['from'] : $G['setting']['sitename'];
			$newpost['fromurl']  = isset($newpost['fromurl']) ? $newpost['fromurl'] : $G['setting']['siteurl'];
			$newpost['tags'] = $newpost['tags'] ? $newpost['tags'] : '';
			$newpost['allowcomment'] = intval($newpost['allowcomment']);
			if (!$newpost['summary']) {
				$newpost['summary'] = cutstr(stripHtml($content), 300);
			}
			$newpost['summary'] = str_replace('&amp;', '&', $newpost['summary']);
			$newpost['summary'] = str_replace('&nbsp;', '', $newpost['summary']);
			$newpost['summary'] = str_replace('　', '', $newpost['summary']);
			$newpost['summary'] = preg_replace('/\s/', '', $newpost['summary']);
			//$newpost['summary'] = preg_replace('/[\n|\r]/', '', $newpost['summary']);
			
			post_update_data(array('id'=>$id), $newpost);
			if ($newpost['type'] == 'article') {
				post_delete_contents(array('aid'=>$id));
				if ($content) {
					$contentlist = preg_split('/<hr class=\"ke-pagebreak\" style=\"page-break-after: always;\">/', $content);
					foreach ($contentlist as $key=>$value){
						$data = array(
								'aid'=>$id,
								'content'=>$content,
								'pageorder'=>$key+1,
								'dateline'=>time()
						);
						post_add_content($data);
					}
				}
			}
			
			if ($newpost['type'] == 'image'){
				$piclist = $_GET['piclist'];
				if ($piclist) {
					foreach ($piclist as $key=>$pic){
						if (is_numeric($key) && $key>0){
							$pic['id'] = $key;
						}
						image_add_data($pic, 0, 1);
					}
				}
			}
			
			if ($newpost['type'] == 'video'){
				$videourl = trim ( $_GET['videourl'] );
				if ($videourl) {
					post_delete_contents(array('aid'=>$id));
					$videodata = \Core\ParseVideoUrl::ParseUrl ($videourl);
					post_add_content(array(
							'aid'=>$id,
							'content'=>serialize($videodata),
							'pageorder'=>1,
							'dateline'=>time()
					));
					if (!$newpost['pic']) {
						post_update_data(array('id'=>$id), array('pic'=>$videodata['img']));
					}
				}
			}
		
			$links = array (
					array (
							'text' => 'reedit',
							'url' => '/?m=admin&c=post&a=edit&id='.$id
					),
					array (
							'text' => 'view',
							'url' => '/?m=post&c=detail&id=' . $id,
							'target' => '_blank'
					)
			);
			$this->showSuccess('modi_succeed','', $links,'',true);
		} else {
			$this->showError('undefined_error');
		}
	}
}