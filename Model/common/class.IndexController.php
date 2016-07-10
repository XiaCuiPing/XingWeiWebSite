<?php
namespace Common;
class IndexController extends BaseController{
	public function index(){
		
	}
	
	public function captcha(){
		$captcha = new \Core\Captcha();
	}
}