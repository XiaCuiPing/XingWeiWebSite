<?php
namespace Shop;
class DetailController extends BaseController{
	public function index(){
		global $G,$lang;
		$shopid = intval($_GET['shopid']);
		$shop = shop_get_data(array('shopid'=>$shopid));
		
		include template('detail');
	}
}