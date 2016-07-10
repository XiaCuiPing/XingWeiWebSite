<?php
namespace Admin;
class ShopController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		if ($this->checkFormSubmit()) {
			$shopids = $_GET['shopid'];
			if ($shopids && is_array($shopids)) {
				$shopids = implodeids($shopids);
				shop_delete_data(array('shopid'=>array('IN', $shopids)));
				$this->showSuccess('delete_succeed');
			}else {
				$this->showError('no_select');
			}
		}else {
			global $G,$lang;
			$pagesize = 30;
			$condition = array();
			$catid = intval($_GET['catid']);
			$keyword = trim($_GET['keyword']);
			if ($catid) $condition['catid'] = $catid;
			if ($keyword) $condition['shopname'] = array('LIKE', $keyword);
			$totalnum = shop_get_num($condition);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$shoplist = shop_get_page($condition, $G['page'], $pagesize);
			if ($shoplist) {
				$shopids = $uids = $datalist = array();
				foreach ($shoplist as $shop){
					array_push($shopids, $shop['shopid']);
					array_push($uids, $shop['uid']);
					$datalist[$shop['shopid']] = $shop;
				}
				
				$shoplist = $datalist;
				unset($datalist,$shop);
				
				if ($shopids) {
					$piclist = shop_get_image_list($shopids);
					foreach ($piclist as $pic){
						$shoplist[$pic['dataid']]['image'] = $pic['image'];
						$shoplist[$pic['dataid']]['thumb'] = $pic['thumb'];
					}
					
					$userlist = member_get_list(array('uid'=>array('IN', implodeids($uids))), $pagesize);
				}
				unset($shopids, $uids, $piclist, $pic);
			}
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid&keyword=$keyword", 1);
			$categoryoptions = category_get_options(0,$catid,0,'shop');
			include template('shop_list');
		}
	}
}