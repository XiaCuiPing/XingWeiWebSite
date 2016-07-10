<?php
namespace Member;
class FavoriteController extends BaseController{
	public function index(){
		global $G,$lang;
		$pagesize  = 20;
		$totalnum  = $this->t('favorite')->where(array('uid'=>$this->uid))->count();
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$favoritelist = $this->t('favorite')->where(array('uid'=>$this->uid))->page($G['page'],$pagesize)->select();
		$pages = $this->showPages($G['page'], $pagecount, $totalnum);
		include template('favorite');
	}
}