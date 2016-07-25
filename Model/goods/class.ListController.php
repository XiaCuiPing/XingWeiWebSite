<?php
namespace Goods;
class ListController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$pagesize = 10;
		$catid = intval($_GET['catid']);
		$totalnum = goods_get_num(array('catid'=>$catid));
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$goodslist['list'] = goods_get_page(array('catid'=>$catid), $G['page'], $pagesize);
		$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid", 1);
		$G['title'] = '商品列表';
		include template('list');
	}
}