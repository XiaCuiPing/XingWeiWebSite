<?php
namespace Member;
class CommentController extends BaseController{
	public function index(){
		global $G,$lang;
		$pagesize  = 10;
		$totalnum  = comment_get_num(array('uid'=>$this->uid));
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$commentlist = comment_get_page(array('uid'=>$this->uid), $G['page'], $pagesize);
		$pages = $this->showPages($G['page'], $pagecount, $totalnum);
		include template('comment');
	}
}