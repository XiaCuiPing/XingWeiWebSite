<?php
namespace Admin;
class PhotoController extends BaseController{
	public function index(){
		global $G,$lang;
		if($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if($delete && is_array($delete)){
				$deleteids = implodeids($delete);
				photo_delete_data("photoid IN($deleteids)");
				$this->showSuccess('delete_succeed');
			}else {
				$this->showError('no_select');
			}
		}else {
			$pagesize  = 20;
			$totalnum  = photo_get_num();
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$photolist = photo_get_page(0, min(array($G['page'], $pagecount)), $pagesize, 'photoid DESC');
			if ($photolist) {
				$uids = $comma = '';
				foreach ($photolist as $list){
					$uids.= $comma.$list['uid'];
					$comma = ',';
				}
				
				$userlist = member_get_list(array('uid'=>array('IN', $uids)));
				unset($uids,$comma,$list);
			}
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, 0, 1);
			include template('photo_list');
		}
	}
}