<?php
namespace Member;
class SafeController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('safe');
	}
}