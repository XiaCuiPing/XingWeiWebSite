<?php
namespace Admin;
class CompanyController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		global $G,$lang;
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				$deleteids = implodeids($delete);
				company_delete_data(array('id'=>array('IN', $deleteids)));
				company_delete_desc(array('companyid'=>array('IN', $deleteids)));
				$this->showSuccess('delete_succeed');
			}else {
				$this->showSuccess('no_select');
			}
		}else {
			$pagesize = 20;
			$totalnum = company_get_num(0);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$companylist = company_get_page(0, $G['page'], $pagesize, 'id ASC');
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, '', 1);
			include template('company_list');
		}
	}
	
	/**
	 * 添加联盟企业
	 */
	public function add(){
		global $G,$lang;
		if ($this->checkFormSubmit()){
			$companynew = $_GET['companynew'];
			if ($companynew['name']){
				$companynew['uid'] = $this->uid;
				$companynew['dateline'] = TIMESTAMP;
				$id = company_add_data($companynew);
	
				$description = $_GET['description'];
				company_add_desc(array('companyid'=>$id, 'description'=>$description));
				$this->showSuccess('save_succeed');
			}else {
				$this->showError('undefined_action');
			}
		}else {
			$categoryoptions = category_get_options(0, 0, 1, 'shop');
			include template('company_form');
		}
	}
	
	/**
	 * 编辑联盟商家
	 */
	public function edit(){
		global $G,$lang;
		$id = intval($_GET['id']);
		if ($this->checkFormSubmit()){
			$companynew = $_GET['companynew'];
			if ($companynew['name']){
				company_update_data(array('id'=>$id), $companynew);
	
				$description = $_GET['description'];
				company_update_desc(array('companyid'=>$id), array('description'=>$description));
				$this->showSuccess('update_succeed');
			}
			else {
				$this->showError('undefined_action');
			}
		}else {
			$company = company_get_data(array('id'=>$id));
			$description = company_get_desc($id);
			$categoryoptions = category_get_options(0, $business['catid'], 1, 'shop');
			include template('company_form');
		}
	}
}