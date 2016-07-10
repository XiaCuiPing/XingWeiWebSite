<?php
namespace Admin;
class PageController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		global $G, $lang;
		if ($this->checkFormSubmit()){
			//删除页面
			$delete = $_GET['delete'];
			if (!empty($delete) && is_array($delete)){
				$deleteids = implode(',', $delete);
				page_delete_data(array('pageid'=>array('IN', $deleteids)));
			}
			//更新页面
			$neworder  = $_GET['neworder'];
			if ($neworder && is_array($neworder)){
				foreach ($neworder as $pageid=>$displayorder){
					page_update_data(array('pageid'=>$pageid), array('displayorder'=>$displayorder));
				}
			}
			$this->showSuccess('update_succeed');
		}else {
			$pagesize  = 20;
			$catid     = intval($_GET['catid']);
			$condition = $catid ? "catid='$catid'" : 'catid>0';
			$totalnum  = page_get_num($condition);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$pagelist  = page_get_page($condition, $G['page'], $pagesize);
			$pages = $this->showPages($G['page'], $pagecount,$totalnum,"catid=$catid");
			$categorylist = page_get_list(array('catid'=>0), 0);
			include template('page_list');
		}
		
	}
	
	public function publish(){
		global $G, $lang;
		if ($this->checkFormSubmit()) {
			$newpage = $_GET['newpage'];
			$newpage['pubtime']  = TIMESTAMP;
			$newpage['modified'] = TIMESTAMP;
			if (!$newpage['summary']) {
				$newpage['summary'] = cutstr(stripHtml($newpage['body']), 400);
			}
			page_add_data($newpage);
			$this->showSuccess('save_succeed');
		}else{
			$categorylist = page_get_list(array('catid'=>0));
			$editorname = 'newpage[body]';
			include template('page_form');
		}
	}

	public function edit(){
		global $G, $lang;
		$pageid = intval($_GET['pageid']);
		if($this->checkFormSubmit()){
			$newpage = $_GET['newpage'];
			$newpage['modified'] = TIMESTAMP;
			if (!$newpage['summary']) {
				$newpage['summary'] = cutstr(stripHtml($newpage['body']), 400);
			}
			page_update_data(array('pageid'=>$pageid), $newpage);
			$this->showSuccess('modi_succeed');
		}else {
			$page = page_get_data(array('pageid'=>$pageid));
			$categorylist = page_get_list(array('catid'=>0));
			$editorname = 'newpage[body]';
			$editorcontent = $page['body'];
			include template('page_form');
		}
	}
	
	/**
	 * 页面分类管理
	 */
	public function category(){
		global $G,$lang;
		if($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if (!empty($delete) && is_array($delete)){
				$deleteids = implode(',', $delete);
				page_delete_data(array('pageid'=>array('IN', $deleteids)));
				page_delete_data(array('catid'=>array('IN', $deleteids)));
			}
			
			$newclass  = $_GET['newclass'];
			if (!empty($newclass) && is_array($newclass)){
				foreach ($newclass['title'] as $key=>$title){
					$pagedata = array(
							'title'=>$title,
							'type'=>'category',
							'displayorder'=>$newclass['displayorder'][$key]
					);
					if($newclass['pageid'][$key]>0){
						if($title){
							page_update_data(array('pageid'=>$newclass['pageid'][$key]), $pagedata);
						}
					}else {
						if($title)page_add_data($pagedata);
					}
				}
			}
			$this->showSuccess('save_succeed');
		}else {
			$categorylist = page_get_list(array('catid'=>0), 0);
			include template('page_category');
		}
	}
}