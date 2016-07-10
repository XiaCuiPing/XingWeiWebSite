<?php
namespace Page;
use Core\Controller;
class IndexController extends Controller{
	public function index(){
		global $G,$lang;
		$pageid = intval($_GET['pageid']);
		$pagecontent = $this->t('page')->where(array('pageid'=>$pageid))->selectOne();
		$pagelist = $this->t('page')->order('displayorder','ASC')->select();
		if ($pagelist){
			$newlist = array();
			foreach ($pagelist as $list){
				$newlist[$list['pageid']] = $list;
			}
			$pagelist = $newlist;
			unset($newlist);
		}
		$category = $pagelist[$pagecontent['catid']];
		$G['title'] = $pagecontent['title'].' - '.$category['title'];
		include template('page');
	}
	
	public function getCategoryList(){
		$categorylist = $this->t('page')->where(array('catid'=>0))->order('displayorder ASC,pageid ASC')->select();
		if ($categorylist){
			$newlist = array();
			foreach ($categorylist as $list){
				$newlist[$list['pageid']] = $list;
			}
			$categorylist = $newlist;
		}else {
			$categorylist = array();
		}
		return $categorylist;
	}
}