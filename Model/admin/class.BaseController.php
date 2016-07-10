<?php
namespace Admin;
use Core\Controller;
class BaseController extends Controller{
	function __construct(){
		global $G;
		parent::__construct();
		define('IN_ADMIN', true);
		if (!cookie('adminlogined') || !$this->uid || !$this->username){
			if ($G['c'] !== 'login'){
				$this->showlogin();
			}
		}else {
			cookie('adminlogined', 1 ,1800);
		}
	}
	
	/**
	 * 显示管理员登录
	 */
	protected function showlogin(){
		global $G,$lang;
		include template('admin_login');
		exit();
	}
}