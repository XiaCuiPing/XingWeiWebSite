<?php
namespace Member;
class OrderController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			
		}else {
			
			include template('order_list');
		}
	}
}