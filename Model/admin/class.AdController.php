<?php
namespace Admin;
class AdController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		if ($this->checkFormSubmit()){
			$ids = $_GET['id'];
			if (!empty($ids) && is_array($ids)){
				$ids = implodeids($ids);
				switch ($_GET['option']) {
					case 'enable':
						ad_update_data("id IN($ids)", array('status'=>0));
						break;
					case 'disable':
						ad_update_data("id IN($ids)", array('status'=>-1));
						break;
					case 'unaudit':
						ad_update_data("id IN($ids)", array('status'=>-11));
						break;
					default:ad_delete_data("id IN($ids)");
				}
				$this->showSuccess('update_succeed');
			}else {
				$this->showError('no_select');
			}
		}else {
			global $G,$lang;
			$pagesize  = 30;
			$totalnum  = ad_get_num(0);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$adlist = ad_get_page(0, $G['page'], $pagesize, 'id ASC');
			$pages = $this->showPages($G['page'], $pagecount, $totalnum);
			include template('ad_list');
		}
	}
	
	public function publish(){
		if ($this->checkFormSubmit()){
			$adnew  = $_GET['adnew'];
			$addata = $_GET['addata'];
			if ($adnew['title']) {
				if ($adnew['type'] == 'image') {
					if ($filedata = photo_upload_data()){
						$addata['image']['image'] = image($filedata['image']);
					}
				}
				
				$adnew['data'] = serialize($addata[$adnew['type']]);
				ad_add_data($adnew);
				$this->showSuccess('save_succeed');
			}else {
				$this->showError('undefined_action');
			}
		}else {
			global $G,$lang;
			include template('ad_form');
		}
	}
	
	public function edit(){
		$id = intval($_GET['id']);
		if ($this->checkFormSubmit()){
			$adnew  = $_GET['adnew'];
			$addata = $_GET['addata'];
			if ($adnew['title']) {
				if ($adnew['type'] == 'image') {
					if ($filedata = photo_upload_data()){
						$addata['image']['image'] = image($filedata['image']);
					}
				}
			
				$adnew['data'] = serialize($addata[$adnew['type']]);
				ad_update_data(array('id'=>$id), $adnew);
				$this->showSuccess('update_succeed');
			}else {
				$this->showError('undefined_action');
			}	
		}else {
			global $G,$lang;
			$ad = ad_get_data(array('id'=>$id));
			$addata[$ad['type']] = $ad['data'];
			include template('ad_form');
		}
	}
}