<?php
namespace Member;
class BindController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('bind');
	}
}