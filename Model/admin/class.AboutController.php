<?php
namespace Admin;
class AboutController extends BaseController{
	public function index(){
		global $G,$lang;
		$postlist[1] = post_get_list(0, 10);
		$postlist[2] = post_get_list(array('status'=>-1), 10);
		include template('about');
	}
}