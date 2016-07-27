<?php
namespace Lianmeng;
class IndexController extends BaseController{
	public function index(){
		global $G,$lang;
		
		G('title','联盟商家');
		include template('index');
	}
}