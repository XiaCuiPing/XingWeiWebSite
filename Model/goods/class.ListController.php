<?php
namespace Goods;
class ListController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$G['title'] = '商品列表';
		include template('list');
	}
}