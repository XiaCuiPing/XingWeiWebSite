<?php
namespace Post;
class ListController extends BaseController{
	public function index(){
		global $G,$lang;
		$pagesize = 20;
		$catid = intval($_GET['catid']);
		$where = $catid ? array('catid'=>$catid) : '';
		$totalnum = post_get_num($where);
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$articlelist = post_get_page($where, $G['page'], $pagesize);
		$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid");
		
		$category = category_get_data($catid);
		$categorylist = category_get_list('article');
		$G['title'] = $category['cname'];
		if ($category['template']){
			include template($category['template']);
		}else {
			include template('list');
		}
	}
}