<?php
namespace Admin;
class IndexController extends BaseController{
	public function index(){
		global $G, $lang;
		include template('admin');
	}
}