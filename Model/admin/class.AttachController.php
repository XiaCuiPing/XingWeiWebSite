<?php
namespace Admin;
class AttachController extends BaseController{
	public function index(){
		global $G,$lang;
		if($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				$deleteids = implode(',', $delete);
				attach_delete_data("attachid IN($deleteids)");
				$this->showSuccess('delete_succeed');
			}else {
				$this->showError('no_select');
			}

		}else {
			$pagesize   = 20;
			$totalnum   = attach_get_num(0);
			$pagecount  = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$attachlist = attach_get_page(0, $G['page'], $pagesize);
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, '', true);
			include template('attach_list');
		}
	}
}