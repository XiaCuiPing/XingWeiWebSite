<?php
namespace Seller;
class SoldController extends BaseController{
	public function index(){
		global $G,$lang;

		$G['title'] = '已售出的商品';
		include template('sold_list');
	}
}