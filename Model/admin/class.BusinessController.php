<?php
namespace Admin;
class BusinessController extends BaseController{
	public function index(){
		$this->showlist();
		
	}
	
	/**
	 * 显示商家列表
	 */
	public function showlist(){
		global $G,$lang;
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				$deleteids = implodeids($delete);
				business_delete_data(array('id'=>array('IN', $deleteids)));
				business_delete_desc(array('businessid'=>array('IN', $deleteids)));
				$this->showSuccess('delete_succeed');
			}else {
				$this->showSuccess('no_select');
			}
		}else {
			$pagesize = 20;
			$totalnum = business_get_num(0);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$businesslist = business_get_page(0, $G['page'], $pagesize, 'id ASC');
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, '', 1);
			include template('business_list');
		}
	}
	
	/**
	 * 添加联盟商家
	 */
	public function add(){
		global $G,$lang;
		if ($this->checkFormSubmit()){
			$businessnew = $_GET['businessnew'];
			if ($businessnew['name']){
				$businessnew['uid'] = $this->uid;
				$businessnew['dateline'] = TIMESTAMP;
				$id = business_add_data($businessnew);
				
				$description = $_GET['description'];
				business_add_desc(array('businessid'=>$id, 'description'=>$description));
				$this->showSuccess('save_succeed');
			}else {
				$this->showError('undefined_action');
			}
		}else {
			$categoryoptions = category_get_options(0, 0, 1, 'shop');
			include template('business_form');
		}
	}
	
	/**
	 * 编辑联盟商家
	 */
	public function edit(){
		global $G,$lang;
		$id = intval($_GET['id']);
		if ($this->checkFormSubmit()){
			$businessnew = $_GET['businessnew'];
			if ($businessnew['name']){
				business_update_data(array('id'=>$id), $businessnew);
				
				$description = $_GET['description'];
				business_update_desc(array('businessid'=>$id), array('description'=>$description));
				$this->showSuccess('update_succeed');
			}
			else {
				$this->showError('undefined_action');
			}
		}else {
			$business = business_get_data(array('id'=>$id));
			$description = business_get_desc($id);
			$categoryoptions = category_get_options(0, $business['catid'], 1, 'shop');
			include template('business_form');
		}
	}
}