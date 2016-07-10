<?php
namespace Admin;
class CommentController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if (!empty($delete) && is_array($delete)){
				$deleteids = implode(',', $delete);
				comment_delete_data(array('cid'=>array('IN', $deleteids)));
			}
			
			$this->showSuccess('delete_succeed');
		}else {
			global $G, $lang;
			$pagesize = 30;
			$condition = array();
			$totalnum    = comment_get_num($condition);
			$pagecount   = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$commentlist = comment_get_page($condition, $G['page'], $pagesize, 'cid DESC', 'Y-m-d H:i');
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, "", true);
			include template('comment_list');
		}
	}
}