<?php
namespace Member;
class PhotoController extends BaseController{
	public function index(){
		global $G,$lang;
		$pagesize  = 20;
		$totalnum  = photo_get_num(array('uid'=>$this->uid));
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$piclist = photo_get_page(array('uid'=>$this->uid), $G['page'], $pagesize);
		$pages = $this->showPages($G['page'], $pagecount, $totalnum);
		include template('photo');
	}
}