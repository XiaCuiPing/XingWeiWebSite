<?php
namespace Member;
class FanController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('fan');
	}
	
	public function newticket(){
		global $G,$lang;
		
		$G['title'] = '添加返券';
		include template('fan_newticket');
	}
}