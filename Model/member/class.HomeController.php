<?php
namespace Member;
class HomeController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$wallet = wallet_get_data($this->uid);
		$score = score_get_data($this->uid);
		include template('index');
	}
}