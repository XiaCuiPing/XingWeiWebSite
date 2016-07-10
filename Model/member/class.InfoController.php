<?php
namespace Member;
class InfoController extends BaseController{
	public function index(){
		global $G,$lang;
		$member  = member_get_data(array('uid'=>$this->uid));
		$profile = member_get_profile($this->uid);
		include template('info');
	}
}