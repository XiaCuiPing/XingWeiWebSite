<?php
namespace App;
class ShopController extends BaseController{
	public function index(){
		
	}
	
	/**
	 * 获取店铺信息
	 */
	public function getdata(){
		$shopid = intval($_GET['shopid']);
		$shop = shop_get_data(array('shopid'=>$shopid));
		$this->showAppData($shop);
	}
	
	/**
	 * 获取店铺列表
	 */
	public function getlist(){
		$catid = intval($_GET['catid']);
		$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
		$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
		$condition = $catid ? array('catid'=>$catid) : '';
		
		$shoplist = shop_get_local_list($condition, $this->longitude, $this->latitude, $num, $limit);
		$this->showAppData(array_values($shoplist));
	}
	
	/**
	 * 获取店铺分页列表
	 */
	public function getpage(){
		$catid = intval($_GET['catid']);
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		$condition = $catid ? array('catid'=>$catid) : '';
		
		$shoplist = shop_get_local_page($condition, $this->longitude, $this->latitude, G('page'), $pagesize);
		$this->showAppData(array_values($shoplist));
	}
	
	/**
	 * 显示店铺详细页
	 */
	public function showdetail(){
		global $G,$lang;
		$shopid = intval($_GET['shopid']);
		$shop = shop_get_data(array('shopid'=>$shopid));
		include template('shop_detail');
	}
	
	public function showlist(){
		
	}
	
	/**
	 * 显示店铺分页列表
	 */
	public function showpage(){
		global $G,$lang;
		$catid = intval($_GET['catid']);
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		$condition = $catid ? array('catid'=>$catid) : '';
		
		$shoplist = shop_get_local_page($condition, $this->longitude, $this->latitude, G('page'), $pagesize);
		include template('shop_page');
	}
}