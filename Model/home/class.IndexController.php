<?php
namespace Home;
class IndexController extends BaseController{
	public function index(){
		global $G,$lang;
		include template('index');
	}
	
	public function lianmeng(){
		global $G,$lang;
		G('title','联盟商家');
		include template('index_lianmeng');
	}
	
	public function market(){
		global $G,$lang;
		G('title','超市');
		include template('index_market');
	}
	
	public function travel(){
		global $G,$lang;
		G('title','酒店旅行');
		include template('index_travel');
	}
	
	public function fan(){
		$this->index();
	}
}