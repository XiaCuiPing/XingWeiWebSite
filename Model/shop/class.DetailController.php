<?php
namespace Shop;
class DetailController extends BaseController{
	public function index(){
		global $G,$lang;
		$shopid = intval($_GET['shopid']);
		$shop = shop_get_data(array('shopid'=>$shopid));
		$description = shop_get_desc(array('shopid'=>$shopid));
		$goodslist = goods_get_list(0, 12);
		include template('detail');
	}
}