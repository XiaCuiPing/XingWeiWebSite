<?php
namespace Post;
class IndexController extends BaseController{
	public function index(){
		global $G,$lang;
		$G['title'] = $lang['home'];
		include template('index');
	}
}