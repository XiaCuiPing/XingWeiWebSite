<?php
namespace Admin;
class CategoryController extends BaseController{
	protected $type = 'article';
	
	public function index(){
		$this->showlist();
	}
	
	protected function delete($deleteids){
		if ($deleteids) {
			category_delete_data(array('catid'=>array('IN', $deleteids)));
		}
	}
	
	/**
	 * 分类列表
	 */
	public function showlist(){
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				$deleteids = implode(',', $delete);
				$this->delete($deleteids);
			}
			
			$categorylist = $_GET['categorylist'];
			if ($categorylist && is_array($categorylist)){
				foreach ($categorylist as $catid=>$category){
					$category['displayorder'] = intval($category['displayorder']);
					$category['enable'] = intval($category['enable']);
					$category['available'] = intval($category['available']);
					category_update_data(array('catid'=>$catid), $category);
				}
			}
			
			$categorynew = $_GET['categorynew'];
			if ($categorynew && is_array($categorynew)){
				foreach ($categorynew as $k=>$category){
					if ($category['cname']) {
						$category['type'] = $this->type;
						$category['displayorder'] = intval($category['displayorder']);
						$category['enable'] = intval($category['enable']);
						$category['available'] = intval($category['available']);
						category_add_data($category);
					}
				}
			}
			
			category_update_cache($this->type);
			$this->showSuccess('update_succeed');
		}else {
			global $G,$lang;
			category_update_cache($this->type);
			$categorylist = category_get_list($this->type, false, true);
			include template('category_list');
		}
	}
	
	/**
	 * 编辑分类
	 */
	public function edit(){
		$catid = intval($_GET['catid']);
		if ($this->checkFormSubmit()){
			$categorynew = $_GET['categorynew'];
			if ($categorynew && is_array($categorynew)) {
				category_update_data(array('catid'=>$catid), $categorynew);
				category_update_cache($this->type);
				$this->showSuccess('update_succeed');
			}
		}else {
			global $G,$lang;
			$category = category_get_data(array('catid'=>$catid));
			$categorylist = category_get_list($this->type);
			include template('category_form');
		}
	}
	
	public function setimage(){
		$catid = intval($_GET['catid']);
		$image = trim($_GET['image']);
		category_update_data(array('catid'=>$catid), array('image'=>$image));
		$this->showAjaxReturn('success');
	}
}