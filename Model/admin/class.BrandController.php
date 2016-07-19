<?php
namespace Admin;
class BrandController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()) {
			$brandids = $_GET['id'];
			if ($brandids && is_array($brandids)){
				$brandids = implodeids($brandids);
				switch ($_GET['option']) {
					case 'delete':
						brand_delete_data(array('id'=>array('IN', $brandids)));
						break;
					default:;
				}
			}
			
			$brandlist = $_GET['brandlist'];
			if ($brandlist && is_array($brandlist)){
				foreach ($brandlist as $id=>$brand){
					brand_update_data(array('id'=>$id), $brand);
				}
			}
			
			$brandnew = $_GET['brandnew'];
			if ($brandnew && is_array($brandnew)){
				foreach ($brandnew as $brand){
					if ($brand['name']) brand_add_data($brand);
				}
			}
			$this->showSuccess('save_succeed');
		}else {
			global $G,$lang;
			$pagesize = 20;
			$totalnum = brand_get_num(0);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$brandlist = brand_get_page(0, $G['page'], $pagesize);
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, '', 1);
			
			include template('brand_list');
		}
	}
	
	/**
	 * 设置品牌logo
	 * 
	 */
	public function setimage(){
		$id = intval($_GET['id']);
		$image = trim($_GET['image']);
		brand_update_data(array('id'=>$id), array('logo'=>$image));
		$this->showAjaxReturn(array('id'=>$id));
	}
}