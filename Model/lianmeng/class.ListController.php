<?php
namespace Lianmeng;
class ListController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$catid = intval($_GET['catid']);
		$province = trim($_GET['province']);
		$condition = array();
		if ($catid) $condition['catid'] = $catid;
		if ($province) $condition['province'] = array('LIKE',$province);
		//$category = category_get_data(array('catid'=>$catid));
		
		$pagesize = 20;
		$totalnum = business_get_num($condition);
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$businesslist = business_get_page($condition, $G['page'], $pagesize, 'id ASC');
		$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid&province=$province");
		
		
		//获取分类列表
		$categorylist = category_get_list('shop');
		//获取省份列表
		
		$provincelist = district_get_list(array('fid'=>0), 100);
		
		//$G['title'] = $category['cname'];
		$G['title'] = '联盟商家';
		include template('list');
	}
}