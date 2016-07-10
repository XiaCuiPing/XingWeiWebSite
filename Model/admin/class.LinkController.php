<?php
namespace Admin;
class LinkController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				$deleteids = implodeids($delete);
				link_delete_data(array('linkid'=>array('IN', $deleteids)));
			}
			
			$linklist = $_GET['linklist'];
			if ($linklist && is_array($linklist)) {
				foreach ($linklist as $linkid=>$link){
					if ($link['title']) {
						link_update_data(array('linkid'=>$linkid), $link);
					}
				}
			}
			
			$newlink = $_GET['newlink'];
			if ($newlink && is_array($newlink)) {
				foreach ($newlink as $link) {
					if ($link['title']) {
						link_add_data($link);
					}
				}
			}
			
			$this->updatecache();
			$this->showSuccess('update_succeed');
			
		}else {
			global $G,$lang;
			$categorylist = link_get_list(array('catid'=>0), 0);
			$linklist = link_get_list(array('catid'=>array('>',0)), 0);
			include template('link_list');
		}
	}
	
	private function updatecache(){
		$categorylist = link_get_list(array('catid'=>0), 0);
		$linklist = link_get_list(array('catid'=>array('>',0)), 0);
		cache('link_cateogory', $categorylist);
		cache('links', $linklist);
	}
}