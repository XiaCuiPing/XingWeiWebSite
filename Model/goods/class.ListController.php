<?php
namespace Goods;
class ListController extends BaseController{
	public function index(){
		global $G,$lang;
		$catid = intval($_GET['catid']);
		$brand = trim($_GET['brand']);
		$condition = array();
		if ($catid) $condition['catid'] = $catid;
		if ($brand) $condition['name'] = array('LIKE', htmlspecialchars($brand));
		
		$pagesize = 15;
		$totalnum = goods_get_num($condition);
		$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
		$goodslist['list'] = goods_get_page($condition, $G['page'], $pagesize);
		$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid&brand=$brand");
		
		//获取同级分类
		if ($catid) {
			$category = category_get_data(array('catid'=>$catid));
			$categorylist = category_get_childs($category['fid'], 'goods');
		}else {
			$categorylist = category_get_childs(0, 'goods');
		}
		//获取品牌列表
		$brandlist = brand_get_list(0, 20);
		
		$G['title'] = '商品列表';
		include template('list');
	}
}