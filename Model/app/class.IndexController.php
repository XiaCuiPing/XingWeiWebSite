<?php
namespace App;
class IndexController extends BaseController{
	public function index(){
		global $G,$lang;
		$sliderlist = ad_get_list(array('groupid'=>10,3));
		$articlelist = post_get_list(0,4);
		include template('index');
	}
}