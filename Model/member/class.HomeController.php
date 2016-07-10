<?php
namespace Member;
class HomeController extends BaseController{
	public function index(){
		global $G,$lang;
		include template('index');
	}
}