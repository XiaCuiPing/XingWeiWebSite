<?php
namespace Member;
class FanController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('fan');
	}
}