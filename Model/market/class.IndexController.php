<?php
namespace Market;
class IndexController extends BaseController {
	public function index() {
		global $G,$lang;
		
		$categorylist = category_get_tree('goods');
		
		G('title','星微超市');
		include template('index');
	}
	
	public function showhot(){
		include template('hot_sale');
	}
}